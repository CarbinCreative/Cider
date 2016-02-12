<?php
/**
 *  Cider
 *
 *  Cider is a PHP based object oriented nano-framework for building small web applications.
 *
 *  @author Carbin Creative <hej@carbin.se>
 *  @license http://opensource.org/licenses/MIT MIT
 */

/* @namespace Middleware */
namespace Cider\Delegation\Middleware;

/* Deny direct file access */
if(!defined('CIDER_ROOT_PATH')) exit;

/**
 *  Middleware
 *
 *  Route middleware abstract.
 *
 *  @vendor Cider
 *  @package Delegation
 *	@subpackage Middleware
 *
 *  @version 1.0.0
 *
 *  @author Carbin Creative <hej@carbin.se>
 */
abstract class Middleware {

	/**
	 *	invoke
	 *
	 *	Executes middleware, must always return array [$request, String $string].
	 *
	 *	@param mixed $request
	 *	@param string $response
	 *
	 *	@return array
	 */
	abstract public function invoke($request, String $response):Array;

}
