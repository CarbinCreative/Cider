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
use Closure;

/**
 *  Mutable
 *
 *  Mutable object trait, used to register custom setters and getters.
 *
 *  @vendor Cider
 *  @package Common
 *
 *  @version 1.0.0
 *
 *  @author Carbin Creative <hej@carbin.se>
 */
trait Mutable {

  /**
   *  @var array $__store
   */
  protected $__store = [];

  /**
   *  @var array $setterCallbacks
   */
  protected $setterCallbacks = [];

  /**
   *  @var array $getterCallbacks
   */
  protected $getterCallbacks = [];

  /**
   *  hasSetter
   *
   *  Validates whether or not a property has a setter callback.
   *
   *  @param string $storeKey
   *
   *  @return bool
   */
  public function hasSetter(String $storeKey):Bool {

    return array_key_exists($storeKey, $this->setterCallbacks);

  }

  /**
   *  registerSetter
   *
   *  Registerers a setter callback.
   *
   *  @param string $storeKey
   *  @param callable $setterCallback
   *
   *  @return void
   */
  public function registerSetter(String $storeKey, Callable $setterCallback) {

    $this->setterCallbacks[$storeKey] = $setterCallback;

  }

  /**
   *  unregisterSetter
   *
   *  Removes a registered setter callback.
   *
   *  @param string $storeKey
   *
   *  @return void
   */
  public function unregisterSetter(String $storeKey) {

    if($this->hasSetter($storeKey) === true) {

      unset($this->setterCallbacks[$storeKey]);

    }

  }

  /**
   *  set
   *
   *  Sets store value using setter if present, otherwise sets value by mutating data store.
   *
   *  @param string $storeKey
   *  @param mixed $mixedValue
   *
   *  @return void
   */
  public function set(String $storeKey, $mixedValue) {

    if($storeKey === '__store') {

      $this->__store = $mixedValue;

    } else if($this->hasSetter($storeKey) === true) {

      $setterCallback = $this->setterCallbacks[$storeKey];

      $boundSetterCallback = Closure::bind($setterCallback, $this);

      $boundSetterCallback($mixedValue);

    } else {

      $this->__store[$storeKey] = $mixedValue;

    }

  }

  /**
   *  __set
   *
   *  Invokes {@see \Cider\Common\Mutable::set}.
   *
   *  @param string $storeKey
   *  @param mixed $mixedValue
   *
   *  @return void
   */
  public function __set(String $storeKey, $mixedValue) {

    $this->set($storeKey, $mixedValue);

  }

  /**
   *  hasGetter
   *
   *  Validates whether or not a property has a getter callback.
   *
   *  @param string $storeKey
   *
   *  @return bool
   */
  public function hasGetter(String $storeKey):Bool {

    return array_key_exists($storeKey, $this->getterCallbacks);

  }

  /**
   *  registerGetter
   *
   *  Registerers a getter callback.
   *
   *  @param string $storeKey
   *  @param callable $getterCallback
   *
   *  @return void
   */
  public function registerGetter(String $storeKey, Callable $getterCallback) {

    $this->getterCallbacks[$storeKey] = $getterCallback;

  }

  /**
   *  unregisterGetter
   *
   *  Removes a registered setter callback.
   *
   *  @param string $storeKey
   *
   *  @return void
   */
  public function unregisterGetter(String $storeKey) {

    if($this->hasGetter($storeKey) === true) {

      unset($this->getterCallbacks[$storeKey]);

    }

  }

  /**
   *  set
   *
   *  Gets store value using getter if present, otherwise returns value from data store.
   *
   *  @param string $storeKey
   *
   *  @return void
   */
  public function get(String $storeKey) {

    if($storeKey === '__store') {

      return $this->__store;

    } else if($this->hasGetter($storeKey) === true) {

      $getterCallback = $this->getterCallbacks[$storeKey];

      $boundGetterCallback = Closure::bind($getterCallback, $this);

      return $boundGetterCallback($storeKey);

    }

    return $this->__store[$storeKey] ?? null;

  }

  /**
   *  __set
   *
   *  Invokes {@see \Cider\Common\Mutable::get}.
   *
   *  @param string $storeKey
   *
   *  @return mixed
   */
  public function __get(String $storeKey) {

    return $this->get($storeKey, $mixedValue);

  }

}
