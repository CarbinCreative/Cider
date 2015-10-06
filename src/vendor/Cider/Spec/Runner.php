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
 *  Runner
 *
 *  Spec runner, runs a test group, or single test.
 *
 *  @vendor Cider
 *  @package Spec
 *
 *  @version 1.0.0
 *
 *  @author Carbin Creative <hej@carbin.se>
 */
class Runner {

  /**
   *  @const int SPEC_SLEEP_DELAY
   */
  const SPEC_SLEEP_DELAY = 1000;

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
   *  @var array $specs
   */
  protected $specs = [];

  /**
   *  @var int $numSpecs
   */
  protected $numSpecs = 0;

  /**
   *  @var string $currentSpec
   */
  protected $currentSpec;

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
   *  Adds a callback that is called before each spec test is run.
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
   *  Adds a callback that is called after each spec test is run.
   *
   *  @param callable $afterEachCallback
   *
   *  @return void
   */
  public function afterEach(Callable $afterEachCallback) {

    $this->afterEach = $afterEachCallback;

  }

  /**
   *  numSkipped
   *
   *  Returns the number of skipped specs.
   *
   *  @return int
   */
  public function numSkipped():Int {

    return $this->numSkipped;

  }

  /**
   *  numPassed
   *
   *  Returns the number of passed specs.
   *
   *  @return int
   */
  public function numPassed():Int {

    return $this->numPassed;

  }

  /**
   *  numFailed
   *
   *  Returns the number of failed specs..
   *
   *  @return int
   */
  public function numFailed():Int {

    return $this->numFailed;

  }

  /**
   *  register
   *
   *  Registers a spec test object.
   *
   *  @param \Cider\Spec\Spec $spec
   *
   *  @return void
   */
  public function register(Spec $spec) {

    $this->specs[$spec->description()] = $spec;

    $this->numSpecs = count($this->specs);

    $this->currentSpec = $spec->description();

  }

  /**
   *  describe
   *
   *  Invokes spec description container.
   *
   *  @return void
   */
  public function describe(String $specDescription, Callable $specContainer) {

    return call_user_func($specContainer, $this);

  }

  /**
   *  it
   *
   *  Adds a test to current spec object and invokes test container.
   *
   *  @param string $testDescription
   *  @param callable $testContainer
   *
   *  @return void
   */
  public function it(String $testDescription, Callable $testContainer) {

    if($this->currentSpec && array_key_exists($this->currentSpec, $this->specs) === true) {

      $currentSpec = $this->specs[$this->currentSpec];

      $currentSpec->it($testDescription, $testContainer);

      return call_user_func($testContainer, $this);

    }

  }

  public function runAll() {

    $timeStart = microtime(true);

    if(is_callable($this->before) === true) {

      call_user_func($this->before);

    }

    $specReports = [];

    if($this->numSpecs > 0) {

      foreach($this->specs as $specDescription => $spec) {

        if(is_callable($this->beforeEach) === true) {

          $spec->before($this->beforeEach);

        }

        if(is_callable($this->afterEach) === true) {

          $spec->after($this->afterEach);

        }

        $spec->run();

        if($spec->skipped() === true) {

          $this->numSkipped += 1;

        }

        if($spec->passed() === true) {

          $this->numPassed += 1;

        }

        if($spec->failed() === true) {

          $this->numFailed += 1;

        }

        $specReports = $specReports + $spec->report();

        usleep(self::SPEC_SLEEP_DELAY);

      }

    }

    $timeEnd = microtime(true);

    $timeElapsed = $timeEnd - $timeStart;

    $timeLabel = implode(' ', ['Specs finished in', $timeElapsed, 'seconds.']);

    $this->report = [
      'timeStart' => $timeStart,
      'timeEnd' => $timeEnd,
      'timeElapsed' => $timeElapsed,
      'timeLabel' => $timeLabel,
      'numSpecs' => $this->numSpecs,
      'numSkipped' => $this->numSkipped,
      'numPassed' => $this->numPassed,
      'numFailed' => $this->numFailed,
      'specs' => $specReports
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
