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
use Cider\Http\Client as HttpClient;
use Cider\Exceptions\FrameworkException;

/**
 *  Hookable
 *
 *  Hookable trait, adds before, after and middleware functionality.
 *
 *  @vendor Cider
 *  @package Route
 *
 *  @version 1.0.0
 *
 *  @author Carbin Creative <hej@carbin.se>
 */
trait Hookable {

	/**
	 *  @var callable $beforeCallback
	 */
	protected $beforeCallback;

	/**
	 *  @var callable $afterCallback
	 */
	protected $afterCallback;

	/**
	 *  @var int $maxMiddlewares
	 */
	protected $maxMiddlewares = 10;

	/**
	 *  @var array $middlewares
	 */
	protected $middlewares = [];

	/**
	 *  before
	 *
	 *  Registeres a callback handler that is invoked before middlewares and route match callback.
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
	 *  invokeBeforeCallback
	 *
	 *  Invokes a "before" callback, which may manipulate request object, and response string.
	 *
	 *  @param mixed $request
	 *  @param string $response
	 *
	 *  @return array
	 */
	public function invokeBeforeCallback($request, String $response):Array {

		if(is_callable($this->beforeCallback) === true) {

			list($request, $response) = call_user_func_array($this->beforeCallback, [$request, $response]);

		}

		return [$request, $response];

	}

	/**
	 *  removeBeforeCallback
	 *
	 *  Removes before callback handler.
	 *
	 *  @return void
	 */
	public function removeBeforeCallback() {

		$this->beforeCallback = null;

	}

	/**
	 *  after
	 *
	 *  Registeres a callback handler that is invoked after middlewares and route match callback.
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
	 *  invokeAfterCallback
	 *
	 *  Invokes a "after" callback, which may manipulate request object, and response string.
	 *
	 *  @param mixed $request
	 *  @param string $response
	 *
	 *  @return array
	 */
	public function invokeAfterCallback($request, String $response):Array {

		if(is_callable($this->afterCallback) === true) {

			list($request, $response) = call_user_func_array($this->afterCallback, [$request, $response]);

		}

		return [$request, $response];

	}

	/**
	 *  removeAfterCallback
	 *
	 *  Removes after callback handler.
	 *
	 *  @return void
	 */
	public function removeAfterCallback() {

		$this->afterCallback = null;

	}

	/**
	 *  removeBeforeAndAfterCallbacks
	 *
	 *  Removes before and after callback handlers.
	 *
	 *  @return void
	 */
	public function removeBeforeAndAfterCallbacks() {

		$this->removeBeforeCallback();
		$this->removeAfterCallback();

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
	 *  getMiddlewares
	 *
	 *  Returns all route middlewares.
	 *
	 *  @return array
	 */
	public function getMiddlewares():Array {

		return $this->middlewares;

	}

	/**
	 *  removeMiddlewares
	 *
	 *  Removes all middlewares.
	 *
	 *  @return void
	 */
	public function removeMiddlewares() {

		$this->middlewares = [];

	}

	/**
	 *  middlewareExists
	 *
	 *  Validates whether or not route middleware exists.
	 *
	 *  @param callable $middleware
	 *
	 *  @return bool
	 */
	public function middlewareExists(Callable $middleware):Bool {

		return in_array($middleware, $this->middlewares);

	}

	/**
	 *  attachMiddleware
	 *
	 *  Attaches middleware if it does not exist and within middleware limit.
	 *
	 *  @param callable $middleware
	 *
	 *  @return void
	 */
	public function attachMiddleware(Callable $middleware) {

		if($this->middlewareExists($middleware) === false && count($this->middlewares) < $this->getMaxMiddlewares()) {

			$this->middlewares[] = $middleware;

		}

	}

	/**
	 *  removeMiddleware
	 *
	 *  Removes middleware if it exists.
	 *
	 *  @return void
	 */
	public function removeMiddleware(Callable $middleware) {

		if($this->middlewareExists($middleware) === true) {

			array_splice($this->middlewares, array_search($middleware, $this->middlewares));

		}

	}

	/**
	 *  middleware
	 *
	 *  Invokes {@see \Cider\Delegation\RoutePath::attachMiddleware} and returns self.
	 *
	 *  @param callable $middleware
	 *
	 *  @return self
	 */
	public function middleware(Callable $middleware):self {

		$this->attachMiddleware($middleware);

		return $this;

	}

	/**
	 *  middlewares
	 *
	 *  Attaches several middlewares at once, see {@see \Cider\Delegation\RoutePath::attachMiddleware}.
	 *
	 *  @param callable $middleware, ...
	 *
	 *  @return self
	 */
	public function middlewares(Callable ...$middlewares):self {

		foreach($middlewares as $middleware) {

			$this->attachMiddleware($middleware);

		}

		return $this;

	}

	/**
	 *  invokeMiddlewares
	 *
	 *  Invokes and collects return values from route path middlewares.
	 *
	 *  @param mixed $request
	 *  @param string $response
	 *
	 *  @return array
	 */
	public function invokeMiddlewares($request, String $response):Array {

		if(count($this->middlewares) === 0) {

			return [$request, $response];

		}

		foreach($this->middlewares as $index => $middleware) {

			if(array_key_exists($index + 1, $this->middlewares) === true) {

				$nextMiddleware = $this->middlewares[$index + 1];

			}

			list($request, $response) = call_user_func_array($middleware, [$request, $response, $nextMiddleware ?? null]);

		}

		return [$request, $response];

	}

}
