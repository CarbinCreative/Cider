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

#### Good

```php
function resolvePathName(String $unresolvedPathName):String {
	/* ... */
}
```

#### Bad 

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