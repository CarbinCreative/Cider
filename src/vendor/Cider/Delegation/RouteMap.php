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
use Exceptions\RouteAlreadyDefinedException;
use Exceptions\RouteNotFoundException;
use Exceptions\RouteException;

/**
 *  RouteMap
 *
 *  Route map handler.
 *
 *  @vendor Cider
 *  @package Route
 *
 *  @version 1.0.0
 *
 *  @author Carbin Creative <hej@carbin.se>
 */
class RouteMap {

	/* @mixins */
	use Hookable;

	/**
	 *  @const string REQUEST_METHOD_DELIMITER
	 */
	const REQUEST_METHOD_DELIMITER = '|';

	/**
	 *  @const array DEFAULT_REQUEST_METHODS
	 */
	const DEFAULT_REQUEST_METHODS = [
		'ANY',
		'GET',
		'POST',
		'PUT',
		'UPDATE',
		'PATCH',
		'DELETE'
	];

	/**
	 *  @const array NON_STANDARD_REQUEST_METHODS
	 */
	const NON_STANDARD_REQUEST_METHODS = [
		'ANY',
		'UPDATE'
	];

	/**
	 *  @var bool $allowCustomRequestMethods
	 */
	protected $allowCustomRequestMethods = false;

	/**
	 *  @var array $routePaths
	 */
	protected $routePaths = [];

	/**
	 *  @var array $routeScopeSegments
	 */
	protected $routeScopeSegments = [];

	/**
	 *  @var string $cachedRouteRequestPath
	 */
	protected $cachedRouteRequestPath = null;

	/**
	 *  @var array $cachedRouteRequestMethods
	 */
	protected $cachedRouteRequestMethods = [];

	/**
	 *  @var callable $missingRouteCallback
	 */
	protected $missingRouteCallback;

	/**
	 *  @var callable $errorRouteCallback
	 */
	protected $errorRouteCallback;

	/**
	 *  allowCustomRequestMethods
	 *
	 *  Allows custom HTTP request methods.
	 *
	 *  @return void
	 */
	public function allowCustomRequestMethods() {

		$this->allowCustomRequestMethods = true;

	}

	/**
	 *  denyCustomRequestMethods
	 *
	 *  Denies custom HTTP request methods.
	 *
	 *  @return void
	 */
	public function denyCustomRequestMethods() {

		$this->allowCustomRequestMethods = false;

	}

	/**
	 *  validRequestMethod
	 *
	 *  Validates input request methods, ignores testing if {@see Cider\Route\Map::$allowCustomRequestMethods} is on.
	 *
	 *  @param string $requestMethods, ...
	 *
	 *  @return bool
	 */
	protected function validRequestMethod(String ...$requestMethods):Bool {

		if($this->allowCustomRequestMethods === true) {

			return true;

		}

		$requestMethods = array_map('strtoupper', $requestMethods);

		$isValidRequestMethod = (count(array_diff($requestMethods, self::DEFAULT_REQUEST_METHODS)) === 0);

		return $isValidRequestMethod;

	}

	/**
	 *  normalizeRequestMethod
	 *
	 *  Returns an array of request methods, removes non-standard HTTP requests (but allows custom ones).
	 *
	 *  @param string $requestMethods, ...
	 *
	 *  @return array
	 */
	protected function normalizeRequestMethod(String ...$requestMethods):Array {

		$normalizedRequestMethods = [];

		$requestMethods = array_map('strtoupper', $requestMethods);

		foreach($requestMethods as $requestMethod) {

			if($requestMethod === 'ANY') {

				$normalizedRequestMethods = array_merge($normalizedRequestMethods, self::DEFAULT_REQUEST_METHODS);

			} else if($requestMethod === 'UPDATE') {

				$normalizedRequestMethods = array_merge($normalizedRequestMethods, ['PUT', 'PATCH']);

			} else {

				if($this->validRequestMethod($requestMethod) === true) {

					$normalizedRequestMethods[] = $requestMethod;

				}

			}

		}

		$normalizedRequestMethods = array_unique($normalizedRequestMethods);

		foreach(self::NON_STANDARD_REQUEST_METHODS as $nonStandardRequestMethod) {

			if(($key = array_search($nonStandardRequestMethod, $normalizedRequestMethods)) !== false) {

				unset($normalizedRequestMethods[$key]);

			}

		}

		return array_filter($normalizedRequestMethods);

	}

	/**
	 *  flushRouteCache
	 *
	 *  Unsets route request cache.
	 *
	 *  @return void
	 */
	protected function flushRouteCache() {

		$this->cachedRouteRequestPath = null;

		$this->cachedRouteRequestMethods = [];

	}

