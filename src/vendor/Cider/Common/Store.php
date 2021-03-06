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
 *  Store
 *
 *  Data store class.
 *
 *  @vendor Cider
 *  @package Common
 *
 *  @version 1.0.0
 *
 *  @author Carbin Creative <hej@carbin.se>
 */
class Store {

	/* @uses */
	use Mutable;

	/**
	 *  Constructor
	 *
	 *  Invokes {@see \Cider\Common\Store::registerMutators}.
	 *
	 *  @return void
	 */
	public function __construct() {

		$this->registerMutators();

	}

	/**
	 *  registerMutators
	 *
	 *  Registers setters and getters used by store.
	 *
	 *  @return void
	 */
	protected function registerMutators() {

		$this->registerSetter('data', function($dataStore) {

			$this->__store = $dataStore;

		});

		$this->registerGetter('data', function() {

			return $this->__store;

		});

		$this->registerGetter('size', function() {

			return count($this->__store);

		});

		$this->registerGetter('keys', function() {

			return array_keys($this->__store);

		});

		$this->registerGetter('values', function() {

			return array_values($this->__store);

		});

		$this->registerGetter('firstKey', function() {

			$storeKeys = $this->keys;

			return $storeKeys[0];

		});

		$this->registerGetter('lastKey', function() {

			$storeKeys = $this->keys;

			return $storeKeys[count($storeKeys) - 1];

		});

		$this->registerGetter('first', function() {

			return $this->data[$this->firstKey];

		});

		$this->registerGetter('last', function() {

			return $this->data[$this->lastKey];

		});

		$this->registerGetter('serialized', function() {

			return $this->serialize();

		});

		$this->registerGetter('parameterized', function() {

			return $this->parameterize();

		});

	}

	/**
	 *  has
	 *
	 *  Checks if store key exists.
	 *
	 *  @param string $storeKey
	 *
	 *  @return bool
	 */
	public function has(String $storeKey):Bool {

		return array_key_exists($storeKey, $this->data);

	}

	/**
	 *  includes
	 *
	 *  Checks if store value exists.
	 *
	 *  @param mixed $mixedValue
	 *
	 *  @return bool
	 */
	public function includes($mixedValue):Bool {

		return in_array($mixedValue, $this->values);

	}

	/**
	 *  keyOf
	 *
	 *  Returns key of store value.
	 *
	 *  @param mixed $mixedValue
	 *
	 *  @return string
	 */
	public function keyOf($mixedValue):String {

		return array_search($mixedValue, $this->data);

	}

	/**
	 *  is
	 *
	 *  Checks if stored value matches input value.
	 *
	 *  @param string $storeKey
	 *  @param mixed $mixedValue
	 */
	public function is(String $storeKey, $mixedValue):Bool {

		return $this->get($storeKey) === $mixedValue;

	}

	/**
	 *  grab
	 *
	 *  Removes and returns store key value.
	 *
	 *  @param string $storeKey
	 *
	 *  @return mixed
	 */
	public function grab(String $storeKey) {

		$mixedValue = $this->get($storeKey);

		$this->remove($storeKey);

		return $mixedValue;

	}

	/**
	 *  remove
	 *
	 *  Removes value from store.
	 *
	 *  @param string $storeKey
	 *
	 *  @return void
	 */
	public function remove(String $storeKey) {

		unset($this->__store[$storeKey]);

	}

	/**
	 *  replace
	 *
	 *  Replaces current store with new store.
	 *
	 *  @param array $newStore
	 *
	 *  @return void
	 */
	public function replace(Array $newStore) {

		$this->__store = $newStore;

	}

	/**
	 *  merge
	 *
	 *  Replaces current store with new store.
	 *
	 *  @param array|Store $stores, ...
	 *
	 *  @return void
	 */
	public function merge(...$stores) {

		$newStore = $this->data;

		foreach($stores as $store) {

			if(is_array($store) === true) {

				$newStore = $newStore + $store;

			} else if($store instanceof Store) {

				$newStore = $newStore + $store->data;

			}

		}

		$this->data = $newStore;

	}

	/**
	 *  serialize
	 *
	 *  Serializes store into a JSON string.
	 *
	 *  @param array $newStore
	 *
	 *  @return void
	 */
	public function serialize():String {

		return json_encode($this->data);

	}

	/**
	 *  parameterize
	 *
	 *  Parameterizes store into a query string.
	 *
	 *  @param array $newStore
	 *
	 *  @return void
	 */
	public function parameterize():String {

		return http_build_query($this->data);

	}

}
