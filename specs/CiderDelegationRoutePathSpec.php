<?php
namespace Cider\Spec;

/* @imports*/
use function \Cider\import;
use function \Cider\expect;

import('Cider\Delegation\RoutePath');

describe('Cider\Delegation\RoutePath', function() {

  $routeHandler = function() {

    return 'Hello World';

  };

  $route = new \Cider\Delegation\RoutePath('/greet', $routeHandler);

  beforeEach(function() use ($route) {

    $route->flushCallbacks();

  });

  it('expects defined pattern to be /greet', function() use ($route) {

    return expect($route->path())->toEqual('/greet');

  });

  it('expects pattern to match URI /greet', function() use ($route) {

    return expect($route->matches('/greet'))->toBeTrue();

  });

  it('has no middlewares attached', function() use ($route) {

    return expect($route->numberOfMiddlewares())->toEqual(0);

  });

  it('can attach at least one middleware', function() use ($route) {

    $initialMiddlewareCount = $route->numberOfMiddlewares();
    $route->middleware(function($request, $response) {});
    $hasIncrementedMiddlewareCount = $route->numberOfMiddlewares() > $initialMiddlewareCount;

    return expect($hasIncrementedMiddlewareCount)->toBeTrue();

  });

  it('can cannot attach more than one middleware', function() use ($route) {

    $route->setMaxMiddlewares(1);

    $route
      ->middleware(function() {})
      ->middleware(function() {});

    return expect($route->numberOfMiddlewares())->toEqual(1);

  });

  it('can invoke route handler', function() use ($route) {

    $response = $route->invoke();

    return expect($response)->toEqual('Hello World');

  });

  it('can invoke route middleware', function() use ($route) {

    $route->middleware(function($request, $response) {

      // Override response
      return [$request, 'Hejsan Världen'];

    });

    $response = $route->invoke();

    return expect($response)->toEqual('Hejsan Världen');

  });

  it('can invoke before route handler', function() use ($route) {

    $route->before(function($request, $response) {

      return [$request, "$ "];

    });

    $response = $route->invoke();

    return expect($response)->toEqual('$ Hello World');

  });

  it('can invoke after route handler', function() use ($route) {

    $route->after(function($request, $response) {

      return [$request, "{$response}!"];

    });

    $response = $route->invoke();

    return expect($response)->toEqual('Hello World!');

  });

  it('should have named parameters', function() use ($routeHandler) {

    $namedRoute = new \Cider\Delegation\RoutePath("/users/:user", $routeHandler);
    $namedRoute->matches("/users/bruce.wayne");

    return expect($namedRoute->parameterExists('user'))->toBeTrue();

  });

  it('should set named parameter to "bruce.wayne"', function() use ($routeHandler) {

    $namedRoute = new \Cider\Delegation\RoutePath("/users/:user", $routeHandler);
    $namedRoute->matches("/users/bruce.wayne");

    return expect($namedRoute->parameterMatches('user', 'bruce.wayne'))->toBeTrue();

  });

  it('can match route with conditions', function() use ($routeHandler) {

    $conditionRoute = new \Cider\Delegation\RoutePath("/users/:user", $routeHandler);
    $conditionRoute->condition('user', '([a-z\.]{1,})');
    $hasRouteMatch = $conditionRoute->matches("/users/bruce.wayne");

    return expect($hasRouteMatch)->toBeTrue();

  });

  it('cannot match route to invalid condition', function() use ($routeHandler) {

    $conditionRoute = new \Cider\Delegation\RoutePath("/users/:user", $routeHandler);
    $conditionRoute->condition('user', '([a-z\.]{1,})');
    $hasRouteMatch = $conditionRoute->matches("/users/brewz-weyn");

    return expect($hasRouteMatch)->toBeFalse();

  });

});
