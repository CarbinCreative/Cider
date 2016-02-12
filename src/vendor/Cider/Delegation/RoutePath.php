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

	/* @mixins */
	use Hookable;

	/**
	 *  @const string ROUTE_PATTERN_DELIMITER
	 */
	const ROUTE_PATTERN_DELIMITER = '/';

	/**
	 *  @const string ROUTE_PATTERN_PARAMETER_REGEX
	 */
	const ROUTE_PATTERN_PARAMETER_REGEX = '~:([\w]+)[\+|\?]?~';

	/**
	 *  @const string ROUTE_PATTERN_DEFAULT_CAPTURE_REGEX
	 */
	const ROUTE_PATTERN_DEFAULT_CAPTURE_REGEX = '[^/]+';

	/**
	 *  @var string $routeName
	 */
	protected $routeName;

	/**
	 *  @var string $routePattern
	 */
	protected $routePattern;

	/**
	 *  @var string $routePatternRegex
	 */
	protected $routePatternRegex;

	/**
	 *  @var callable $matchCallback
	 */
	protected $matchCallback;

	/**
	 *  @var array $parameters
	 */
	protected $parameters = [];

	/**
	 *  @var array $conditions
	 */
	protected $conditions = [];

	/**
	 *  Constructor
	 *
	 *  Sets route pattern, match callback and invokes {@see \Cider\Delegation\RoutePath::update}.
	 *
	 *  @param string $routePattern
	 *  @param callable $routeMatchCallback
	 *
	 *  @return void
	 */
	public function __construct(String $routePattern, Callable $routeMatchCallback) {

		$this->routePattern = $routePattern;

		$this->matchCallback = $routeMatchCallback;

		$this->update();

	}

	/**
	 *  update
	 *
	 *  Updates route path regex.
	 *
	 *  @return void
	 */
	protected function update() {

		$this->routePatternRegex = $this->compileRoutePathPatternRegex($this->routePattern);

	}

	/**
	 *  compileRoutePathPatternRegex
	 *
	 *  Compiles a Cider specific route path into valid regex.
	 *
	 *  @param string $routePathPattern
	 *
	 *  @return string
	 */
	protected function compileRoutePathPatternRegex(String $routePathPattern):String {

		$routePathPatternRegex = preg_replace_callback(
			self::ROUTE_PATTERN_PARAMETER_REGEX,
			[$this, 'compileRouteParameterRegex'],
			str_replace(')', ')?', $routePathPattern)
		);

		if(substr($routePathPattern, -1) === self::ROUTE_PATTERN_DELIMITER) {

			$routePathPatternRegex .= '?';

		}

		$routePathPatternRegex = implode(['~^', $routePathPatternRegex, '$~i']);

		return $routePathPatternRegex;

	}

	/**
	 *  compileRouteParameterRegex
	 *
	 *  Compiles a parameter segment (:foo, :foo? or :foo+) into a named regex capture.
	 *
	 *  @param array $routeParameterMatches
	 *
	 *  @return string
	 */
	protected function compileRouteParameterRegex(Array $routeParameterMatches):String {

		list($routeParameterPattern, $routeParameterName) = $routeParameterMatches;

		$routeParameterRegex = self::ROUTE_PATTERN_DEFAULT_CAPTURE_REGEX;
		$conditionalParameter = (substr($routeParameterPattern, -1) === '?');
		$greedyMatchParameter = (substr($routeParameterPattern, -1) === '+');

		if($this->conditionExists($routeParameterName) === true) {

			$routeParameterRegex = $this->getCondition($routeParameterName);

		}

		if($greedyMatchParameter === true) {

			$routeParameterRegex = '.+';

		}

		$routeParameterRegex = "(?P<{$routeParameterName}>{$routeParameterRegex})";

		if($conditionalParameter === true) {

			$routeParameterRegex = "?(?:{$routeParameterRegex})?";

		}

		return $routeParameterRegex;

	}

	/**
	 *  name
	 *
	 *  Sets route name.
	 *
	 *  @param string $routeName
	 *
	 *  @return self
	 */
	public function name(String $routeName):self {

		$this->routeName = $routeName;

		return $this;

	}

	/**
	 *  pattern
	 *
	 *  Returns route pattern.
	 *
	 *  @return string
	 */
	public function pattern():String {

		return $this->routePattern;

	}

	/**
	 *  patternRegex
	 *
	 *  Returns route pattern regex.
	 *
	 *  @return string
	 */
	public function patternRegex():String {

		return $this->routePatternRegex;

	}

	/**
	 *  parameterExists
	 *
	 *  Validates whether or not route parameter exists.
	 *
	 *  @param string $parameterName
	 *
	 *  @return bool
	 */
	public function parameterExists(String $parameterName):Bool {

		return array_key_exists($parameterName, $this->parameters);

	}

	/**
	 *  parameterMatches
	 *
	 *  Validates if route parameter matches expected data.
	 *
	 *  @param string $parameterName
	 *  @param mixed $expectedParameterData
	 *
	 *  @return bool
	 */
	public function parameterMatches(String $parameterName, $expectedParameterData):Bool {

		if($this->parameterExists($parameterName) === true) {

			return $this->getParameter($parameterName) === $expectedParameterData;

		}

		return false;

	}

	/**
	 *  setParameter
	 *
	 *  Sets parameter to route object.
	 *
	 *  @param string $parameterName
	 *  @param mixed $parameterData
	 *
	 *  @return void
	 */
	public function setParameter(String $parameterName, $parameterData) {

		$this->parameters[$parameterName] = $parameterData;

	}

	/**
	 *  getParameter
	 *
	 *  Returns parameter value (or empty string if it doesn't exist).
	 *
	 *  @param string $parameterName
	 *
	 *  @return string|array
	 */
	public function getParameter(String $parameterName) {

		$parameterData = $this->parameters[$parameterName] ?? '';

		if(is_array($parameterData) === true && count($parameterData) === 1) {

			return $parameterData[0];

		}

		return $parameterData;

	}

	/**
	 *  conditionExists
	 *
	 *  Validates whether or not a route condition exists.
	 *
	 *  @param string $conditionName
	 *
	 *  @return bool
	 */
	public function conditionExists(String $conditionName):Bool {

		return array_key_exists($conditionName, $this->conditions);

	}

	/**
	 *  setCondition
	 *
	 *  Sets route condition.
	 *
	 *  @param string $conditionName
	 *  @param string $conditionRegex
	 *
	 *  @return void
	 */
	public function setCondition(String $conditionName, String $conditionRegex) {

		$conditionName = str_replace(':', '', $conditionName);
		$this->conditions[$conditionName] = $conditionRegex;

		$this->update();

	}

	/**
	 *  getCondition
	 *
	 *  Returns condition regex (or empty string if it doesn't exist).
	 *
	 *  @param string $conditionName
	 *
	 *  @return string
	 */
	public function getCondition(String $conditionName):String {

		if($this->conditionExists($conditionName) === true) {

			return $this->conditions[$conditionName] ?: self::ROUTE_PATTERN_DEFAULT_CAPTURE_REGEX;

		}

		return '';

	}

	/**
	 *  condition
	 *
	 *  Invokes {@see \Cider\Delegation\RoutePath::setCondition} and returns self.
	 *
	 *  @param string $conditionName
	 *  @param string $conditionRegex
	 *
	 *  @return self
	 */
	public function condition(String $conditionName, String $conditionRegex):self {

		$this->setCondition($conditionName, $conditionRegex);

		return $this;

	}

	/**
	 *  conditions
	 *
	 *  Sets multiple conditions, see {@see \Cider\Delegation\RoutePath::setCondition}.
	 *
	 *  @param array $conditions
	 *
	 *  @return self
	 */
	public function conditions(Array $conditions):self {

		foreach($conditions as $conditionName => $conditionRegex) {

			$this->setCondition($conditionName, $conditionRegex);

		}

		return $this;

	}

	/**
	 *  matches
	 *
	 *  Validates whether or not route matches request URI. Updates route parameters if true.
	 *
	 *  @param string $requestUri
	 *
	 *  @return bool
	 */
	public function matches(String $requestUri):Bool {

		if(preg_match($this->routePatternRegex, $requestUri, $requestUriMatches) !== false) {

			if(count($requestUriMatches) === 0) {

				return false;

			}

			foreach($requestUriMatches as $parameterName => $requestParameter) {

				if(is_string($parameterName) === true) {

					if(strpos(self::ROUTE_PATTERN_DELIMITER, $requestParameter) !== -1) {

						$requestParameter = explode(self::ROUTE_PATTERN_DELIMITER, $requestParameter);

					}

					$this->parameters[$parameterName] = $requestParameter;

				}

			}

			return true;

		}

		return false;

	}

	/**
	 *  invoke
	 *
	 *  Invokes route hooks, match handler and middlewares.
	 *
		*	@param string $initialResponse
	 *
	 *  @return string
	 */
	public function invoke(String $initialResponse = null):String {

		list($request, $response) = $this->invokeBeforeCallback($this, $initialResponse ?? '');

		$response .= call_user_func_array($this->matchCallback, array_merge([$request, $response], $this->parameters));

		if(count($this->middlewares) > 0) {

			list($request, $response) = $this->invokeMiddlewares($request, $response);

		}

		list($request, $response) = $this->invokeAfterCallback($request, $response ?? '');

		return $response;

	}

}
