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

/**
 *  @const string NAMESPACE_SEPARATOR
 */
define('NAMESPACE_SEPARATOR', '\\');

/**
 *  @const string CIDER_ROOT_PATH
 */
define('CIDER_ROOT_PATH', __DIR__ . DIRECTORY_SEPARATOR);

/* Set error handling */
error_reporting(E_ALL ^ E_NOTICE);
ini_set('display_erros', 'On');

/* Output buffer */
$outputBuffer = null;

/* Run the application */
try {

  /* Load version file */
  require_once CIDER_ROOT_PATH . 'version.php';

  /* Load Cider bootstrap */
  require_once implode(DIRECTORY_SEPARATOR, [
    rtrim(CIDER_ROOT_PATH, DIRECTORY_SEPARATOR),
    'src', 'vendor', 'Cider', 'bootstrap.php'
  ]);

  ob_start();

  /* Load optional app bootstrap */
  relativeRequire('app/bootstrap');

  /* Load optional route definitions */
  relativeRequire('app/routes');

  $outputBuffer = ob_get_clean();

  echo 'Cider ' . CIDER_VERSION;

} catch (FrameworkException $exception) {

  $outputBuffer = $exception;

} finally {

  /* Send output */
  echo $outputBuffer;

  /* @emits "rendered" */
  app()->signal('rendered');

}
