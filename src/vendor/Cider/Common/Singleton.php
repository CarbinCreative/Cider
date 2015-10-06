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
 *  Singleton
 *
 *  Singleton class pattern trait.
 *
 *  @vendor Cider
 *  @package Common
 *
 *  @version 1.0.0
 *
 *  @author Carbin Creative <hej@carbin.se>
 */
trait Singleton {

  /**
   *  @staticvar self $instance
   */
  protected static $instance;

  /**
   *  getInstance
   *
   *  Returns singleton instance.
   *
   *  @return self
   */
  final public static function getInstance():self {

    return static::$instance ?: new static;

  }

  /**
   *  Constructor
   *
   *  Calls {@see \Cider\Common\Singleton::init}. Initialization disabled via 'final' and 'private' keywords.
   *
   *  @return void
   */
  final private function __construct() {

    $this->init();

  }

  /**
   *  init
   *
   *  Overridable noop method, called in {@see \Cider\Common\Singleton::__construct}.
   *
   *  @return void
   */
  protected function init() {}

  /**
   *  __wakeup
   *
   *  Class unserialization disabled via 'final' and 'private' keywords.
   *
   *  @return void
   */
  final private function __wakeup() {}

  /**
   *  __clone
   *
   *  Class cloning disabled via 'final' and 'private' keywords.
   *
   *  @return void
   */
  final private function __clone() {}

}
