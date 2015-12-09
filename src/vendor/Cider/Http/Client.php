<?php
/**
 *  Cider
 *
 *  Cider is a PHP based object oriented nano-framework for building small web applications.
 *
 *  @author Carbin Creative <hej@carbin.se>
 *  @license http://opensource.org/licenses/MIT MIT
 */

/* @namespace Http */
namespace Cider\Http;

/* Deny direct file access */
if(!defined('CIDER_ROOT_PATH')) exit;

/**
 *  Client
 *
 *  HTTP client.
 *
 *  @vendor Cider
 *  @package Http
 *
 *  @version 1.0.0
 *
 *  @author Carbin Creative <hej@carbin.se>
 */
class Client {

  /* @uses */
  use \Cider\Event\Emitter;

  /**
   *  @const string HTTP_1_0
   */
  const HTTP_1_0 = 'HTTP/1.0';

  /**
   *  @const string HTTP_1_1
   */
  const HTTP_1_1 = 'HTTP/1.1';

  /**
   *  @const string HTTP_2_0
   */
  const HTTP_2_0 = 'HTTP/2.0';

  /**
   *  @const array HTTP_STATUS_CODES
   */
  const HTTP_STATUS_CODES = [
    100 => 'Continue',
    101 => 'Switching Protocols',
    200 => 'OK',
    201 => 'Created',
    202 => 'Accepted',
    203 => 'Non-Authoritative Information',
    204 => 'No Content',
    205 => 'Reset Content',
    206 => 'Partial Content',
    300 => 'Multiple Choices',
    301 => 'Moved Permanently',
    302 => 'Found',
    303 => 'See Other',
    304 => 'Not Modified',
    305 => 'Use Proxy',
    307 => 'Temporary Redirect',
    400 => 'Bad Request',
    401 => 'Unauthorized',
    402 => 'Payment Required',
    403 => 'Forbidden',
    404 => 'Not Found',
    405 => 'Method Not Allowed',
    406 => 'Not Acceptable',
    407 => 'Proxy Authentication Required',
    408 => 'Request Timeout',
    409 => 'Conflict',
    410 => 'Gone',
    411 => 'Length Required',
    412 => 'Precondition Failed',
    413 => 'Request Entity Too Large',
    414 => 'Request-URI Too Long',
    415 => 'Unsupported Media Type',
    416 => 'Requested Range Not Satisfiable',
    417 => 'Expectation Failed',
    418 => "I'm A Teapot",
    429 => 'Too Many Requests',
    500 => 'Internal Server Error',
    501 => 'Not Implemented',
    502 => 'Bad Gateway',
    503 => 'Service Unavailable',
    504 => 'Gateway Timeout',
    505 => 'HTTP Version Not Supported',
    509 => 'Bandwidth Limit Exceeded',
    701 => 'Meh',
    719 => 'I Am Not A Teapot',
    732 => 'Fucking Unic💩de',
    740 => 'Computer Says No',
    748 => 'Confounded By Ponies',
    749 => 'Reserved For Chuck Norris',
    763 => 'Under-Caffeinated',
    764 => 'Over-Caffeinated',
    793 => 'Zombie Apocalypse'
  ];

  /**
   *  @const array HTTP_STATUS_TYPES
   */
  const HTTP_STATUS_TYPES = [
    1 => 'Informational',
    2 => 'Success',
    3 => 'Redirect',
    4 => 'Client Error',
    5 => 'Server Error',
    7 => 'Developer Error'
  ];

  /**
   *  @const array HTTP_REQUEST_METHODS
   */
  const HTTP_REQUEST_METHODS = [
    'HEAD',
    'OPTIONS',
    'GET',
    'POST',
    'PUT',
    'PATCH',
    'DELETE',
    'TRACE',
    'CONNECT'
  ];

  /**
   *  @var string $httpProtocol HTTP protocol version.
   */
  protected $httpProtocol = self::HTTP_1_1;

  /**
   *  @var int $httpStatusCode
   */
  protected $httpStatusCode;

  /**
   *  @var string $httpStatusType
   */
  protected $httpStatusType;

  /**
   *  @var array $httpHeaders
   */
  protected $httpHeaders = [];

