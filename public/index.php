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
define('CIDER_ROOT_PATH', implode(DIRECTORY_SEPARATOR, [__DIR__, '..']) . DIRECTORY_SEPARATOR);

/* Set error handling */
error_reporting(E_ALL ^ E_NOTICE);
ini_set('display_erros', 'On');

require_once CIDER_ROOT_PATH . implode(DIRECTORY_SEPARATOR, ['src', 'vendor', 'Cider', 'Common', 'Singleton.php']);

class Greeter {

  use Common\Singleton;

  public function greet(String $firstName):String {

    return "Hello {$firstName}!";

  }

}

echo Greeter::getInstance()->greet('Robin');
