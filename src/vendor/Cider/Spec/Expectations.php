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
 *  Expectations
 *
 *  Interface used to define expectation classes.
 *
 *  @vendor Cider
 *  @package Spec
 *
 *  @version 1.0.0
 *
 *  @author Carbin Creative <hej@carbin.se>
 */
interface Expectations {

  /**
   *  toEqual
   *
   *  Expect actual to be exact match to expected.
   *
   *  @param mixed $obj
   *
   *  @return bool
   */
  public function toEqual($expected):Bool;

  /**
   *  notToEqual
   *
   *  Inverse of {@see \Cider\Spec\Expects::toEqual}.
   *
   *  @param mixed $obj
   *
   *  @return bool
   */
  public function notToEqual($expected):Bool;

  /**
   *  toEqualAny
   *
   *  Expect actual to match at least one expectation.
   *
   *  @param mixed $expectations, ...
   *
   *  @return bool
   */
  public function toEqualAny(...$expectations):Bool;

  /**
   *  notToEqualAny
   *
   *  Inverse of {@see \Cider\Spec\Expects::toEqualAny}.
   *
   *  @param mixed $expectations, ...
   *
   *  @return bool
   */
  public function notToEqualAny(...$expectations):Bool;

  /**
   *  toEqualAll
   *
   *  Expect actual to match all expectations.
   *
   *  @param mixed $expectations, ...
   *
   *  @return bool
   */
  public function toEqualAll(...$expectations):Bool;

  /**
   *  notToEqualAll
   *
   *  Inverse of {@see \Cider\Spec\Expects::toEqualAll}.
   *
   *  @param mixed $expectations, ...
   *
   *  @return bool
   */
  public function notToEqualAll(...$expectations):Bool;

  /**
   *  toBeTrue
   *
   *  Expect actual to be implicit true.
   *
   *  @return bool
   */
  public function toBeTrue():Bool;

  /**
   *  toBeTruthy
   *
   *  Expect actual to be anything but falsy, {@see \Cider\Spec\Expects::toBeFalsy}.
   *
   *  @return bool
   */
  public function toBeTruthy():Bool;

  /**
   *  toBeFalse
   *
   *  Expect actual to be implicit false.
   *
   *  @return bool
   */
  public function toBeFalse():Bool;

  /**
   *  toBeFalsy
   *
   *  Expect actual to be falsy.
   *
   *  @return bool
   */
  public function toBeFalsy():Bool;

  /**
   *  toSatisfy
   *
   *  Custom satisfy handler, passes through actual and additional arguments.
   *
   *  @param callable $callback
   *
   *  @return bool
   */
  public function toSatisfy(Callable $callback, ...$callbackArguments):Bool;

  /**
   *  instanceOf
   *
   *  Expects actual to be instance of expected.
   *
   *  @param string $expected
   *
   *  @return bool
   */
  public function instanceOf($expected):Bool;

  /**
   *  typeOf
   *
   *  Expects actual to be type of expected.
   *
   *  @param string $expected
   *
   *  @return bool
   */
  public function typeOf(String $expected):Bool;

}
