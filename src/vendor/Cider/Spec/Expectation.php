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

    $matchedExpectations = [];

    foreach($expectations as $expected) {

      $matchedExpectations[] = $this->toEqual($expected);

    }

    $matchedExpectations = array_unique($matchedExpectations);

    return (count($matchedExpectations) === 1);

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
  public function toSatisfy(Callable $callback):Bool {

    return call_user_func($callback, $this->actual) === true;

  }

}
