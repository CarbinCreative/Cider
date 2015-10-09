<?php
/**
 *  Cider
 *
 *  Cider is a PHP based object oriented nano-framework for building small web applications.
 *
 *  @author Carbin Creative <hej@carbin.se>
 *  @license http://opensource.org/licenses/MIT MIT
 */

/* @namespace Spec */
namespace Cider\Spec;

/* Deny direct file access */
if(!defined('CIDER_ROOT_PATH')) exit;

/* @imports */
use \Cider\Exceptions\BadMethodCallException;

/**
 *  Expectation
 *
 *  Light weight axpectation assertion library.
 *
 *  @vendor Cider
 *  @package Spec
 *
 *  @version 1.0.0
 *
 *  @author Carbin Creative <hej@carbin.se>
 */
class Expectation {

  /**
   *  @var mixed $actual
   */
  protected $actual;

  /**
   *  Constructor
   *
   *  Sets {@see \Cider\Spec\Assertion::$actual}.
   *
   *  @return void
   */
  public function __construct($actual) {

    $this->actual = $actual;

  }

  /**
   *  expect
   *
   *  Sets and returns new instance of {@see \Cider\Spec\Expectation}.
   *
   *  @param mixed $actual
   *
   *  @return \Cider\Spec\Expectation
   */
  protected function expect($actual):self {

    $newInstance = new self($actual);

    return $newInstance;

  }

  /**
   *  toEqual
   *
   *  Expect actual to be exact match to expected.
   *
   *  @param mixed $obj
   *
   *  @return bool
   */
  public function toEqual($expected):Bool {

    return $this->actual === $expected;

  }

  /**
   *  notToEqual
   *
   *  Expect actual not to match expected.
   *
   *  @param mixed $obj
   *
   *  @return bool
   */
  public function notToEqual($expected):Bool {

    return $this->toEqual($expected) === false;

  }

  /**
   *  toEqualAny
   *
   *  Expect actual to match at least one expectation.
   *
   *  @param mixed $expectations, ...
   *
   *  @return bool
   */
  public function toEqualAny(...$expectations):Bool {

    foreach($expectations as $expected) {

      if($this->toEqual($expected) === true) {

        return true;

      }

    }

    return false;

  }

  /**
   *  notToEqualAny
   *
   *  Expect actual not to match any expectation.
   *
   *  @param mixed $expectations, ...
   *
   *  @return bool
   */
  public function notToEqualAny(...$expectations):Bool {

    return $this->toEqualAny(...$expectations) === false;

  }

  /**
   *  toEqualAll
   *
   *  Expect actual to match all expectations.
   *
   *  @param mixed $expectations, ...
   *
   *  @return bool
   */
  public function toEqualAll(...$expectations):Bool {

    foreach($expectations as $expected) {

      if($this->notToEqual($expected) === true) {

        return false;

      }

    }

    return true;

  }

  /**
   *  notToEqualAll
   *
   *  Expect actual not to match all expections.
   *
   *  @param mixed $expectations, ...
   *
   *  @return bool
   */
  public function notToEqualAll(...$expectations):Bool {

    return $this->toEqualAll(...$expectations) === false;

  }

  /**
   *  toBeTrue
   *
   *  Expect actual to be explicit true.
   *
   *  @return bool
   */
  public function toBeTrue():Bool {

    return $this->actual === true;

  }

  /**
   *  toBeTruthy
   *
   *  Expect actual to be anything but falsy, {@see \Cider\Spec\Assertion::falsy}.
   *
   *  @return bool
   */
  public function toBeTruthy():Bool {

    return $this->toBeFalsy() === false;

  }

  /**
   *  toBeFalse
   *
   *  Expect actual to be explicit false.
   *
   *  @return bool
   */
  public function toBeFalse():Bool {

    return $this->actual === false;

  }

  /**
   *  toBeFalsy
   *
   *  Expect actual to be falsy.
   *
   *  @return bool
   */
  public function toBeFalsy():Bool {

    return $this->toEqualAny(false, 0, '', null, NAN);

  }

  /**
   *  toSatisfy
   *
   *  Expect actual to return true from callback.
   *
   *  @param callable $callback
   *
   *  @return bool
   */
  public function toSatisfy(Callable $callback, ...$callbackArguments):Bool {

    return call_user_func_array($callback, [$this->actual] + $callbackArguments) === true;

  }

  /**
   *  instanceOf
   *
   *  Expects actual to be instance of expected.
   *
   *  @param string $expected
   *
   *  @return bool
   */
  public function instanceOf($expected):Bool {

    return is_a($this->actual, $expected);

  }

  /**
   *  typeOf
   *
   *  Expects actual to be type of expected.
   *
   *  @param string $expected
   *
   *  @return bool
   */
  public function typeOf(String $expected):Bool {

    $validObjectTypes = [
      'array',
      'bool',
      'callable',
      'double',
      'float',
      'int',
      'integer',
      'long',
      'null',
      'numeric',
      'object',
      'real',
      'resource',
      'scalar',
      'string'
    ];

    if($this->expect($expected)->toEqualAny(...$validObjectTypes) === true) {

      return $this->toSatisfy("is_{$expected}");

    }

    return false;

  }

}
