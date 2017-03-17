# Atanvarno\Router
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](https://github.com/atanvarno69/router/blob/master/LICENSE)
[![Latest Version](https://img.shields.io/github/release/atanvarno69/router.svg?style=flat-square)](https://github.com/atanvarno69/router/releases)
[![Build Status](https://img.shields.io/travis/atanvarno69/router/master.svg?style=flat-square)](https://travis-ci.org/atanvarno69/router)
[![Coverage Status](https://img.shields.io/coveralls/atanvarno69/router/master.svg?style=flat-square)](https://coveralls.io/r/atanvarno69/router?branch=master)

A [PSR-7](http://www.php-fig.org/psr/psr-7/) wrapper for 
[FastRoute](https://github.com/nikic/FastRoute).

## Requirements
**PHP >= 7.0** is required, but the latest stable version of PHP is recommended.

## Installation
```bash
$ composer require atanvarno/router:^0.2.0
```

## Basic Usage
Two routers are provided: 
[`SimpleRouter`](https://github.com/atanvarno69/router/blob/master/docs/SimpleRouter.md) 
and 
[`CachedRouter`](https://github.com/atanvarno69/router/blob/master/docs/CachedRouter.md). 
These both implement the
[`Router`](https://github.com/atanvarno69/router/blob/master/docs/Router.md) 
interface.

### Instantiation
```php
// A simple, non-caching, router:
use Atanvarno\Router\SimpleRouter;
$router = new SimpleRouter();

// A caching router:
use Atanvarno\Router\CachedRouter;
$router = new CachedRouter();
```
By default, the `GroupCountBased` [FastRoute](https://github.com/nikic/FastRoute) 
driver is used. Other drivers may be specified in the router constructor 
using the `Router::DRIVER_*` constants.

See 
[SimpleRouter::__construct()](https://github.com/atanvarno69/router/blob/master/docs/SimpleRouter.md#__construct) and 
[CachedRouter::__construct()](https://github.com/atanvarno69/router/blob/master/docs/CachedRouter.md#__construct).

### Defining routes
Routes can be defined by using the `add()` method, the `addGroup()` method or 
via constructor injection. There are also shortcut methods for every HTTP 
method.

#### `add()`
```php
$router->add($method, $pattern, $handler);
```
The `$method` is an uppercase HTTP method string for which a certain route 
should match. It is possible to specify multiple valid methods using an array. 
There is a `Router::METHOD_*` constant for each valid HTTP method.

By default the `$pattern` uses a syntax where `{foo}` specifies a placeholder 
with name `foo` and matching the regex `[^/]+`. To adjust the pattern the 
placeholder matches, you can specify a custom pattern by writing `{bar:[0-9]+}`.

Custom patterns for route placeholders cannot use capturing groups. For example 
`{lang:(en|de)}` is not a valid placeholder, because `()` is a capturing group. 
Instead you can use either `{lang:en|de}` or `{lang:(?:en|de)}`.

Furthermore parts of the route enclosed in `[...]` are considered optional, so 
that `/foo[bar]` will match both `/foo` and `/foobar`. Optional parts are only 
supported in a trailing position, not in the middle of a route.

The `$handler` parameter does not necessarily have to be a callback, it could 
also be a controller class name or any other kind of data you wish to associate 
with the route. Atanvarno\Router only tells you which handler corresponds to 
your request, how you interpret it is up to you.

`add()` implements a [fluent interface](https://en.wikipedia.org/wiki/Fluent_interface), 
allowing multiple calls to be chained.

See [Router::add()](https://github.com/atanvarno69/router/blob/master/docs/Router.md#add).

#### `addGroup()`
You can specify routes inside of a group. All routes defined inside a group 
will have a common prefix.

For example, defining your routes as:
```php
$router->addGroup(
    '/admin',
    [
        [Router::METHOD_GET, '/user/{name}', 'handler']
        [Router::METHOD_DELETE, '/user/{name}', 'handler'],
    ]
);
```
Will have the same result as:
```php
$router->add(Router::METHOD_GET, '/admin/user/{name}', 'handler')
    ->add(Router::METHOD_DELETE, '/admin/user/{name}', 'handler');
```
`addGroup()` implements a [fluent interface](https://en.wikipedia.org/wiki/Fluent_interface), 
allowing multiple calls to be chained.

See [Router::addGroup()](https://github.com/atanvarno69/router/blob/master/docs/Router.md#addgroup).

#### Constructor injection
Route information can be injected into the `Router` instance constructor. This 
parameter accepts an array of arrays containing `$method`, `$pattern` and 
`$handler` values, like a call to `add()`.

This allows routes to be held in a separate configuration file that returns 
such an array:
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

$router = new SimpleRouter(Router::DRIVER_GROUP_COUNT, include 'path/to/routes.php');
```
See 
[SimpleRouter::__construct()](https://github.com/atanvarno69/router/blob/master/docs/SimpleRouter.md#__construct) and 
[CachedRouter::__construct()](https://github.com/atanvarno69/router/blob/master/docs/CachedRouter.md#__construct).

#### Shortcut methods
For all the valid HTTP request methods shortcut methods are available. For
example:
```php
$router->get('/get-route', 'get_handler')
    ->post('/post-route', 'post_handler');
// Is equivalent to:
$router->add(Router::METHOD_GET, '/get-route', 'get_handler')
    ->add(Router::METHOD_POST, '/post-route', 'post_handler');
```
Shortcut methods implement a [fluent interface](https://en.wikipedia.org/wiki/Fluent_interface), 
allowing multiple calls to be chained.

See [Router](https://github.com/atanvarno69/router/blob/master/docs/Router.md).

### Dispatching
A request is dispatched by calling the `dispatch()` method. This method accepts 
a [PSR-7 `RequestInterface`](http://www.php-fig.org/psr/psr-7/#psrhttpmessagerequestinterface) 
instance.
```php
$request = //... define your PSR-7 request.

$result = $router->dispatch($request);
```

Note that all URI paths are normalised so that they have no trailing slash 
and begin with a leading slash. All user supplied patterns are likewise 
normalised.

`dispatch()` returns an array whose first element contains a status code. It is 
one of `FastRoute\Dispatcher::NOT_FOUND`, 
`FastRoute\Dispatcher::METHOD_NOT_ALLOWED` or `FastRoute\Dispatcher::FOUND`. 
For the method not allowed status the second array element contains a list of 
HTTP methods allowed for the supplied request.

> **NOTE:** The HTTP specification requires that a `405 Method Not Allowed` 
response include the `Allow:` header to detail available methods for the 
requested resource. Applications using Router should use the second array 
element to add this header when relaying a 405 response.

For the found status the second array element is the handler that was 
associated with the route and the third array element is a dictionary of 
placeholder names to their values. For example:
```php
// Routing against GET /user/atan/42
[FastRoute\Dispatcher::FOUND, 'handler0', ['name' => 'atan', 'id' => '42']]
```

See [`Router::dispatch()`](https://github.com/atanvarno69/router/blob/master/docs/Router.md#dispatch).

### Caching
Instead of using `SimpleRouter` you can use `CachedRouter`.

`CachedRouter` requires a [PSR-16](http://www.php-fig.org/psr/psr-16/) cache 
object as a constructor parameter.

By default, `CachedRouter` will take its dispatch data directly from the cache 
and bypass and routes defined by `add()` calls or constructor injection. Where 
no dispatch data is available (for example on the first `dispatch()` call or if 
the cache data has expired) `CachedRouter` will generate dispatch data from the 
defined routes and store it in the cache.

If your route configuration has changed and you need to update the dispatch 
data in the cache, call `refreshCache()`.

See [CachedRouter](https://github.com/atanvarno69/router/blob/master/docs/CachedRouter.md).

### Exceptions
All exceptions thrown implement the interface [`RouterException`](https://github.com/atanvarno69/router/blob/master/docs/RouterException.md).

Rather than supply `array` results, `dispatch()` can instead throw exceptions 
for not found and method not allowed results. 
[`MethodNotAllowedException::getAllowed()`](https://github.com/atanvarno69/router/blob/master/docs/MethodNotAllowedException.md#getallowed) 
provides a list of allowed methods for the required `Allow:` response header.

The package contains these exceptions:
* [`InvalidArgumentException`](https://github.com/atanvarno69/router/blob/master/docs/InvalidArgumentException.md)
* [`MethodNotAllowedException`](https://github.com/atanvarno69/router/blob/master/docs/MethodNotAllowedException.md)
* [`NotFoundException`](https://github.com/atanvarno69/router/blob/master/docs/NotFoundException.md)

### A Note on HEAD Requests
The HTTP specification requires servers to [support both GET and HEAD methods](http://www.w3.org/Protocols/rfc2616/rfc2616-sec5.html#sec5.1.1):

> The methods GET and HEAD MUST be supported by all general-purpose servers

To avoid forcing users to manually register `HEAD` routes for each resource we 
fallback to matching an available `GET` route for a given resource. Applications 
MAY always specify their own `HEAD` method route for a given resource to bypass 
this behavior entirely.

## Full API
See [API](https://github.com/atanvarno69/router/blob/master/docs/API.md).