  /**
   *  @var string $requestMethod
   */
  protected $requestMethod;

  /**
   *  @var array $requestData
   */
  protected $requestData = [];

  /**
   *  setProtocol
   *
   *  Set current HTTP protocol for this context.
   *
   *  @param string $httpProtocol
   *
   *  @return bool
   */
  public function setProtocol(String $httpProtocol):Bool {

    $validHttpProtocols = [
      self::HTTP_1_0,
      self::HTTP_1_1,
      self::HTTP_2_0
    ];

    if(in_array($httpProtocol, $validHttpProtocols) === true) {

      $this->httpProtocol = $httpProtocol;

      return true;

    }

    return false;

  }

  /**
   *  getProtocol
   *
   *  Returns context HTTP protocol.
   *
   *  @return string
   */
  public function getProtocol():String {

    return $this->httpProtocol;

  }

  /**
   *  setStatusCode
   *
   *  Sets context status code.
   *
   *  @param int $httpStatusCode
   *
   *  @return bool
   */
  public function setStatusCode(Int $httpStatusCode = null):Bool {

    if($httpStatusCode === null) {

      $httpStatusCode = http_response_code();

    }

    if(in_array($httpStatusCode, array_keys(self::HTTP_STATUS_CODES)) === true) {

      $this->httpStatusCode = $httpStatusCode;

      return true;

    }

    return false;

  }

  /**
   *  getStatusCode
   *
   *  Returns context status code.
   *
   *  @return int
   */
  public function getStatusCode():Int {

    return $this->httpStatusCode;

  }

  /**
   *  statusCodeAsInt
   *
   *  Alias for {@see Cider\Http\Client::getStatusCode}.
   *
   *  @return int
   */
  public function statusCodeAsInt():Int {

    return $this->getStatusCode();

  }

  /**
   *  statusCodeAsString
   *
   *  Returns status code string.
   *
   *  @param int $httpStatusCode
   *
   *  @return string
   */
  public function statusCodeAsString(Int $httpStatusCode = null):String {

    if($httpStatusCode === null) {

      $httpStatusCode = $this->httpStatusCode;

    }

    if(array_key_exists($httpStatusCode, self::HTTP_STATUS_CODES) === true) {

      return self::HTTP_STATUS_CODES[$httpStatusCode];

    }

    return '';

  }

  /**
   *  statusTypeAsString
   *
   *  Returns status type, i.e. "Informational" or "Redirect".
   *
   *  @param int $httpStatusCode
   *
   *  @return string
   */
  public function statusTypeAsString(Int $httpStatusCode = null):String {

    if($httpStatusCode === null) {

      $httpStatusCode = $this->httpStatusCode;

    }

    $httpStatusType = floor($httpStatusCode / 100);

    if(array_key_exists($httpStatusType, self::HTTP_STATUS_TYPES) === true) {

      return self::HTTP_STATUS_TYPES[$httpStatusType];

    }

    return '';

  }

  /**
   *  statusAsString
   *
   *  Returns full context status code.
   *
   *  @return string
   */
  public function statusAsString():String {

    return implode(' ', [
      $this->getProtocol(),
      $this->statusCodeAsInt(),
      $this->statusCodeAsString()
    ]);

  }

  /**
   *  validRequestMethod
   *
   *  Returns boolean whether or not input string is a valid HTTP request method.
   *
   *  @param string $httpRequestMethod
   *
   *  @return bool
   */
  public function validRequestMethod(String $httpRequestMethod):Bool {

    return in_array(strtoupper($httpRequestMethod), self::HTTP_REQUEST_METHODS);

  }

  /**
   *  setRequestMethod
   *
   *  Sets context request method, or captures request method if no request method parameter is passed.
   *
   *  @param string $httpRequestMethod
   *
   *  @return string
   */
  public function setRequestMethod(String $httpRequestMethod = null):Bool {

    $captureMethodOverride = false;

    if($httpRequestMethod === null) {

      $httpRequestMethod = $_SERVER['REQUEST_METHOD'];

      $captureMethodOverride = true;

    }

    $httpRequestMethod = strtoupper($httpRequestMethod);

    if($captureMethodOverride === true && empty($_POST) === false && array_key_exists('_METHOD', $_POST) === true) {

      $httpRequestMethod = strtoupper($_POST['_METHOD']);

    }

    if($this->validRequestMethod($httpRequestMethod) === true) {

      $this->requestMethod = $httpRequestMethod;

      return true;

    }

    return false;

  }

