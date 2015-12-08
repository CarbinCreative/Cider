<?php
/**
 *  Cider
 *
 *  Cider is a PHP based object oriented nano-framework for building small web applications.
 *
 *  @author Carbin Creative <hej@carbin.se>
 *  @license http://opensource.org/licenses/MIT MIT
 */

/* @namespace Delegation */
namespace Cider\Delegation;

/* Deny direct file access */
if(!defined('CIDER_ROOT_PATH')) exit;

/* @imports */
use Cider\Exception\OverflowException;

/**
 *  RoutePath
 *
 *  Route path object. Inspired by {@link https://github.com/slimphp/Slim/blob/2.x/Slim/Route.php}.
 *
 *  @vendor Cider
 *  @package Delegation
 *
 *  @version 1.0.0
 *
 *  @author Carbin Creative <hej@carbin.se>
 */
class RoutePath {

  /**
   *  @const string ROUTE_PATTERN_DELIMITER
   */
  const ROUTE_PATTERN_DELIMITER = '/';

  /**
   *  @const string ROUTE_PATTERN_PARAMETER_REGEX
   */
  const ROUTE_PATTERN_PARAMETER_REGEX = '~:([\w]+)\+?~';

  /**
   *  @var int $maxMiddlewares
   */
  protected $maxMiddlewares = 10;

  /**
   *  @var string $patternPath
   */
  protected $patternPath;

  /**
   *  @var string $patternRegex
   */
  protected $patternRegex;

  /**
   *  @var array $parameters
   */
  protected $parameters = [];

  /**
   *  @var array $middlewares
   */
  protected $middlewares = [];

  /**
   *  @var array $conditions
   */
  protected $conditions = [];

  /**
   *  @var callable $callback
   */
  protected $callback;

  /**
   *  @var callable $beforeCallback
   */
  protected $beforeCallback;

  /**
   *  @var callable $afterCallback
   */
  protected $afterCallback;

  /**
   *  Constructor
   *
   *  Sets route pattern and route callback method.
   *
   *  @param string $routePattern
   *  @param callable $routeCallback
   *
   *  @return self
   */
  public function __construct(String $routePattern, Callable $routeCallback) {

    $this->patternPath = $routePattern ?: self::ROUTE_PATTERN_DELIMITER;

    $this->patternRegex = $this->patternToRegex($this->patternPath);

    $this->callback = $routeCallback;

  }

  /**
   *  setMaxMiddlewares
   *
   *  Sets max route middlewares to each route map item.
   *
   *  @param int $maxMiddlewares
   *
   *  @return void
   */
  public function setMaxMiddlewares(Int $maxMiddlewares) {

    $this->maxMiddlewares = $maxMiddlewares;

  }

  /**
   *  getMaxMiddlewares
   *
   *  Return max event listeners to current context.
   *
   *  @return void
   */
  public function getMaxMiddlewares():Int {

    return $this->maxMiddlewares;

  }

  /**
   *  attachMiddleware
   *
   *  Attaches a middleware callback to current route path object.
   *
   *  @param callable $routeMiddleware
   *
   *  @throws \Cider\Exception\OverflowException
   *
   *  @return void
   */
  public function attachMiddleware(Callable $routeMiddleware) {

    $this->middlewares[] = $routeMiddleware;

    if(count($this->middlewares) > $this->getMaxMiddlewares()) {

      throw new OverflowException('Maximum middlewares per route exceeded.');

    }

  }

  /**
   *  middleware
   *
   *  Alias method for {@see \Cider\Delegation\RoutePath::attachMiddleware}.
   *
   *  @param callable $routeMiddleware
   *
   *  @return self
   */
  public function middleware(Callable $routeMiddleware):self {

    $this->attachMiddleware($routeMiddleware);

    return $this;

  }

  /**
   *  before
   *
   *  Callback invoked before a route is matched.
   *
   *  @param callable $routeBeforeCallback
   *
   *  @return self
   */
  public function before(Callable $routeBeforeCallback):self {

    $this->beforeCallback = $routeBeforeCallback;

    return $this;

  }

  /**
   *  after
   *
   *  Callback invoked after a route is matched.
   *
   *  @param callable $routeAfterCallback
   *
   *  @return self
   */
  public function after(Callable $routeAfterCallback):self {

    $this->afterCallback = $routeAfterCallback;

    return $this;

  }

  /**
   *  conditionExists
   *
   *  Validates if a condition exists.
   *
   *  @param string $parameterName
   *
   *  @return bool
   */
  public function conditionExists(String $parameterName):Bool {

    return array_key_exists($parameterName, $this->conditions);

  }

  /**
   *  setCondition
   *
   *  Sets a condition to current route path object.
   *
   *  @param string $parameterName
   *  @param string $conditionRegex
   *
   *  @return void
   */
  public function setCondition(String $parameterName, String $conditionRegex) {

    $this->conditions[$parameterName] = $conditionRegex;

    // Update pattern regex
    $this->patternRegex = $this->patternToRegex($this->patternPath);

  }

