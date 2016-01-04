<?php
/**
 *  Cider
 *
 *  Cider is a PHP based object oriented nano-framework for building small web applications.
 *
 *  @author Carbin Creative <hej@carbin.se>
 *  @license http://opensource.org/licenses/MIT MIT
 */

/* @namespace Cider */
namespace Cider;

/* Deny direct file access */
if(!defined('CIDER_ROOT_PATH')) exit;

/* Import required files */
require_once implode(DIRECTORY_SEPARATOR, [rtrim(CIDER_ROOT_PATH, DIRECTORY_SEPARATOR), 'src', 'vendor', 'Cider', 'Exceptions', 'FrameworkException.php']);
require_once implode(DIRECTORY_SEPARATOR, [rtrim(CIDER_ROOT_PATH, DIRECTORY_SEPARATOR), 'src', 'vendor', 'Cider', 'Exceptions', 'FileNotFoundException.php']);
require_once implode(DIRECTORY_SEPARATOR, [rtrim(CIDER_ROOT_PATH, DIRECTORY_SEPARATOR), 'src', 'vendor', 'Cider', 'Exceptions', 'BadMethodCallException.php']);
require_once implode(DIRECTORY_SEPARATOR, [rtrim(CIDER_ROOT_PATH, DIRECTORY_SEPARATOR), 'src', 'vendor', 'Cider', 'Spec', 'Expectations.php']);
require_once implode(DIRECTORY_SEPARATOR, [rtrim(CIDER_ROOT_PATH, DIRECTORY_SEPARATOR), 'src', 'vendor', 'Cider', 'Spec', 'Expects.php']);
require_once implode(DIRECTORY_SEPARATOR, [rtrim(CIDER_ROOT_PATH, DIRECTORY_SEPARATOR), 'src', 'vendor', 'Cider', 'Spec', 'ExpectsAll.php']);

/* @imports */
use RegexIterator;
use RecursiveRegexIterator;
use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;
use \Cider\Spec\Expects;
use \Cider\Spec\ExpectsAll;
use \Cider\Exceptions\FileNotFoundException;

/**
 *  path
 *
 *  Normalizes path name from Cider root.
 *
 *  @param string $unresolvedPath
 *  @param bool $appendDirectorySeparator
 *  @param string $pathSeparator
 *
 *  @return string
 */
function path(String $unresolvedPath, Bool $appendDirectorySeparator = false, String $pathSeparator = '/'):String {

  $resolvedPath = preg_replace('#' . $pathSeparator . '+#', $pathSeparator, trim($unresolvedPath, $pathSeparator));

  $resolvedPath = implode('', [
    CIDER_ROOT_PATH,
    trim(str_replace($pathSeparator, DIRECTORY_SEPARATOR, trim($resolvedPath, $pathSeparator)), DIRECTORY_SEPARATOR)
  ]);

  if($appendDirectorySeparator === true) {

    $resolvedPath .= DIRECTORY_SEPARATOR;

  }

  return $resolvedPath;

}

/**
 *  relativeRequire
 *
 *  Attempts to require a file relative to Cider root path.
 *
 *  @param string $relativeRequirePath
 *  @param bool $throwException
 *
 *  @throws \Cider\Exceptions\FileNotFoundException
 *
 *  @return bool
 */
function relativeRequire(String $relativeRequirePath, Bool $throwException = false):Bool {

  $resolvedRequirePath = path($relativeRequirePath);

  if(substr($resolvedRequirePath, -4) !== '.php') {

    $resolvedRequirePath .= '.php';

  }

  if(file_exists($resolvedRequirePath) === true) {

    require_once $resolvedRequirePath;

  } else if($throwException === true) {

    throw new FileNotFoundException("File {$resolvedRequirePath} does not exist.");

  }

  return false;

}

/**
 *  import
 *
 *  Requires vendor package based on namespace path using {@see Cider\relativeRequire}, and returns number of files imported.
 *
 *  @param string $unresolvedNamespace
 *
 *  @return int
 */