  /**
   *  getRequestMethod
   *
   *  Returns context request method.
   *
   *  @return string
   */
  public function getRequestMethod():String {

    return $this->requestMethod;

  }

  /**
   *  sanitizeParameter
   *
   *  Sanitizes parameter value, uses {@man filter_var}.
   *
   *  @param mixed $parameterValue
   *  @param string $parameterType
   *
   *  @return string
   */
  public function sanitizeParameter($parameterValue, String $parameterType = null):String {

    if($parameterType === null) {

      $parameterType = 'string';

    }

    $parameterType = strtolower($parameterType);

    $filterType = FILTER_SANITIZE_STRING;
    $filterFlags = null;

    switch($parameterType) {

      case 'url' :
        $filterType = FILTER_SANITIZE_URL;
        break;

      case 'int' :
        $filterType = FILTER_SANITIZE_NUMBER_INT;
        break;

      case 'float' :
        $filterType = FILTER_SANITIZE_NUMBER_FLOAT;
        $filterFlags = FILTER_FLAG_ALLOW_FRACTION | FILTER_FLAG_ALLOW_THOUSAND;
        break;

      case 'email' :
        $filterType = FILTER_SANITIZE_EMAIL;
        break;

      case 'string' :
      default :
        $filterType = FILTER_SANITIZE_STRING;
        $filterFlags = FILTER_FLAG_NO_ENCODE_QUOTES;
      break;

    }

    return filter_var($parameterValue, $filterType, $filterFlags);

  }

  /**
   *  sanitizeParameters
   *
   *  Sanitizes multiple parameters via {@see Cider\Http\Client::sanitizeParameter}.
   *
   *  @params mixed $parameterValues, ...
   *
   *  @return void
   */
  public function sanitizeParameters(...$parameterValues) {

    return array_map([$this, 'sanitizeParameter'], $parameterValues);

  }

  /**
   *  setParameter
   *
   *  Sanitizes and sets parameter to input or context HTTP request method.
   *
   *  @param string $parameterName
   *  @param mixed $parameterValue
   *  @param string $parameterType
   *  @param string $httpRequestMethod
   *
   *  @return bool
   */
  public function setParameter(String $parameterName, $parameterValue, String $parameterType = null, String $httpRequestMethod = null):Bool {

    if($httpRequestMethod === null) {

      $httpRequestMethod = $this->getRequestMethod();

    }

    if($this->validRequestMethod($httpRequestMethod) === true) {

      if(is_array($this->requestData[$httpRequestMethod]) === false) {

        $this->requestData[$httpRequestMethod] = [];

      }

      $parameterName = $this->sanitizeParameter($parameterName, 'string');

      $this->requestData[$httpRequestMethod][$parameterName] = $this->sanitizeParameter($parameterValue, $parameterType);

      return true;

    }

    return false;

  }

  /**
   *  getParameter
   *
   *  Returns parameter value of input or context HTTP request method.
   *
   *  @param string $parameterName
   *  @param string $httpRequestMethod
   *
   * @return mixed
   */
  public function getParameter(String $parameterName, String $httpRequestMethod = null) {

    if($httpRequestMethod === null) {

      $httpRequestMethod = $this->getRequestMethod();

    }

    if($this->validRequestMethod($httpRequestMethod) === true) {

      if(is_array($this->requestData[$httpRequestMethod]) === true) {

        $parameterName = $this->sanitizeParameter($parameterName, 'string');

        if(array_key_exists($parameterName, $this->requestData[$httpRequestMethod]) === true) {

          $this->requestData[$httpRequestMethod][$parameterName];

        }

        return null;

      }

      return null;

    }

    return null;

  }

  /**
   *  isSuccess
   *
   *  Returns true if HTTP status code is 2xx {@link http://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html#sec10.2}.
   *
   *  @return bool
   */
  public function isSuccess():Bool {

    return (intval(floor($this->statusCodeAsInt() / 100)) === 2);

  }

