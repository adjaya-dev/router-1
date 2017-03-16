# Atanvarno\Router
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](https://github.com/atanvarno69/router/blob/master/LICENSE)
[![Latest Version](https://img.shields.io/github/release/atanvarno69/router.svg?style=flat-square)](https://github.com/atanvarno69/router/releases)
[![Build Status](https://img.shields.io/travis/atanvarno69/router/master.svg?style=flat-square)](https://travis-ci.org/atanvarno69/router)
[![Coverage Status](https://img.shields.io/coveralls/atanvarno69/router/master.svg?style=flat-square)](https://coveralls.io/r/atanvarno69/router?branch=master)

A [PSR-7](http://www.php-fig.org/psr/psr-7/) wrapper for [FastRoute](https://github.com/nikic/FastRoute).

## Requirements
**PHP >= 7.0** is required, but the latest stable version of PHP is recommended.

## Installation
```bash
$ composer require atanvarno/router:^0.1.0
```

## Usage
Atanvarno\Router comes with two concrete `Router` implementations: `SimpleRouter` and `CachedRouter`.

### Instantiation
```php
use Atanvarno\Router\{Router, SimpleRouter};

$router = new SimpleRouter();
```
The constructor optionally accepts a driver as its first parameter, using a constant from the `Router` interface, one of:
* `Router::CHAR_COUNT`
* `Router::GROUP_COUNT` (this is the default value)
* `Router::GROUP_POS`
* `Router::MARK`
```php
$router = new SimpleRouter(Router::CHAR_COUNT);
```

### Defining routes
Routes are defined by calling the `add()` method on a `Router` instance:
```php
$router->add($method, $pattern, $handler);
```
The `$method` is an uppercase HTTP method string for which a certain route should match. It is possible to specify multiple valid methods using an array. There is a `Router::METHOD_*` constant for each valid HTTP method.
```php
// These two calls
$router->add(Router::METHOD_GET, '/test', 'handler');
$router->add(Router::METHOD_POST, '/test', 'handler');
// Are equivalent to this one call
$router->add([Router::METHOD_GET, Router::METHOD_POST], '/test', 'handler');
```

By default the `$Pattern` uses a syntax where `{foo}` specifies a placeholder  with name `foo` and matching the regex `[^/]+`. To adjust the pattern the placeholder matches, you can specify a custom pattern by writing `{bar:[0-9]+}`. Some examples:

```php
// Matches /user/42, but not /user/xyz
$router->add(Router::METHOD_GET, '/user/{id:\d+}', 'handler');

// Matches /user/foobar, but not /user/foo/bar
$router->add(Router::METHOD_GET, '/user/{name}', 'handler');

// Matches /user/foo/bar as well
$router->add(Router::METHOD_GET, '/user/{name:.+}', 'handler');
```

Custom patterns for route placeholders cannot use capturing groups. For example `{lang:(en|de)}` is not a valid placeholder, because `()` is a capturing group. Instead you can use either `{lang:en|de}` or `{lang:(?:en|de)}`.

Furthermore parts of the route enclosed in `[...]` are considered optional, so that `/foo[bar]` will match both `/foo` and `/foobar`. Optional parts are only supported in a trailing position, not in the middle of a route.

```php
// This route
$router->add(Router::METHOD_GET, '/user/{id:\d+}[/{name}]', 'handler');
// Is equivalent to these two routes
$router->add(Router::METHOD_GET, '/user/{id:\d+}', 'handler');
$router->add(Router::METHOD_GET, '/user/{id:\d+}/{name}', 'handler');

// Multiple nested optional parts are possible as well
$router->add(Router::METHOD_GET, '/user[/{id:\d+}[/{name}]]', 'handler');

// This route is NOT valid, because optional parts can only occur at the end
$router->add(Router::METHOD_GET, '/user[/{id:\d+}]/{name}', 'handler');
```

The `$handler` parameter does not necessarily have to be a callback, it could also be a controller class name or any other kind of data you wish to associate with the route. Router only tells you which handler corresponds to your URI, how you interpret it is up to you.

#### Shorcut methods for all request methods

For all the valid HTTP request methods shortcut methods are available. For example:
```php
$router->get('/get-route', 'get_handler');
$router->post('/post-route', 'post_handler');
// Is equivalent to:
$router->add(Router::METHOD_GET, '/get-route', 'get_handler');
$router->add(Router::METHOD_POST, '/post-route', 'post_handler');
```

#### Constructor injection
Route information can be injected into the `Router` instance constructor as its second parameter. This parameter accepts an array of arrays containing `$method`, `$pattern` and `$handler` values, like a call to `add()`.
```php
$routes = [
    [Router::METHOD_GET, '/user[/{id:\d+}[/{name}]]', 'handler'],
    [Router::METHOD_PATCH, '/table/{tid}/{uid}/{data}', 'handler'],
    // ...
];

$router = new SimpleRouter(Router::GROUP_COUNT, $routes);
```

This allows routes to be held in a separate configuration file that returns such an array:
```php
<?php // routes.php
use Atanvarno\Router\Router;

return [
    [Router::METHOD_GET, '/user[/{id:\d+}[/{name}]]', 'handler'],
    [Router::METHOD_PATCH, '/table/{tid}/{uid}/{data}', 'handler'],
    //...
];
```
```php
<?php // main.php
use Atanvarno\Router\{Router, SimpleRouter};

$router = new SimpleRouter(Router::GROUP_COUNT, include 'path/to/routes.php');

// ...
```

#### Route groups
[Todo]

### Caching
Instead of using `SimpleRouter` you can use `CachedRouter`. 
`CachedRouter::__construct()` takes parameters:
* `$cache`, a [PSR-16 `CacheInterface`]() instance.
* `$cacheKey`, a `string` key to use for router data. This defaults to `'routerData''`.
* `$driver`, as `SimpleRouter`. This defaults to `Router::GROUP_COUNT`.
* `$routes`, an `array` of routes, see [Constructor injection](#constructor-injection).
* `$cacheDisabled`, a `bool` value. This defaults to `false`. Passing `true` will make `CachedRouter` ignore the cache and behave like `SimpleRouter` which dispatching.

By default, `CachedRouter` will take its dispatch data directly from the cache and bypass and routes defined by `add()` calls or constructor injection. Where no dispatch data is available (for example on the first `dispatch()` call or if the cache data has expired) `CachedRouter` will generate dispatch data from the defined routes and store it in the cache.

If your route configuration has changed and you need to update the dispatch data in the cache, call `refreshCache()`.

### Dispatching
A request is dispatched by calling the `dispatch()` method of the `Router` instance. This method accepts a [PSR-7 `RequestInterface`](http://www.php-fig.org/psr/psr-7/#psrhttpmessagerequestinterface) instance.

The `dispatch()` method returns an array whose first element contains a status code. It is one of `FastRoute\Dispatcher::NOT_FOUND`, `FastRoute\Dispatcher::METHOD_NOT_ALLOWED` and `FastRoute\Dispatcher::FOUND`. For the method not allowed status the second array element contains a list of HTTP methods allowed for the supplied URI. For example:
```php
[FastRoute\Dispatcher::METHOD_NOT_ALLOWED, [Router::METHOD_GET,Router::METHOD_POST]]
```
> **NOTE:** The HTTP specification requires that a `405 Method Not Allowed` response include the `Allow:` header to detail available methods for the requested resource. Applications using Router should use the second array element to add this header when relaying a 405 response.

For the found status the second array element is the handler that was associated with the route and the third array element is a dictionary of placeholder names to their values. For example:
```php
// Routing against GET /user/nikic/42
[FastRoute\Dispatcher::FOUND, 'handler0', ['name' => 'nikic', 'id' => '42']]
```

### A Note on HEAD Requests
The HTTP specification requires servers to [support both GET and HEAD methods](http://www.w3.org/Protocols/rfc2616/rfc2616-sec5.html#sec5.1.1):

> The methods GET and HEAD MUST be supported by all general-purpose servers

To avoid forcing users to manually register HEAD routes for each resource we fallback to matching an available GET route for a given resource. Applications MAY always specify their own HEAD method route for a given resource to bypass this behavior entirely.
## Full API
See [API](https://github.com/atanvarno69/router/blob/master/docs/API.md).
