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

/* @imports */
use \Cider\Delegation\RouteMap;
use \Cider\Delegation\Dispatcher;

/**
 *  app
 *
 *  Returns {@see Cider\Kernel} instance.
 *
 *  @return \Cider\Kernel
 */
function app():Kernel {

  return Kernel::getInstance();

}

/**
 *  route
 *
 *  Returns {@see Cider\Delegation\RouteMap} instance.
 *
 *  @return Cider\Delegation\RouteMap
 */
function route():RouteMap {

  return Kernel::getInstance()->routeMap;

}

/**
 *  dispatcher
 *
 *  Returns {@see Cider\Delegation\Dispatcher} instance.
 *
 *  @return Cider\Delegation\Dispatcher
 */
function dispatcher():Dispatcher {

  return Kernel::getInstance()->dispatcher;

}
