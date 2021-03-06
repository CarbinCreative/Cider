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
 *  Map
 *
 *  Route dispatcher.
 *
 *  @vendor Cider
 *  @package Delegation
 *
 *  @version 1.0.0
 *
 *  @author Carbin Creative <hej@carbin.se>
 */
class Dispatcher {

	/* @mixins */
	use \Cider\Event\Emitter;

	/**
	 *  @var \Cider\Http\Client $httpClient
	 */
	protected $httpClient;

	/**
	 *  @var \Cider\Delegation\RouteMap $routeMap
	 */
	protected $routeMap;

	/**
	 *  Constructor
	 *
	 *  Sets Http client and route maps instances.
	 *
	 *  @param \Cider\Http\Client $httpClient
	 *  @param \Cider\Delegation\RouteMap $routeMap
	 *
	 *  @return void
	 */
	public function __construct(HttpClient $httpClient, RouteMap $routeMap) {

		$this->httpClient = $httpClient;

		$this->routeMap = $routeMap;

	}

	/**
	 *  requestMethod
	 *
	 *  Returns current request method.
	 *
	 *  @return string
	 */
	protected function requestMethod():String {

		return $this->httpClient->getRequestMethod();

	}

	/**
	 *  routePaths
	 *
	 *  Returns all registered route paths.
	 *
	 *  @return array
	 */
	protected function routePaths():Array {

		return $this->routeMap->paths();

	}

	/**
	 *  dispatch
	 *
	 *  Invokes route path if a match is found.
	 *
	 *  @param string $requestUri
	 *
	 *  @emits "dispatching"
	 *  @emits "dispatched"
	 *  @emits "missingRoute"
	 *
	 *  @return string
	 */
	public function dispatch(String $requestUri):String {

		$requestMethod = $this->requestMethod();

		$routePaths = $this->routePaths();

		$foundMatch = false;

		$this->emit('dispatching');

		foreach($routePaths as $routePathPattern => $routeMethodPaths) {

			if(array_key_exists($requestMethod, $routeMethodPaths) === true) {

				$routePath = $routeMethodPaths[$requestMethod];

				if($routePath->matches($requestUri) === true) {

					list($request, $response) = $this->routeMap->invokeBeforeCallback($this->httpClient, '');

					if(count($this->routeMap->getMiddlewares()) > 0) {

						list($request, $response) = $this->routeMap->invokeMiddlewares($request, $response);

					}

					$response = $routePath->invoke($response);

					list($request, $response) = $this->routeMap->invokeAfterCallback($request, $response ?? '');

					$foundMatch = true;

					break;

				}

			}

		}

		if($foundMatch === false) {

			$this->emit('missingRoute');

			$this->httpClient->send(404);

			return $this->routeMap->handleMissingRoute();

		}

		$this->emit('dispatched');

		return $response ?? '';

	}

	/**
	 *  dispatchError
	 *
	 *  Dispatches error route.
	 *
	 *  @param Cider\Exceptions\FrameworkException $exception
	 *  @param int $errorStatusCode
	 *
	 *  @return string
	 */
	public function dispatchError(FrameworkException $exception, Int $errorStatusCode = 500):String {

		$this->emit('errorRoute');

		$this->httpClient->send($errorStatusCode);

		return $this->routeMap->handleErrorRoute();

	}

}
