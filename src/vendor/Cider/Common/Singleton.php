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
   *  @staticvar self $__instance
   */
  protected static $__instance;

  /**
   *  getInstance
   *
   *  Returns self initialized instance.
   *
   *  @return self
   */
  public static function getInstance():self {

    if(is_object(self::$__instance) === false) {

      self::$__instance = new self;

    }

    return self::$__instance;

  }

  /**
   *  Constructor
   *
   *  Initialization disabled via 'final' and 'private' keywords.
   *
   *  @return void
   */
  final private function __construct() {}

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
