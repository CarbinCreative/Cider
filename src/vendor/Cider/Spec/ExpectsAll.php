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
 *  ExpectsAll
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
class ExpectsAll implements Expectations {

  /**
   *  @var mixed $actuals
   */
  protected $actuals;

  /**
   *  Constructor
   *
   *  Sets {@see \Cider\Spec\Expect::$actuals}.
   *
   *  @param mixed $actuals, ...
   *
   *  @return void
   */
  public function __construct(...$actuals) {

    $this->actuals = $actuals;

  }

  /**
   *  each
   *
   *  Maps all actuals to match expectation.
   *
   *  @param callable $expectCallback
   *  @param mixed $expectCallbackArguments, ...
   *
   *  @return bool
   */
  protected function each($expectCallback, ...$expectCallbackArguments):Bool {

    $allExpectations = array_map(function($actual) use ($expectCallback, $expectCallbackArguments) {

      var_dump($expectCallback, $expectCallbackArguments);

      return call_user_func_array([new Expects($actual), $expectCallback], $expectCallbackArguments);

    }, $this->actuals);

    $uniqueExpectations = array_unique($allExpectations);

    return (count($uniqueExpectations) === 1 && end($uniqueExpectations) === true);

  }

  /**
   *  @see \Cider\Spec\Expects::toEqual
   */
  public function toEqual($expected):Bool {

    return $this->each('toEqual', $expected);

  }

  /**
   *  @see \Cider\Spec\Expects::notToEqual
   */
  public function notToEqual($expected):Bool {

    return $this->each('notToEqual', $expected);

  }

  /**
   *  @see \Cider\Spec\Expects::toEqualAny
   */
  public function toEqualAny(...$expectations):Bool {

    return $this->each('toEqualAny', ...$expectations);

  }

  /**
   *  @see \Cider\Spec\Expects::notToEqualAny
   */
  public function notToEqualAny(...$expectations):Bool {

    return $this->each('notToEqualAny', ...$expectations);

  }

  /**
   *  @see \Cider\Spec\Expects::toEqualAll
   */
  public function toEqualAll(...$expectations):Bool {

    return $this->each('toEqualAll', ...$expectations);

  }

  /**
   *  @see \Cider\Spec\Expects::notToEqualAll
   */
  public function notToEqualAll(...$expectations):Bool {

    return $this->each('notToEqualAll', ...$expectations);

  }

  /**
   *  @see \Cider\Spec\Expects::toBeTrue
   */
  public function toBeTrue():Bool {

    return $this->each('toBeTrue');

  }

  /**
   *  @see \Cider\Spec\Expects::toBeTruthy
   */
  public function toBeTruthy():Bool {

    return $this->each('toBeTruthy');

  }

  /**
   *  @see \Cider\Spec\Expects::toBeFalse
   */
  public function toBeFalse():Bool {

    return $this->each('toBeFalse');

  }

  /**
   *  @see \Cider\Spec\Expects::toBeFalsy
   */
  public function toBeFalsy():Bool {

    return $this->each('toBeFalsy');

  }

  /**
   *  @see \Cider\Spec\Expects::toSatisfy
   */
  public function toSatisfy(Callable $callback, ...$callbackArguments):Bool {

    return $this->each('toSatisfy', $callback, ...$callbackArguments);

  }

  /**
   *  @see \Cider\Spec\Expects::instanceOf
   */
  public function instanceOf($expected):Bool {

    return $this->each('instanceOf', $expected);

  }

  /**
   *  @see \Cider\Spec\Expects::typeOf
   */
  public function typeOf(String $expected):Bool {

    return $this->each('typeOf', $expected);

  }

}
