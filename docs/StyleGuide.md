[RFC 2119]: http://www.ietf.org/rfc/rfc2119.txt

# Cider Style Guide & Best Practices

_The key words "MUST", "MUST NOT", "REQUIRED", "SHALL", "SHALL NOT", "SHOULD", "SHOULD NOT", "RECOMMENDED", "MAY", and "OPTIONAL" in this document are to be interpreted as described in [RFC 2119][]._

This document serves as a guideline for developers contributing and/or using Cider in production. Third party libraries are excluded from this guide.

* Framework specific code from contributors **MUST** follow this guide.
* Application specific code from framework usage **SHOULD** follow this guide.


---


## General Guidelines


### Indentation

* Documents **MUST** be indented with _hard tabs_.
* Spaces **MAY** be used for _alignment_.


### Case sensitivity

* Variable and function names **MUST** be camel cased. I.e `$variableName`.
	* `protected` and `private` variables **MAY** use a single underscore as prefix.
* Classes, Abstracts, Interfaces and Traits **MUST** be camel cased with capital first letter. I.e `ClassName`.


### Quotes

* All strings **MUST** use single quotes. I.e `'Hello World!'`.
* Interpolated strings **MUST** use double quotes. I.e `"Hello, {$name}!"`.


### Type hinting

* All user defined functions and method definitions **MUST** be type hinted.

```php
function setName(String $firstName, String $lastName):Bool {}
```


### Namespace

* Namespaces **MUST** map file path, including directories.
	* `src/vendor` is omitted from namespaces. I.e `Cider\Route\Path` maps `{rootPath}/src/vendor/Cider/Route/Path.php`.
* Namespaces **SHOULD** be in singular form. I.e `Cider\Route\Path`.


---


## Functions & Statements

* A space **MUST** supersede closing parentheses.
* Opening bracket **MUST** be on the same row as the function declaration or statement.

#### Acceptable

```php
function resolvePathName(String $unresolvedPathName):String {
	/* ... */
}
```

#### Not acceptable

```php
function resolvePathName($unresolvedPathName)
{
	/* ... */
}
```

### Single line statements

* Single line statements **MUST NOT** be used, with the exception that _single line return statements_ **MAY** be used.

#### Acceptable

```php
function emit(String $eventName, ...$eventCallbackArguments):self {
	if($this->hasEventListener($eventName) === false) return;
}
```

#### Not acceptable

```php
function emit(String $eventName, $eventCallbackArguments = null):self {
	if(is_array($eventCallbackArguments) === false) $eventCallbackArguments = [];
}
```

---


## Classes, Abstracts, Traits & Interfaces

* All Classes, Abstracts, Traits and Interfaces **MUST** have a documentation header, namespace definition and include namespaces with `use`.
* Namespaces, including relative namespaces **SHOULD** be defined outside class, trait or interface definition.

#### Class Template

```php
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

/* @imports */
use Cider\Common\Singleton;
use Cider\Common\Mutable;
use Cider\Common\Factory;
use Cider\Event\Emitter as EventEmitter;

/**
 *  Application
 *
 *  Cider application class, acts as a registry object holding Cider class instances.
 *
 *  @vendor Cider
 *  @package Core
 *
 *  @version 1.0.0
 *
 *  @author Carbin Creative <hej@carbin.se>
 */
class Application {

	/* @mixins */
	use Singleton;
	use Mutable;
	use Factory;
	use EventEmitter;

}
```
