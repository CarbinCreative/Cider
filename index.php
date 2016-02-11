<?php
/**
 *  Cider
 *
 *  Cider is a PHP based object oriented nano-framework for building small web applications.
 *
 *  @author Carbin Creative <hej@carbin.se>
 *  @license http://opensource.org/licenses/MIT MIT
 */

/* @namespace Cider */
namespace Cider;

/* @imports */
use Cider\Exceptions\FrameworkException;

/**
 *  @const string NAMESPACE_SEPARATOR
 */
define('NAMESPACE_SEPARATOR', '\\');

/**
 *  @const string CIDER_ROOT_PATH
 */
define('CIDER_ROOT_PATH', __DIR__ . DIRECTORY_SEPARATOR);

/* Set error handling */
error_reporting(E_ALL ^ E_NOTICE);
ini_set('display_erros', 'On');

/* Output buffer */
$outputBuffer = null;

/* Run the application */
try {

	/* Load version file */
	require_once CIDER_ROOT_PATH . 'version.php';

	/* Load Cider bootstrap */
	require_once implode(DIRECTORY_SEPARATOR, [
		rtrim(CIDER_ROOT_PATH, DIRECTORY_SEPARATOR),
		'src', 'vendor', 'Cider', 'bootstrap.php'
	]);

	ob_start();

	/* Resolve HTTP request */
	app()->http->resolve();

	/* Load optional app bootstrap */
	relativeRequire('app/bootstrap');

	/* Load routes */
	relativeRequire('app/routes');

	/* @emits "rendering" */
	app()->signal('rendering');

	/* Dispatch current request URI */
	echo dispatcher()->dispatch(uri());

	/* Capture buffer */
	$outputBuffer = ob_get_clean();

} catch (\Exception $exception) {

	/* Dispatch generic exceptions */
	$outputBuffer = dispatcher()->dispatchError($exception);

} finally {

	/* Send HTTP headers */
	app()->http->sendHeaders();

	/* Send output */
	echo $outputBuffer;

	/* @emits "rendered" */
	app()->signal('rendered');

}
