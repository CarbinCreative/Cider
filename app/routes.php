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

/* Deny direct file access */
if(!defined('CIDER_ROOT_PATH')) exit;

/* Set up routes and app specific event listeners */

route()->get('/', function() {

  return sprintf("<h1>Cider %s</h1>", CIDER_VERSION);

});
