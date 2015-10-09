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
 *  Spec
 *
 *  Spec test descriptor class.
 *
 *  @vendor Cider
 *  @package Spec
 *
 *  @version 1.0.0
 *
 *  @author Carbin Creative <hej@carbin.se>
 */
class Spec {

  /* @mixins */
  use \Cider\Event\Emitter;

  /**
   *  @const int TEST_SLEEP_DELAY
   */
  const TEST_SLEEP_DELAY = 1000;

  /**
   *  @var callable $before
   */
  protected $before;

  /**
   *  @var callable $after
   */
  protected $after;

  /**
   *  @var callable $beforeEach
   */
  protected $beforeEach;

  /**
   *  @var callable $afterEach
   */
  protected $afterEach;

  /**
   *  @var string $description
   */
  protected $description;

  /**
   *  @var array $tests
   */
  protected $tests = [];

  /**
   *  @var int $numTests
   */
  protected $numTests = 0;

  /**
   *  @var int $numSkipped
   */
  protected $numSkipped = 0;

  /**
   *  @var int $numPassed
   */
  protected $numPassed = 0;

  /**
   *  @var int $numFailed
   */
  protected $numFailed = 0;

  /**
   *  @var array $report
   */
  protected $report = [];

  /**
   *  before
   *
   *  Adds a callback that is called before a spec is run.
   *
   *  @param callable $beforeCallback
   *
   *  @return void
   */
  public function before(Callable $beforeCallback) {

    $this->before = $beforeCallback;

  }

  /**
   *  after
   *
   *  Adds a callback that is called after a spec is run.
   *
   *  @param callable $afterCallback
   *
   *  @return void
   */
  public function after(Callable $afterCallback) {

    $this->after = $afterCallback;

  }

  /**
   *  beforeEach
   *
   *  Adds a callback that is called before a spec is run.
   *
   *  @param callable $beforeEachCallback
   *
   *  @return void
   */
  public function beforeEach(Callable $beforeEachCallback) {

    $this->beforeEach = $beforeEachCallback;

  }

  /**
   *  afterEach
   *
   *  Adds a callback that is called after a spec is run.
   *
   *  @param callable $afterEachCallback
   *
   *  @return void
   */
  public function afterEach(Callable $afterEachCallback) {

    $this->afterEach = $afterEachCallback;

  }

  /**
   *  describe
   *
   *  Sets spec test description.
   *
   *  @param string $description
   *
   *  @return void
   */
  public function describe(String $description) {

    $this->description = $description;

  }

  /**
   *  description
   *
   *  Returns spec description.
   *
   *  @return string
   */
  public function description():String {

    return $this->description ?? '';

  }

  /**
   *  it
   *
   *  Sets test description and callback.
   *
   *  @param string $testDescription
   *  @param callable $testContainer
   *
   *  @return void
   */
  public function it(String $testDescription, Callable $testCallback) {

    $this->tests[$testDescription] = $testCallback;

    $this->numTests = count($this->tests);

  }

  /**
   *  numTests
   *
   *  Returns the number of tests in this spec.
   *
   *  @return int
   */
  public function numTests():Int {

    return $this->numTests;

  }

  /**
   *  numSkipped
   *
   *  Returns the number of skipped tests in this spec.
   *
   *  @return int
   */
  public function numSkipped():Int {

    return $this->numSkipped;

  }

  /**
   *  numPassed
   *
   *  Returns the number of passed tests in this spec.
   *
   *  @return int
   */
  public function numPassed():Int {

    return $this->numPassed;

  }

  /**
   *  numFailed
   *
   *  Returns the number of failed tests in this spec.
   *
   *  @return int
   */
  public function numFailed():Int {

    return $this->numFailed;

  }

  /**
   *  skip
   *
   *  Reports current spec test as skipped.
   *
   *  @param string $testDescription
   *
   *  @emits "skip"
   *
   *  @return void
   */
  protected function skip(String $testDescription) {

    $this->numSkipped += 1;

    $this->emit('skip', $testDescription);

  }

  /**
   *  pass
   *
   *  Reports current spec test as passed.
   *
   *  @param string $testDescription
   *
   *  @emits "pass"
   *
   *  @return void
   */
  protected function pass(String $testDescription) {

    $this->numPassed += 1;

    $this->emit('pass', $testDescription);

  }

  /**
   *  fail
   *
   *  Reports current spec test as failed.
   *
   *  @param string $testDescription
   *
   *  @emits "fail"
   *
   *  @return void
   */
  protected function fail(String $testDescription) {

    $this->numFailed += 1;

    $this->emit('fail', $testDescription);

  }

  /**
   *  skipped
   *
   *  Returns true if all tests in this spec were skipped.
   *
   *  @return bool
   */
  public function skipped():Bool {

    return $this->numTests === $this->numSkipped;

  }

  /**
   *  passed
   *
   *  Returns true if no tests in this spec failed.
   *
   *  @return bool
   */
  public function passed():Bool {

    return $this->numFailed === 0;

  }

  /**
   *  failed
   *
   *  Returns true if any test failed.
   *
   *  @return bool
   */
  public function failed():Bool {

    return $this->numFailed > 0;

  }

  /**
   *  run
   *
   *  Runs all tests in this spec.
   *
   *  @emits "tick"
   *
   *  @return void
   */
  public function run() {

    $timeStart = microtime(true);

    if(is_callable($this->before) === true) {

      call_user_func($this->before);

    }

    $specReport = [];

    if($this->numTests > 0) {

      foreach($this->tests as $testDescription => $testCallback) {

        if(is_callable($this->beforeEach) === true) {

          call_user_func($this->beforeEach);

        }

        $testCallbackResult = call_user_func($testCallback);

        if($testCallbackResult === true) {

          $this->pass($testDescription);

          $specReport[$testDescription] = ['pass', $testDescription];

        } else if($testCallbackResult === false) {

          $this->fail($testDescription);

          $specReport[$testDescription] = ['fail', $testDescription];

        } else {

          $this->skip($testDescription);

          $specReport[$testDescription] = ['skip', $testDescription];

        }

        if(is_callable($this->afterEach) === true) {

          call_user_func($this->afterEach);

        }

        $this->emit('tick', $testDescription);

        usleep(self::TEST_SLEEP_DELAY);

      }

    }

    $timeEnd = microtime(true);

    $timeElapsed = $timeEnd - $timeStart;

    $timeLabel = implode(' ', ['Spec finished in', $timeElapsed, 'seconds.']);

    $this->report[$this->description] = [
      'timeStart' => $timeStart,
      'timeEnd' => $timeEnd,
      'timeElapsed' => $timeElapsed,
      'timeLabel' => $timeLabel,
      'skipped' => $this->skipped(),
      'passed' => $this->passed(),
      'failed' => $this->failed(),
      'numTests' => $this->numTests,
      'numSkipped' => $this->numSkipped,
      'numPassed' => $this->numPassed,
      'numFailed' => $this->numFailed,
      'tests' => $specReport
    ];

    if(is_callable($this->after) === true) {

      call_user_func($this->after);

    }

  }

  /**
   *  report
   *
   *  Returns spec report.
   *
   *  @return array
   */
  public function report():Array {

    return $this->report;

  }

}