  /**
   *  isRedirect
   *
   *  Returns true if HTTP status code is 3xx {@link http://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html#sec10.3}.
   *
   *  @return bool
   */
  public function isRedirect():Bool {

    return (intval(floor($this->statusCodeAsInt() / 100)) === 3);

  }

  /**
   *  isClientError
   *
   *  Returns true if HTTP status code is 4xx {@link http://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html#sec10.4}.
   *
   *  @return bool
   */
  public function isClientError():Bool {

    return (intval(floor($this->statusCodeAsInt() / 100)) === 4);

  }

  /**
   *  isServerError
   *
   *  Returns true if HTTP status code is 5xx {@link http://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html#sec10.5}.
   *
   *  @return bool
   */
  public function isServerError():Bool {

    return (intval(floor($this->statusCodeAsInt() / 100)) === 5);

  }

  /**
   *  isDeveloperError
   *
   *  Returns true if HTTP status code is 7xx {@link https://github.com/joho/7XX-rfc}.
   *
   *  @return bool
   */
  public function isDeveloperError():Bool {

    return (intval(floor($this->statusCodeAsInt() / 100)) === 7);

  }

  /**
   *  isError
   *
   *  Returns true if HTTP status is 4xx, 5xx or 7xx.
   *
   *  @return bool
   */
  public function isError():Bool {

    return ($this->isClientError() === true || $this->isServerError() === true || $this->isDeveloperError() === true);

  }

  /**
   *  isHead
   *
   *  Returns true if HTTP request method is HEAD.
   *
   *  @return bool
   */
  public function isHead():Bool {

    return ($this->getRequestMethod() === 'HEAD');

  }

  /**
   *  isOptions
   *
   *  Returns true if HTTP request method is OPTIONS.
   *
   *  @return bool
   */
  public function isOptions():Bool {

    return ($this->getRequestMethod() === 'OPTIONS');

  }

  /**
   *  isGet
   *
   *  Returns true if HTTP request method is GET.
   *
   *  @return bool
   */
  public function isGet():Bool {

    return ($this->getRequestMethod() === 'GET');

  }

  /**
   *  isPost
   *
   *  Returns true if HTTP request method is POST.
   *
   *  @return bool
   */
  public function isPost():Bool {

    return ($this->getRequestMethod() === 'POST');

  }

  /**
   *  isPut
   *
   *  Returns true if HTTP request method is PUT.
   *
   *  @return bool
   */
  public function isPut():Bool {

    return ($this->getRequestMethod() === 'PUT');

  }

  /**
   *  isPatch
   *
   *  Returns true if HTTP request method is PATCH.
   *
   *  @return bool
   */
  public function isPatch():Bool {

    return ($this->getRequestMethod() === 'PATCH');

  }

  /**
   *  isDelete
   *
   *  Returns true if HTTP request method is DELETE.
   *
   *  @return bool
   */
  public function isDelete():Bool {

    return ($this->getRequestMethod() === 'DELETE');

  }

  /**
   *  isTrace
   *
   *  Returns true if HTTP request method is TRACE.
   *
   *  @return bool
   */
  public function isTrace():Bool {

    return ($this->getRequestMethod() === 'TRACE');

  }

  /**
   *  isConnect
   *
   *  Returns true if HTTP request method is CONNECT.
   *
   *  @return bool
   */
  public function isConnect():Bool {

    return ($this->getRequestMethod() === 'CONNECT');

  }

