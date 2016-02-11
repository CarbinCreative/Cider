<?php
/**
 *  Cider
 *
 *  Cider is a PHP based object oriented nano-framework for building small web applications.
 *
 *  @author Carbin Creative <hej@carbin.se>
 *  @license http://opensource.org/licenses/MIT MIT
 */

/* @namespace Common */
namespace Cider\Common;

/* Deny direct file access */
if(!defined('CIDER_ROOT_PATH')) exit;

/* @imports */
use ReflectionClass;
use \Cider\Exceptions\BadMethodCallException;

/**
 *  Factory
 *
 *  Factory class pattern trait.
 *
 *  @vendor Cider
 *  @package Common
 *
 *  @version 1.0.0
 *
 *  @author Carbin Creative <hej@carbin.se>
 */
trait Factory {

	/**
	 *  initialize
	 *
	 *  Creates a new reflection instance of input class.
	 *
	 *  @param string $className
	 *  @param string $instanceName
	 *  @param string $classMethodName
	 *  @param mixed $classMethodArguments, ...
	 *
	 *  @throws \Cider\Exceptions\BadMethodCallException
	 *
	 *  @return instance
	 */
	public function initialize(String $className, String $instanceName, String $classMethodName = null, ...$classMethodArguments) {

		if($this->get($instanceName) !== null) {

			throw new BadMethodCallException('Instance already initialized.');

		}

		$classInstance = call_user_func_array(
			[
				new ReflectionClass($className),
				$classMethodName ?? 'newInstance'
			],
			$classMethodArguments
		);

		$this->set($instanceName, $classInstance);

		return $classInstance;

	}

}
