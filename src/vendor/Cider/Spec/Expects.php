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

/**
 *  Expects
 *
 *  Lightweight expectation library.
 *
 *  @vendor Cider
 *  @package Spec
 *
 *  @version 1.0.0
 *
 *  @author Carbin Creative <hej@carbin.se>
 */
class Expects implements Expectations {

  /**
   *  @const array VALID_OBJECT_TYPES
   */
  const VALID_OBJECT_TYPES = [
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

  /**
   *  @var mixed $actual
   */
  protected $actual;

  /**
   *  Constructor
   *
   *  Sets {@see \Cider\Spec\Expect::$actual}.
   *
   *  @param mixed $actual
   *
   *  @return void
   */
  public function __construct($actual = null) {

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

  public function each(...$actuals):ExpectsAll {

    return new ExpectsAll(...$actuals);

  }

  /**
   *  @see \Cider\Spec\Expects::toEqual
   */
  public function toEqual($expected):Bool {

    return $this->actual === $expected;

  }

  /**
   *  @see \Cider\Spec\Expects::notToEqual
   */
  public function notToEqual($expected):Bool {

    return $this->toEqual($expected) === false;

  }

  /**
   *  @see \Cider\Spec\Expects::toEqualAny
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
   *  @see \Cider\Spec\Expects::notToEqualAny
   */
  public function notToEqualAny(...$expectations):Bool {

    return $this->toEqualAny(...$expectations) === false;

  }

  /**
   *  @see \Cider\Spec\Expects::toEqualAll
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
   *  @see \Cider\Spec\Expects::notToEqualAll
   */
  public function notToEqualAll(...$expectations):Bool {

    return $this->toEqualAll(...$expectations) === false;

  }

  /**
   *  @see \Cider\Spec\Expects::toBeTrue
   */
  public function toBeTrue():Bool {

    return $this->actual === true;

  }

  /**
   *  @see \Cider\Spec\Expects::toBeTruthy
   */
  public function toBeTruthy():Bool {

    return $this->toBeFalsy() === false;

  }

  /**
   *  @see \Cider\Spec\Expects::toBeFalse
   */
  public function toBeFalse():Bool {

    return $this->actual === false;

  }

  /**
   *  @see \Cider\Spec\Expects::toBeFalsy
   */
  public function toBeFalsy():Bool {

    return $this->toEqualAny(false, 0, '', null, NAN);

  }

  /**
   *  @see \Cider\Spec\Expects::toSatisfy
   */
  public function toSatisfy(Callable $callback, ...$callbackArguments):Bool {

    return call_user_func_array($callback, [$this->actual] + $callbackArguments) === true;

  }

  /**
   *  @see \Cider\Spec\Expects::instanceOf
   */
  public function instanceOf($expected):Bool {

    return is_a($this->actual, $expected);

  }

  /**
   *  @see \Cider\Spec\Expects::typeOf
   */
  public function typeOf(String $expected):Bool {

    if($this->expect($expected)->toEqualAny(...self::VALID_OBJECT_TYPES) === true) {

      return $this->toSatisfy("is_{$expected}");

    }

    return false;

  }

}
