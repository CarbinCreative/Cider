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
 *  Cors
 *
 *  CORS middleware.
 *
 *  @vendor Cider
 *  @package Delegation
 *	@subpackage Middleware
 *
 *  @version 1.0.0
 *
 *  @author Carbin Creative <hej@carbin.se>
 */
class Cors extends Middleware {

	/**
	 *	@var string $allowOrigin
	 */
	protected $allowOrigin = '*';

	/**
	 *	@var array $allowMethods
	 */
	protected $allowMethods = [
		'GET',
		'POST'
	];

	/**
	 *	@var array $allowHeaders
	 */
	protected $allowHeaders = [
		'Accept',
		'Accept-Language',
		'Content-Language',
		'Content-Type'
	];

	/**
	 *	@var bool $allowCredentials
	 */
	protected $allowCredentials = false;

	/**
	 *	@var int $accessControlMaxAge
	 */
	protected $accessControlMaxAge = 86400;

	/**
	 *	setAllowOrigin
	 *
	 *	Sets Access-Control-Allow-Origin header.
	 *
	 *	@param string $allowOrigin
	 *
		*	@return void
	 */
	public function setAllowOrigin(String $allowOrigin) {

		$this->allowOrigin = $allowOrigin;

	}

	/**
	 *	getAllowOrigin
	 *
	 *	Returns Access-Control-Allow-Origin header.
	 *
		*	@return string
	 */
	public function getAllowOrigin():String {

		return $this->allowOrigin ?? '*';

	}

	/**
	 *	setAllowMethods
	 *
	 *	Sets Access-Control-Allow-Methods header.
	 *
	 *	@param array $allowMethods
	 *
		*	@return void
	 */
	public function setAllowMethods(Array $allowMethods) {

		$this->allowMethods = $allowMethods;

	}

	/**
	 *	getAllowMethods
	 *
	 *	Returns Access-Control-Allow-Methods headers.
	 *
		*	@return string
	 */
	public function getAllowMethods():String {

		return implode(', ', array_unique(
			array_merge(
				['OPTIONS'],
				$this->allowMethods
			)
		));

	}

	/**
	 *	setAllowHeaders
	 *
	 *	Sets Access-Control-Allow-Headers header.
	 *
	 *	@param array $allowHeaders
	 *
		*	@return void
	 */
	public function setAllowHeaders(Array $allowHeaders) {

		$this->allowHeaders = $allowHeaders;

	}

	/**
	 *	getAllowHeaders
	 *
	 *	Returns Access-Control-Allow-Headers header.
	 *
		*	@return string
	 */
	public function getAllowHeaders():String {

		return implode(', ', $this->allowHeaders);

	}

	/**
	 *	allowCredentials
	 *
	 *	Sets Access-Control-Allow-Credentials to true.
	 *
		*	@return void
	 */
	public function allowCredentials() {

		$this->allowCredentials = true;

	}

	/**
	 *	disallowCredentials
	 *
	 *	Sets Access-Control-Allow-Credentials to false.
	 *
		*	@return void
	 */
	public function disallowCredentials() {

		$this->allowCredentials = false;

	}

	/**
	 *	getAllowCredentials
	 *
		*	Returns Access-Control-Allow-Credentials header.
	 *
	 *	@return string
	 */
	public function getAllowCredentials():String {

		return ($this->allowCredentials === true) ? 'true' : 'false';

	}

	/**
	 *	setAccessControlMaxAge
	 *
		*	Sets Access-Control-Max-Age header.
	 *
	 *	@param int $accessControlMaxAge
	 *
	 *	@return void
	 */
	public function setAccessControlMaxAge(Int $accessControlMaxAge) {

		$this->accessControlMaxAge = $accessControlMaxAge;

	}

	/**
	 *	getAccessControlMaxAge
	 *
		*	Returns Access-Control-Max-Age header.
	 *
	 *	@return string
	 */
	public function getAccessControlMaxAge():String {

		return "{$this->accessControlMaxAge}";

	}

	/**
	 *	invoke
	 *
	 *	Sends CORS headers.
	 *
	 *	@param mixed $request
	 *	@param string $response
	 *
	 *	@return array
	 */
	public function invoke($request, String $response):Array {

		$request->setHeader('Access-Control-Max-Age', $this->getAccessControlMaxAge());
		$request->setHeader('Access-Control-Allow-Origin', $this->getAllowOrigin());
		$request->setHeader('Access-Control-Allow-Methods', $this->getAllowMethods());
		$request->setHeader('Access-Control-Allow-Headers', $this->getAllowHeaders());
		$request->setHeader('Access-Control-Allow-Credentials', $this->getAllowCredentials());

		return [$request, $response];
	}

}