	/**
	 *  cacheRouteRequestPath
	 *
	 *  Caches route request path.
	 *
	 *  @param string $routeRequestPath
	 *
	 *  @return void
	 */
	protected function cacheRouteRequestPath(String $routeRequestPath) {

		$this->cachedRouteRequestPath = $routeRequestPath;

	}

	/**
	 *  cacheRouteRequestMethods
	 *
	 *  Caches route request methods.
	 *
	 *  @param string $routeRequestMethods, ...
	 *
	 *  @return void
	 */
	protected function cacheRouteRequestMethods(String ...$routeRequestMethods) {

		$this->cachedRouteRequestMethods = $routeRequestMethods;

	}

	/**
	 *  routeRequestPath
	 *
	 *  Returns a normalized route request path including route scope paths.
	 *
	 *  @param string $routeRequestString
	 *
	 *  @return string
	 */
	public function routeRequestPath(String $routeRequestString):String {

		$sanitizeRouteRequestStringRegex = implode(['~', RoutePath::ROUTE_PATTERN_DELIMITER, '{2,}~']);

		$routeRequestSegments = array_merge($this->routeScopeSegments, [$routeRequestString]);

		$routeRequestString = implode(RoutePath::ROUTE_PATTERN_DELIMITER, $routeRequestSegments);

		$cleanRouteRequestString = preg_replace($sanitizeRouteRequestStringRegex, RoutePath::ROUTE_PATTERN_DELIMITER, $routeRequestString);

		if(empty($cleanRouteRequestString) === true) {

			$cleanRouteRequestString = RoutePath::ROUTE_PATTERN_DELIMITER;

		}

		$cleanRouteRequestString = trim($cleanRouteRequestString, RoutePath::ROUTE_PATTERN_DELIMITER);

		return $cleanRouteRequestString;

	}

	/**
	 *  pushScopeSegment
	 *
	 *  Adds route scope segment.
	 *
	 *  @param string $routeScopeSegment
	 *
	 *  @return void
	 */
	protected function pushScopeSegment(String $routeScopeSegment) {

		$this->routeScopeSegments[] = $routeScopeSegment;

	}

	/**
	 *  popScopeSegment
	 *
	 *  Removes route scope segment.
	 *
	 *  @return void
	 */
	protected function popScopeSegment() {

		array_pop($this->routeScopeSegments);

	}

	/**
	 *  scope
	 *
	 *  Route scope handler used to group route paths.
	 *
	 *  @param string $routeScopePath
	 *  @param callable $routeScopeWrapper
	 *
	 *  @return self
	 */
	public function scope(String $routeScopePath, Callable $routeScopeWrapper):self {

		if(in_array($routeScopePath, $this->routeScopeSegments) === false) {

			$this->pushScopeSegment(trim($routeScopePath, RoutePath::ROUTE_PATTERN_DELIMITER));

		}

		$scopeWrapperClosure = $routeScopeWrapper->bindTo($this);
		$scopeWrapperClosure();

		$this->popScopeSegment();

		return $this;

	}

	/**
	 *  paths
	 *
	 *  Returns all route paths.
	 *
	 *  @return array
	 */
	public function paths():Array {

		return $this->routePaths;

	}

	/**
	 *  add
	 *
	 *  Adds a new request path object and returns it.
	 *
	 *  @param String $routeRequestMethod
	 *  @param string $routeRequestPathPattern
	 *  @param string $routeRequestCallback
	 *
	 *  @return \Cider\Route\RoutePath
	 */
	public function add(String $routeRequestMethod, String $routeRequestPathPattern, Callable $routeRequestCallback = null):RoutePath {

		$routeRequestMethods = explode(self::REQUEST_METHOD_DELIMITER, $routeRequestMethod);

		if($this->validRequestMethod(...$routeRequestMethods) === false) {

			throw new InvalidArgumentException('Invalid route request method.');

		}

		$routeRequestMethods = $this->normalizeRequestMethod(...$routeRequestMethods);

		$routeRequestPath = $this->routeRequestPath($routeRequestPathPattern);

		if(empty($routeRequestPath) === true) {

			$routeRequestPath = RoutePath::ROUTE_PATTERN_DELIMITER;

		}

		$this->cacheRouteRequestPath($routeRequestPath);
		$this->cacheRouteRequestMethods(...$routeRequestMethods);

		foreach($routeRequestMethods as $routeRequestMethod) {

			$currentRouteRequest = $this->routePaths[$routeRequestPath];

			if(array_key_exists($routeRequestMethod, $currentRouteRequest ?: []) === true) {

				throw new RouteAlreadyDefinedException("Route for '{$routeRequestPath}' via {$routeRequestMethod} already defined.");

			}

			$routeRequestObject = new RoutePath($routeRequestPath, $routeRequestCallback ?? $noopRouteCallback);
			$this->routePaths[$routeRequestPath][$routeRequestMethod] = $routeRequestObject;

		}

		return $routeRequestObject;

	}

