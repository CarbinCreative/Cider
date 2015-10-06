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

// Invoke spec runner
$runner = new Runner;

/**
 *  currentRunner
 *
 *  Returns current runner instance.
 *
 *  @return \Cider\Spec\Runner
 */
function currentRunner():Runner {

  global $runner;

  return $runner;

}

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

  global $runner;

  $spec = new Spec;
  $spec->describe($specDescription);

  $runner->register($spec);

  return $runner->describe($specDescription, $specContainer);

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

  global $runner;

  return $runner->it($testDescription, $testContainer);

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

  global $runner;

  $runner->before($beforeCallback);

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

  global $runner;

  $runner->after($afterCallback);

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

  global $runner;

  $runner->beforeEach($beforeEachCallback);

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

  global $runner;

  $runner->afterEach($afterEachCallback);

}

/**
 *  expect
 *
 *  Creates a new expectation instance and returns it.
 *
 *  @param mixed $actual
 *
 *  @return \Cider\Spec\Expectation
 */
function expect($actual):Expectation {

  return new Expectation($actual);

}
