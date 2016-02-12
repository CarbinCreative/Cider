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
namespace Cider\Delegation\Exceptions;

/* Deny direct file access */
if(!defined('CIDER_ROOT_PATH')) exit;

/* @imports */
use Cider\Exceptions\FrameworkException;

/**
 *  RouteException
 *
 *  Generic route exception.
 *
 *  @vendor Cider
 *  @package Delegation
 *  @subpackage Exceptions
 *
 *  @version 1.0.0
 *
 *  @author Carbin Creative <hej@carbin.se>
 */
class RouteException extends FrameworkException {}