  /**
   *  getCondition
   *
   *  Returns condition if it exists.
   *
   *  @param string $parameterName
   *
   *  @return string
   */
  public function getCondition(String $parameterName):String {

    if($this->conditionExists($parameterName) === true) {

      return $this->conditions[$parameterName] ?: '[^/]+';

    }

    return '';

  }

  /**
   *  condition
   *
   *  Alias method for {@see \Cider\Delegation\RoutePath::setCondition}.
   *
   *  @param string $parameterName
   *  @param string $conditionRegex
   *
   *  @return self
   */
  public function condition(String $parameterName, String $conditionRegex):self {

    $this->setCondition($parameterName, $conditionRegex);

    return $this;

  }

  /**
   *  patternToRegex
   *
   *  Creates a regex from route pattern.
   *
   *  @param string $routePattern
   *
   *  @return string
   */
  protected function patternToRegex(String $routePattern):String {

    $routeRequestPathRegex = preg_replace_callback(
      self::ROUTE_PATTERN_PARAMETER_REGEX,
      [$this, 'namedPatternRegex'],
      str_replace(')', ')?', $routePattern)
    );

    if(substr($routePattern, -1) === self::ROUTE_PATTERN_DELIMITER) {

      $routeRequestPathRegex .= '?';

    }

    $routeRequestPathRegex = implode(['~^', $routeRequestPathRegex, '$~i']);

    return $routeRequestPathRegex;

  }

  /**
   *  namedPatternRegex
   *
   *  Returns a named capture regex for each named pattern match from {@see \Cider\Delegation\RoutePath::patternToRegex}.
   *
   *  @param array $namedRoutePatternMatch
   *
   *  @return string
   */
  protected function namedPatternRegex(Array $namedRoutePatternMatch):String {

    list($namedRoutePattern, $namedRouteParameter) = $namedRoutePatternMatch;

    $namedCaptureRegexPattern = '[^/]+';

    if($this->conditionExists($namedRouteParameter) === true) {

      $namedCaptureRegexPattern = $this->getCondition($namedRouteParameter);

    }

    if(substr($namedRoutePattern, -1) === '+') {

      $namedCaptureRegexPattern = '.+';

    }

    return implode(['(?P<', $namedRouteParameter ,'>', $namedCaptureRegexPattern, ')']);

  }

  /**
   *  matches
   *
   *  Validates if route patch matches specified URI.
   *
   *  @param string $requestUri
   *
   *  @return bool
   */
  public function matches(String $requestUri):Bool {

    if(preg_match($this->patternRegex, $requestUri, $requestUriMatches) !== false) {

      if(count($requestUriMatches) === 0) {

        return false;

      }

      foreach($requestUriMatches as $parameterName => $requestParameter) {

        if(is_string($parameterName) === true) {

          $this->parameters[$parameterName] = $requestParameter;

        }

      }

      return true;

    }

    return false;

  }

  /**
   *  invokeBeforeCallback
   *
   *  Invokes a "before" callback, passes through request parameters, callback must return array.
   *
   *  @param array $requestParameters
   *
   *  @return array
   */
  protected function invokeBeforeCallback(Array $requestParameters):Array {

    if(is_callable($this->beforeCallback) === true) {

      $requestParameters = call_user_func_array($this->beforeCallback, [$requestParameters]);

    }

    return $requestParameters;

  }

  /**
   *  invokeBeforeCallback
   *
   *  Invokes a "before" callback, passes through request parameters, callback must return array.
   *
   *  @param array $requestParameters
   *
   *  @return string
   */
  protected function invokeAfterCallback(String $callbackOutput):String {

    if(is_callable($this->afterCallback) === true) {

      $callbackOutput = call_user_func_array($this->afterCallback, [$callbackOutput]);

    }

    return $callbackOutput;

  }

  /**
   *  invokeMiddlewares
   *
   *  Invokes and collects return values from route path middlewares.
   *
   *  @return array
   */
  protected function invokeMiddlewares():Array {

    $outputFromMiddlewares = [];

    foreach($this->middlewares as $index => $middleware) {

      if(array_key_exists($index + 1, $this->middlewares) === true) {

        $nextMiddleware = $this->middlewares[$index + 1];

        $outputFromMiddlewares[] = call_user_func_array($middleware, [$nextMiddleware]);

      }

    }

    return $outputFromMiddlewares;

  }

  /**
   *  invoke
   *
   *  Invokes route before, after and callback handlers and returns output as string.
   *
   *  @return string
   */
  public function invoke():String {

    $parameters = $this->invokeBeforeCallback($this->parameters);

    $parameters = array_merge($parameters, $this->invokeMiddlewares());

    $output = call_user_func_array($this->callback, $parameters);

    $output = $this->invokeAfterCallback($output);

    return $output;

  }

}