	/**
	 *  any
	 *
	 *  Helper method for any HTTP request method.
	 *
	 *  @param string $routeRequestPathPattern
	 *  @param string $routeRequestCallback
	 *
	 *  @return \Cider\Route\RoutePath
	 */
	public function any(String $routeRequestPathPattern, Callable $routeRequestCallback = null):RoutePath {

		$noopRouteCallback = [$this, 'noopRouteCallback'];

		return $this->add('any', $routeRequestPathPattern, $routeRequestCallback ?? $noopRouteCallback);

	}

	/**
	 *  get
	 *
	 *  Helper method for GET requests.
	 *
	 *  @param string $routeRequestPathPattern
	 *  @param string $routeRequestCallback
	 *
	 *  @return \Cider\Route\RoutePath
	 */
	public function get(String $routeRequestPathPattern, Callable $routeRequestCallback):RoutePath {

		return $this->add('get', $routeRequestPathPattern, $routeRequestCallback);

	}

	/**
	 *  post
	 *
	 *  Helper method for POST requests.
	 *
	 *  @param string $routeRequestPathPattern
	 *  @param string $routeRequestCallback
	 *
	 *  @return \Cider\Route\RoutePath
	 */
	public function post(String $routeRequestPathPattern, Callable $routeRequestCallback):RoutePath {

		return $this->add('post', $routeRequestPathPattern, $routeRequestCallback);

	}

	/**
	 *  put
	 *
	 *  Helper method for PUT requests.
	 *
	 *  @param string $routeRequestPathPattern
	 *  @param string $routeRequestCallback
	 *
	 *  @return \Cider\Route\RoutePath
	 */
	public function put(String $routeRequestPathPattern, Callable $routeRequestCallback):RoutePath {

		return $this->add('put', $routeRequestPathPattern, $routeRequestCallback);

	}

	/**
	 *  patch
	 *
	 *  Helper method for PATCH requests.
	 *
	 *  @param string $routeRequestPathPattern
	 *  @param string $routeRequestCallback
	 *
	 *  @return \Cider\Route\RoutePath
	 */
	public function patch(String $routeRequestPathPattern, Callable $routeRequestCallback):RoutePath {

		return $this->add('patch', $routeRequestPathPattern, $routeRequestCallback);

	}

	/**
	 *  update
	 *
	 *  Helper method for PATCH and PUT requests.
	 *
	 *  @param string $routeRequestPathPattern
	 *  @param string $routeRequestCallback
	 *
	 *  @return \Cider\Route\RoutePath
	 */
	public function update(String $routeRequestPathPattern, Callable $routeRequestCallback):RoutePath {

		return $this->add('update', $routeRequestPathPattern, $routeRequestCallback);

	}

	/**
	 *  delete
	 *
	 *  Helper method for DELETE requests.
	 *
	 *  @param string $routeRequestPathPattern
	 *  @param string $routeRequestCallback
	 *
	 *  @return \Cider\Route\RoutePath
	 */
	public function delete(String $routeRequestPathPattern, Callable $routeRequestCallback):RoutePath {

		return $this->add('delete', $routeRequestPathPattern, $routeRequestCallback);

	}

	/**
	 *  missingRoute
	 *
	 *  Registeres a missing route callback handler.
	 *
	 *  @param callable $missingRouteCallback
	 *
	 *  @return self
	 */
	public function missingRoute(Callable $missingRouteCallback):self {

		$this->missingRouteCallback = $missingRouteCallback;

		return $this;

	}

	/**
	 *  handleMissingRoute
	 *
	 *  Invokes missing route callback handler.
	 *
	 *	@throws Cider\Delegation\Exceptions\RouteNotFoundException
	 *
	 *  @return string
	 */
	public function handleMissingRoute():String {

		if(is_callable($this->missingRouteCallback) === true) {

			return call_user_func_array($this->missingRouteCallback, []);

		}

		throw new RouteNotFoundException;

	}

	/**
	 *  errorRoute
	 *
	 *  Registeres a exception route callback handler.
	 *
	 *  @param callable $errorRouteCallback
	 *
	 *  @return self
	 */
	public function errorRoute(Callable $errorRouteCallback):self {

		$this->errorRouteCallback = $errorRouteCallback;

		return $this;

	}

	/**
	 *  handleErrorRoute
	 *
	 *  Invokes missing route callback handler.
	 *
	 *	@throws Cider\Delegation\Exceptions\RouteException
	 *
	 *  @return string
	 */
	public function handleErrorRoute():String {

		if(is_callable($this->errorRouteCallback) === true) {

			return call_user_func_array($this->errorRouteCallback, []);

		}

		throw new RouteException;

	}

}
