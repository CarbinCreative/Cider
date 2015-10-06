<?php
/**
 *  Cider
 *
 *  Cider is a PHP based object oriented nano-framework for building small web applications.
 *
 *  @author Carbin Creative <hej@carbin.se>
 *  @license http://opensource.org/licenses/MIT MIT
 */

/* @namespace Exceptions */
namespace Cider\Exceptions;

/* Deny direct file access */
if(!defined('CIDER_ROOT_PATH')) exit;

/**
 *  BadMethodCallException
 *
 *  Bad method call exception.
 *
 *  @vendor Cider
 *  @package Exceptions
 *
 *  @version 1.0.0
 *
 *  @author Carbin Creative <hej@carbin.se>
 */
class BadMethodCallException extends FrameworkException {}
