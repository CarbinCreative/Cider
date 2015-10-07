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
