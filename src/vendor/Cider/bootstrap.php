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

/* Load required functions */
require_once implode(DIRECTORY_SEPARATOR, [__DIR__, 'functions.php']);
require_once implode(DIRECTORY_SEPARATOR, [__DIR__, 'helpers.php']);

/* Register autoloader */
spl_autoload_register(function($className) {

  return import($className);

});

/* Initialize default libraries */
$httpClient = app()->initialize('Cider\Http\Client', 'http');
$routeMap = app()->initialize('Cider\Delegation\RouteMap', 'routeMap');
app()->initialize('Cider\Delegation\Dispatcher', 'dispatcher', null, $httpClient, $routeMap);
