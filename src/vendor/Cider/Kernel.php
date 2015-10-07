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
 *  Kernel
 *
 *  Cider kernel class.
 *
 *  @vendor Cider
 *  @package Cider
 *
 *  @version 1.0.0
 *
 *  @author Carbin Creative <hej@carbin.se>
 */
class Kernel {

  /* @uses */
  use Common\Singleton;
  use Common\Registry;
  use Common\Factory;
  use Event\Emitter;

  /**
   *  signal
   *
   *  Alias for {@see Cider\Event\Emitter::emit}.
   *
   *  @param string $eventType
   *  @param mixed $eventTypeCallbackArguments, ...
   *
   *  @return bool
   */
  public function signal(String $eventType, ...$eventTypeCallbackArguments):Bool {

    return $this->emit($eventType, ...$eventTypeCallbackArguments);

  }

}
