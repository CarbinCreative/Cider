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

/**
 *  Registry
 *
 *  Registry class pattern trait.
 *
 *  @vendor Cider
 *  @package Common
 *
 *  @version 1.0.0
 *
 *  @author Carbin Creative <hej@carbin.se>
 */
trait Registry {

  /**
   *  @var array $__store
   */
  protected $__store = [];

  /**
   *  __isset
   *
   *  Validates if key exists in registry store.
   *
   *  @param string $key
   *
   *  @return bool
   */
  public function __isset(String $key):Bool {

    return array_key_exists($key, $this->__store);

  }

  /**
   *  __unset
   *
   *  Removes key item from registry store.
   *
   *  @param string $key
   *
   *  @return void
   */
  public function __unset(String $key) {

    if(array_key_exists($key, $this->__store) === true) {

      unset($this->__store[$key]);

    }

  }

  /**
   *  __set
   *
   *  Sets registry key.
   *
   *  @param string $key
   *  @param mixed $value
   *
   *  @return void
   */
  public function __set(String $key, $value) {

    $this->__store[$key] = $value;

  }

  /**
   *  __get
   *
   *  Returns key value from registry store, if it exists.
   *
   *  @param string $key
   *
   *  @return mixed
   */
  public function __get(String $key) {

    if(array_key_exists($key, $this->__store) === true) {

      return $this->__store[$key];

    }

    return null;

  }

  /**
   *  size
   *
   *  Returns size of {@see \Cider\Common\Registry::$__store}.
   *
   *  @return int
   */
  public function size():Int {

    return count($this->__store);

  }

}
