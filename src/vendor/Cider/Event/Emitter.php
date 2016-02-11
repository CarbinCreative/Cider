<?php
/**
 *  Cider
 *
 *  Cider is a PHP based object oriented nano-framework for building small web applications.
 *
 *  @author Carbin Creative <hej@carbin.se>
 *  @license http://opensource.org/licenses/MIT MIT
 */

/* @namespace Event */
namespace Cider\Event;

/* Deny direct file access */
if(!defined('CIDER_ROOT_PATH')) exit;

/* @imports */
use Cider\Exceptions\OverflowException;

/**
 *  Emitter
 *
 *  A light weight event emitter trait.
 *
 *  @vendor Cider
 *  @package Event
 *
 *  @version 1.0.0
 *
 *  @author Carbin Creative <hej@carbin.se>
 */
trait Emitter {

	/**
	 *  @var int $maxEventListeners
	 */
	protected $maxEventListeners = 10;

	/**
	 *  @var array $eventListeners
	 */
	protected $eventListeners = [];

	/**
	 *  setMaxEventListeners
	 *
	 *  Sets max event listeners to each event type to current context.
	 *
	 *  @param int $maxEventListeners
	 *
	 *  @return void
	 */
	public function setMaxEventListeners(Int $maxEventListeners) {

		$this->maxEventListeners = $maxEventListeners;

	}

	/**
	 *  getMaxEventListeners
	 *
	 *  Return max event listeners to current context.
	 *
	 *  @return void
	 */
	public function getMaxEventListeners():Int {

		return $this->maxEventListeners;

	}

	/**
	 *  getAllEventListeners
	 *
	 *  Returns all event listeners.
	 *
	 *  @return array
	 */
	public function getAllEventListeners():Array {

		return $this->eventListeners;

	}

	/**
	 *  getEventListeners
	 *
	 *  Returns all event listeners registered to event type.
	 *
	 *  @return array
	 */
	public function getEventListeners(String $eventType):Array {

		if(array_key_exists($eventType, $this->eventListeners) === false) {

			$this->eventListeners[$eventType] = [];

		}

		return $this->eventListeners[$eventType];

	}

	/**
	 *  removeAllEventListeners
	 *
	 *  Removes all event listeners.
	 *
	 *  @param string $eventType
	 *
	 *  @return array
	 */
	public function removeAllEventListeners(String $eventType = null):self {

		if($eventType !== null && array_key_exists($eventType, $this->eventListeners) === true) {

			$this->eventListeners[$eventType] = [];

			return $this;

		}

		$this->eventListeners = [];

		return $this;

	}

	/**
	 *  addEventListener
	 *
	 *  Adds event listener. Throws exception if {@see Emitter::$maxEventListeners} is exceeded.
	 *
	 *  @param string $eventType
	 *  @param callable $eventTypeCallback
	 *
	 *  @throws \Cider\Exceptions\OverflowException
	 *
	 *  @return self
	 */
	public function addEventListener(String $eventType, Callable $eventTypeCallback):self {

		if(array_key_exists($eventType, $this->eventListeners) === false) {

			$this->eventListeners[$eventType] = [];

		}

		$this->eventListeners[$eventType][] = $eventTypeCallback;

		if(count($this->eventListeners[$eventType]) > $this->maxEventListeners) {

			throw new OverflowException('Maximum event listeners exceeded.');

		}

		return $this;

	}

	/**
	 *  removeEventListener
	 *
	 *  Removes registered event listener if event callback exists.
	 *
	 *  @param string $eventType
	 *  @param callable $eventTypeCallback
	 *
	 *  @return self
	 */
	public function removeEventListener(String $eventType, Callable $eventTypeCallback):self {

		if(empty($this->eventListeners) === true) return $this;

		if(array_key_exists($eventType, $this->eventListeners) === false) return $this;

		$eventTypeCallbackIndex = array_search($eventTypeCallback, $this->eventListeners[$eventType]);

		if($eventTypeCallbackIndex === false) return $this;

		array_splice($this->eventListeners[$eventType], $eventTypeCallbackIndex, 1);

		return $this;

	}

	/**
	 *  addOnceEventListener
	 *
	 *  Adds an event listener that will remove itself after it has been invoked.
	 *
	 *  @param string $eventType
	 *  @param callable $eventTypeCallback
	 *
	 *  @return self
	 */
	public function addOnceEventListener(String $eventType, Callable $eventTypeCallback):self {

		$onceCallback = function() use ($eventType, $eventTypeCallback, &$onceCallback) {

			$this->removeEventListener($eventType, $onceCallback);
			call_user_func_array($eventTypeCallback, func_get_args());

		};

		$this->addEventListener($eventType, $onceCallback);

		return $this;

	}

	/**
	 *  emit
	 *
	 *  Invokes all event type listeners.
	 *
	 *  @param string $eventType
	 *  @param mixed $eventTypeCallbackArguments, ...
	 *
	 *  @return bool
	 */
	protected function emit(String $eventType, ...$eventTypeCallbackArguments):Bool {

		if(empty($this->eventListeners) === true) return false;

		if(array_key_exists($eventType, $this->eventListeners) === false) return false;

		$eventTypeCallbacks = $this->eventListeners[$eventType];

		foreach($eventTypeCallbacks as $eventTypeCallback) {

			call_user_func_array($eventTypeCallback, $eventTypeCallbackArguments);

		}

		return true;

	}

	/**
	 *  on
	 *
	 *  Alias method for {@see \Cider\Event\Emitter::addEventListener}.
	 *
	 *  @return self
	 */
	public function on():self {

		return call_user_func_array([$this, 'addEventListener'], func_get_args());

	}

	/**
	 *  off
	 *
	 *  Alias method for {@see \Cider\Event\Emitter::removeEventListener}.
	 *
	 *  @return self
	 */
	public function off():self {

		return call_user_func_array([$this, 'removeEventListener'], func_get_args());

	}

	/**
	 *  once
	 *
	 *  Alias method for {@see \Cider\Event\Emitter::addOnceEventListener}.
	 *
	 *  @return self
	 */
	public function once():self {

		return call_user_func_array([$this, 'addOnceEventListener'], func_get_args());

	}

}