function import(String $unresolvedNamespace, String $includePathPrefix = null):Int {

  $includePathPrefix = $includePathPrefix ?? 'src/vendor/';
  $namespaceDirectoryPath = str_replace(NAMESPACE_SEPARATOR, DIRECTORY_SEPARATOR, $unresolvedNamespace);
  $resolvedNamespaceIncludePath = implode([$includePathPrefix, $namespaceDirectoryPath]);

  if(substr($unresolvedNamespace, -1) === '*') {

    $regexDirectoryIterator = new RegexIterator(
      new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator(substr($resolvedNamespaceIncludePath, 0, -1))
      ),
      '/^.+[A-Z][a-z]+\.php$/',
      RecursiveRegexIterator::GET_MATCH
    );

    $includePaths = array_keys(iterator_to_array($regexDirectoryIterator));

  } else {

    $includePaths = [$resolvedNamespaceIncludePath];

  }

  foreach($includePaths as $includePath) {

    relativeRequire($includePath, true);

  }

  return count($includePaths);

}

/**
 *  url
 *
 *  Returns full request URL.
 *
 *  @param bool $omitRequestPath
 *
 *  @return string
 */
function url(Bool $omitRequestPath = false):String {

  $sslSuffix = (empty($_SERVER['HTTPS']) === true) ? '' : (strtolower($_SERVER['HTTPS']) === 'on') ? 's' : '';

  $protocol = strtolower($_SERVER['SERVER_PROTOCOL']);
  $protocol = substr($protocol, 0, strpos($protocol, '/')) . $sslSuffix;

  $port = (intval($_SERVER['SERVER_PORT']) === 80) ? '' : (':' . $_SERVER['SERVER_PORT']);

  $resolvedUrl = implode('', [
    $protocol,
    '://',
    $_SERVER['SERVER_NAME'],
    $port,
    $_SERVER['REQUEST_URI']
  ]);

  if($omitRequestPath === true) {

    $resolvedUrl = implode('', [
      $protocol,
      '://',
      $_SERVER['SERVER_NAME'],
      $port,
      dirname($_SERVER['SCRIPT_NAME'])
    ]);

  }

  return $resolvedUrl;

}

/**
 *  uri
 *
 *  Resolves URI if provided, otherwise returns resolved request URI.
 *
 *  @param string $unresolvedUri
 *
 *  @return string
 */
function uri(String $unresolvedUri = null):String {

  if($unresolvedUri === null) {

    $requestUri = explode('/', trim($_SERVER['REQUEST_URI'], '/'));
    $scriptName = explode('/', trim($_SERVER['SCRIPT_NAME'], '/'));

    $segments = array_diff_assoc($requestUri, $scriptName);
    $segments = array_filter($segments);

    if(empty($segments) === true) {

      return '/';

    }

    $uriPath = implode('/', $segments);
    $uriPath = parse_url($uriPath, PHP_URL_PATH);

  return $uriPath;

  }

  return preg_replace('#/+#', '/', trim(slugify($unresolvedUri), '/'));

}

/**
 *  slugify
 *
 *  Returns a slug from unresolved string.
 *
 *  @param string $unresolvedString
 *  @param string $wordDelimiter
 *  @param array $wordReplacements
 *
 *  @return string
 */
function slugify(String $unresolvedString, String $wordDelimiter = '-', Array $wordReplacements = []):String {

  if(count($wordReplacements) > 0) {

    $unresolvedString = str_ireplace($wordReplacements, ' ', $unresolvedString);

  }

  $resolvedString = iconv('UTF-8', 'ASCII//TRANSLIT', $unresolvedString);
  $resolvedString = preg_replace("%[^-/+|\w ]%", '', $resolvedString);
  $resolvedString = strtolower(trim($resolvedString, '-'));
  $resolvedString = preg_replace("/[\/_|+ -]+/", $wordDelimiter, $resolvedString);

  return $resolvedString;

}

/**
 *  expect
 *
 *  Creates a new expectation instance and returns it.
 *
 *  @param mixed $actual
 *
 *  @return \Cider\Spec\Expects
 */
function expect($actual = null):Expects {

  return new Expects($actual);

}