  /**
   *  isAjax
   *
   *  Returns true if X-Requested-With header is sent.
   *
   *  @return bool
   */
  public function isAjax():Bool {

    return (empty($_SERVER['HTTP_X_REQUESTED_WITH']) === false && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest');

  }

  /**
   *  resolve
   *
   *  Resolves current request and hydrates request data.
   *
   *  @emits "resolving"
   *  @emits "resolved"
   *
   *  @return void
   */
  public function resolve() {

    $this->emit('resolving');

    $this->setStatusCode();

    $this->setRequestMethod();

    $requestMethods = array_merge(self::HTTP_REQUEST_METHODS, ['RAW']);

    foreach($requestMethods as $requestMethod) {

      if(array_key_exists($requestMethod, $this->requestData) === false) {

        $this->requestData[$requestMethod] = [];

      }

      switch($requestMethod) {
        case 'RAW' :
          $this->requestData[$requestMethod] = array_merge($this->requestData[$requestMethod], $_REQUEST);
          break;

        case 'GET' :
          $this->requestData[$requestMethod] = array_merge($this->requestData[$requestMethod], $_GET);
          break;

        case 'POST' :
          $this->requestData[$requestMethod] = array_merge($this->requestData[$requestMethod], $_POST);
          break;

        default :
          parse_str(file_get_contents('php://input'), $requestData);
          $this->requestData[$requestMethod] = array_merge($this->requestData[$requestMethod], $requestData);
        break;
      }

    }

    $this->httpHeaders = array_merge($this->httpHeaders, getallheaders());

    $this->emit('resolved');

  }

  /**
   *  observeHttpStatusChanges
   *
   *  Emits events associated with HTTP status changes.
   *
   *  @emits "success"
   *  @emits "error"
   *  @emits "redirect"
   *  @emits HTTP status code
   *
   *  @return void
   */
  protected function observeHttpStatusChanges() {

    if($this->isSuccess() === true) {

      $this->emit('success');

    } else if($this->isError() === true) {

      $this->emit('error');

    } else if($this->isRedirect() === true) {

      $this->emit('redirect');

    }

    $this->emit("{$this->statusCodeAsInt()}");

  }

  /**
   *  validHeader
   *
   *  Validates header name.
   *
   *  @param string $headerName
   *
   *  @return bool
   */
  public function validHeader(String $headerName):Bool {

    return (preg_match("/^[a-zA-Z0-9-]+$/", $headerName) !== false);

  }

  /**
   *  setHeader
   *
   *  Sets context HTTP header.
   *
   *  @param string $headerName
   *  @param string $headerValue
   *
   *  @return bool
   */
  public function setHeader(String $headerName, String $headerValue):Bool {

    if($this->validHeader($headerName) === true) {

      $this->httpHeaders[$headerName] = $headerValue;

      return true;

    }

    return false;

  }

  /**
   *  getHeader
   *
   *  Returns context HTTP header if exists.
   *
   *  @param string $headerName
   *
   *  @return string
   */
  public function getHeader(String $headerName):String {

    if(array_key_exists($headerName, $this->httpHeaders) === true) {

      return $this->httpHeaders[$headerName];

    }

    return '';

  }

  /**
   *  setHeaders
   *
   *  Sets multiple headers at once.
   *
   *  @param array $httpHeaders
   *
   *  @return bool
   */
  public function setHeaders(Array $httpHeaders):Bool {

    if(count($httpHeaders) > 0) {

      foreach($httpHeaders as $headerName => $headerValue) {

        $this->setHeader($headerName, $headerValue);

      }

      return true;

    }

    return false;

  }

  /**
   *  getHeaders
   *
   *  Returns all headers.
   *
   *  @return array
   */
  public function getHeaders() {

    return $this->httpHeaders;

  }

  /**
   *  sendHeaders
   *
   *  Sends context HTTP status headers and headers to client.
   *
   *  @return void
   */
  public function sendHeaders() {

    header($this->statusAsString(), true, $this->statusCodeAsInt());

    foreach($this->getHeaders() as $headerName => $headerValue) {

      header("{$headerName}: {$headerValue}");

    }

    $this->observeHttpStatusChanges();

  }

  /**
   *  send
   *
   *  Alias method for {@see Cider\Http\Client::setStatusCode}.
   *
   *  @param int $httpStatusCode
   *
   *  @return void
   */
  public function send(Int $httpStatusCode) {

    $this->setStatusCode($httpStatusCode);

  }

  /**
   *  redirect
   *
   *  Sends a redirect status.
   *
   *  @param string $redirectUrl
   *  @param int $redirectStatusCode
   *
   *  @return void
   */
  public function redirect(String $redirectUrl, Int $redirectStatusCode = 302) {

    if(strlen($redirectUrl) > 0) {

      header("Location: {$redirectUrl}", true, $redirectStatusCode);
      exit;

    }

  }

}
