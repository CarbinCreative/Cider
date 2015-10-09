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
 *  Static spec runner.
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
   *  @var array $specs
   */
  protected static $specs = [];

  /**
   *  @var int $numSpecs
   */
  protected static $numSpecs = 0;

  /**
   *  @var string $currentSpec
   */
  protected static $currentSpec;

  /**
   *  @var \Cider\Spec\Spec $currentSpecInstance
   */
  protected static $currentSpecInstance;

  /**
   *  @var int $numSkipped
   */
  protected static $numSkipped = 0;

  /**
   *  @var int $numPassed
   */
  protected static $numPassed = 0;

  /**
   *  @var int $numFailed
   */
  protected static $numFailed = 0;

  /**
   *  @var array $report
   */
  protected static $report = [];

  /**
   *  before
   *
   *  Adds a callback to current spec, that is called before a spec is run.
   *
   *  @param callable $beforeCallback
   *
   *  @return void
   */
  public static function before(Callable $beforeCallback) {

    if(self::$currentSpecInstance instanceof Spec) {

      self::$currentSpecInstance->before($beforeCallback);

    }

  }

  /**
   *  after
   *
   *  Adds a callback to current spec, that is called after a spec is run.
   *
   *  @param callable $afterCallback
   *
   *  @return void
   */
  public static function after(Callable $afterCallback) {

    if(self::$currentSpecInstance instanceof Spec) {

      self::$currentSpecInstance->after($afterCallback);

    }

  }

  /**
   *  beforeEach
   *
   *  Adds a callback to current spec, that is called before each spec test is run.
   *
   *  @param callable $beforeEachCallback
   *
   *  @return void
   */
  public static function beforeEach(Callable $beforeEachCallback) {

    if(self::$currentSpecInstance instanceof Spec) {

      self::$currentSpecInstance->beforeEach($beforeEachCallback);

    }

  }

  /**
   *  afterEach
   *
   *  Adds a callback to current spec, that is called after each spec test is run.
   *
   *  @param callable $afterEachCallback
   *
   *  @return void
   */
  public static function afterEach(Callable $afterEachCallback) {

    if(self::$currentSpecInstance instanceof Spec) {

      self::$currentSpecInstance->afterEach($afterEachCallback);

    }

  }

  /**
   *  numSkipped
   *
   *  Returns the number of skipped specs.
   *
   *  @return int
   */
  public static function numSkipped():Int {

    return self::$numSkipped;

  }

  /**
   *  numPassed
   *
   *  Returns the number of passed specs.
   *
   *  @return int
   */
  public static function numPassed():Int {

    return self::$numPassed;

  }

  /**
   *  numFailed
   *
   *  Returns the number of failed specs..
   *
   *  @return int
   */
  public static function numFailed():Int {

    return self::$numFailed;

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
  public static function register(Spec $spec) {

    self::$specs[$spec->description()] = $spec;

    self::$numSpecs = count(self::$specs);

    self::$currentSpec = $spec->description();

    self::$currentSpecInstance = $spec;

  }

  /**
   *  describe
   *
   *  Invokes spec description container.
   *
   *  @return void
   */
  public static function describe(String $specDescription, Callable $specContainer) {

    return call_user_func($specContainer);

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
  public static function it(String $testDescription, Callable $testContainer) {

    if(self::$currentSpec && array_key_exists(self::$currentSpec, self::$specs) === true) {

      self::$currentSpecInstance->it($testDescription, $testContainer);

      return call_user_func($testContainer);

    }

  }

  /**
   *  runAll
   *
   *  Runs all spec tests.
   *
   *  @return void
   */
  public static function runAll() {

    $timeStart = microtime(true);

    $specReports = [];

    if(self::$numSpecs > 0) {

      foreach(self::$specs as $specDescription => $spec) {

        $spec->run();

        if($spec->skipped() === true) {

          self::$numSkipped += 1;

        }

        if($spec->passed() === true) {

          self::$numPassed += 1;

        }

        if($spec->failed() === true) {

          self::$numFailed += 1;

        }

        $specReports = $specReports + $spec->report();

        usleep(self::SPEC_SLEEP_DELAY);

      }

    }

    $timeEnd = microtime(true);

    $timeElapsed = $timeEnd - $timeStart;

    $timeLabel = implode(' ', ['Specs finished in', $timeElapsed, 'seconds.']);

    self::$report = [
      'timeStart' => $timeStart,
      'timeEnd' => $timeEnd,
      'timeElapsed' => $timeElapsed,
      'timeLabel' => $timeLabel,
      'numSpecs' => self::$numSpecs,
      'numSkipped' => self::$numSkipped,
      'numPassed' => self::$numPassed,
      'numFailed' => self::$numFailed,
      'specs' => $specReports
    ];

  }

  /**
   *  report
   *
   *  Returns spec report.
   *
   *  @return array
   */
  public static function report():Array {

    return self::$report;

  }

}
