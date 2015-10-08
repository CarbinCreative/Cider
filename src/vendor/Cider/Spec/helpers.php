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
 *  describe
 *
 *  Helper alias function for {@see \Cider\Spec\Runner::describe}.
 *
 *  @param string $specDescription
 *  @param callable $specContainer
 *
 *  @return void
 */
function describe(String $specDescription, Callable $specContainer) {

  $spec = new Spec;
  $spec->describe($specDescription);

  Runner::register($spec);

  return Runner::describe($specDescription, $specContainer);

}

/**
 *  it
 *
 *  Helper alias function for {@see \Cider\Spec\Runner::it}.
 *
 *  @param string $testDescription
 *  @param callable $testContainer
 *
 *  @return void
 */
function it(String $testDescription, Callable $testContainer) {

  return Runner::it($testDescription, $testContainer);

}

/**
 *  before
 *
 *  Adds a before callback.
 *
 *  @param callable $beforeCallback
 *
 *  @return void
 */
function before(Callable $beforeCallback) {

  Runner::before($beforeCallback);

}

/**
 *  after
 *
 *  Adds a after callback.
 *
 *  @param callable $afterCallback
 *
 *  @return void
 */
function after(Callable $afterCallback) {

  Runner::after($afterCallback);

}

/**
 *  beforeEach
 *
 *  Adds a before callback.
 *
 *  @param callable $beforeEachCallback
 *
 *  @return void
 */
function beforeEach(Callable $beforeEachCallback) {

  Runner::beforeEach($beforeEachCallback);

}

/**
 *  afterEach
 *
 *  Adds a after each callback.
 *
 *  @param callable $afterEachCallback
 *
 *  @return void
 */
function afterEach(Callable $afterEachCallback) {

  Runner::afterEach($afterEachCallback);

}
