# Atanvarno\Router\Router
Interface for [PSR-7](http://www.php-fig.org/psr/psr-7/) HTTP request routers 
built on top of [FastRoute](https://github.com/nikic/FastRoute).
```php
interface Router extends RequestMethodInterface
{
    // Constants
    public const DRIVER_CHAR_COUNT
    public const DRIVER_GROUP_COUNT
    public const DRIVER_GROUP_POS
    public const DRIVER_MARK
    public const METHOD_CONNECT
    public const METHOD_DELETE
    public const METHOD_GET
    public const METHOD_HEAD
    public const METHOD_OPTIONS
    public const METHOD_PATCH
    public const METHOD_POST
    public const METHOD_PURGE
    public const METHOD_PUT
    public const METHOD_TRACE
    
    // Methods
    public function add($methods, string $pattern, $handler): Router
    public function addGroup(string $patternPrefix, array $routes): Router
    public function connect(string $pattern, $handler): Router
    public function delete(string $pattern, $handler): Router
    public function dispatch(RequestInterface $request, bool $exceptions = false): array
    public function get(string $pattern, $handler): Router
    public function head(string $pattern, $handler): Router
    public function options(string $pattern, $handler): Router
    public function patch(string $pattern, $handler): Router
    public function post(string $pattern, $handler): Router
    public function purge(string $pattern, $handler): Router
    public function put(string $pattern, $handler): Router
    public function trace(string $pattern, $handler): Router
}
```
Extends [`Fig\Http\Message\RequestMethodInterface`](https://github.com/php-fig/http-message-util).

* [add](#add)
* [addGroup](#addGroup)
* [connect](#connect)
* [delete](#delete)
* [dispatch](#dispatch)
* [get](#get)
* [head](#head)
* [options](#options)
* [patch](#patch)
* [post](#post)
* [purge](#purge)
* [put](#put)
* [trace](#trace)

## add
Adds a route.
```php
add($methods, string $pattern, $handler): Router
```
Accepts an uppercase HTTP method string for which a certain route should 
match. It is possible to specify multiple valid methods using an array. You 
may use the `Router::METHOD_*` constants here.

Accepts a pattern to match against a URL path. By default the pattern uses a 
syntax where `{foo}` specifies a placeholder with name `foo` and matching the 
regex `[^/]+`. To adjust the pattern the placeholder matches, you can specify 
a custom pattern by writing `{bar:[0-9]+}`. Some examples:
```php
// Matches /user/42, but not /user/xyz
$router->add(Router::METHOD_GET, '/user/{id:\d+}', 'handler');

// Matches /user/foobar, but not /user/foo/bar
$router->add(Router::METHOD_GET, '/user/{name}', 'handler');

// Matches /user/foo/bar as well
$router->add(Router::METHOD_GET, '/user/{name:.+}', 'handler');
```

Custom patterns for route placeholders cannot use capturing groups. For 
example `{lang:(en|de)}` is not a valid placeholder, because `()` is a 
capturing group. Instead you can use either `{lang:en|de}` or 
`{lang:(?:en|de)}`.

Furthermore parts of the route enclosed in `[...]` are considered optional, 
so that `/foo[bar]` will match both `/foo` and `/foobar`. Optional parts are 
only supported in a trailing position, not in the middle of a route.

```php
// This route
$router->add(Router::METHOD_GET, '/user/{id:\d+}[/{name}]', 'handler');
// Is equivalent to these two routes
$router->add(Router::METHOD_GET, '/user/{id:\d+}', 'handler');
$router->add(Router::METHOD_GET, '/user/{id:\d+}/{name}', 'handler');

// Multiple nested optional parts are possible as well
$router->add(Router::METHOD_GET, '/user[/{id:\d+}[/{name}]]', 'handler');

// This route is NOT valid, as optional parts can only occur at the end
$router->add(Router::METHOD_GET, '/user[/{id:\d+}]/{name}', 'handler');
```

Accepts a handler of any value. The handler does not necessarily have to be a 
callback, it could also be a controller class name or any other kind of data 
you wish to associate with the route. The `dispatch()` method only tells you 
which handler corresponds to your URI, how you interpret it is up to you.

### Parameters
* `string|string[]` **$methods**

  Required. HTTP method(s) for the route.

* `string` **$pattern**

  Required. URL path pattern for the route.

* `mixed` **$handler**

  Required. Any arbitrary handler value.

### Throws
* [`InvalidArgumentException`](InvalidArgumentException.md)

  HTTP method is invalid.

### Returns
* `Router` **$this**

  [Fluent interface](https://en.wikipedia.org/wiki/Fluent_interface), allowing
  multiple calls to be chained.

## addGroup
Adds a group of routes.
```php
addGroup(string $patternPrefix, array $routes): Router
```
All routes defined inside a group will have a common prefix. For example:
```php
$router->addGroup(
    '/admin',
    [
        [Router::METHOD_GET, '/do-something', 'handler'],
        [Router::METHOD_GET, '/do-another-thing', 'handler'],
        [Router::METHOD_GET, '/do-something-else', 'handler'],
    ]
);
// Will have the same result as:
$router->add(Router::METHOD_GET, '/admin/do-something', 'handler');
$router->add(Router::METHOD_GET, '/admin/do-another-thing', 'handler');
$router->add(Router::METHOD_GET, '/admin/do-something-else', 'handler');
```

### Parameters
* `string` **$patternPrefix**

  Required. Group prefix pattern.

* `array` **$routes**

  Required. Array of [`add()`](#add) parameter values.

### Throws
* [`InvalidArgumentException`](InvalidArgumentException.md)

  Routes array is invalid.

### Returns
* `Router` **$this**

  [Fluent interface](https://en.wikipedia.org/wiki/Fluent_interface), allowing
  multiple calls to be chained.

## connect
Adds a `CONNECT` method route.
```php
connect(string $pattern, $handler): Router
```

### Parameters
* `string` **$pattern**

  Required. URL path pattern for the route.

* `mixed` **$handler**

  Required. Any arbitrary handler value.

### Throws
Nothing is thrown.

### Returns
* `Router` **$this**

  [Fluent interface](https://en.wikipedia.org/wiki/Fluent_interface), allowing
  multiple calls to be chained.

## delete
Adds a `DELETE` method route.
```php
delete(string $pattern, $handler): Router
```

### Parameters
* `string` **$pattern**

  Required. URL path pattern for the route.

* `mixed` **$handler**

  Required. Any arbitrary handler value.

### Throws
Nothing is thrown.

### Returns
* `Router` **$this**

  [Fluent interface](https://en.wikipedia.org/wiki/Fluent_interface), allowing
  multiple calls to be chained.

## dispatch
Dispatches a [PSR-7](http://www.php-fig.org/psr/psr-7/) request.
```php
dispatch(RequestInterface $request, bool $exceptions = false): array
```
Returns an array whose first element contains a status code, one of:

* `FastRoute\Dispatcher::NOT_FOUND`
* `FastRoute\Dispatcher::METHOD_NOT_ALLOWED`
* `FastRoute\Dispatcher::FOUND`

For the method not allowed status the second array element contains a list of 
HTTP methods allowed for the supplied request's URI. For example:
```php
[
    FastRoute\Dispatcher::METHOD_NOT_ALLOWED,
    [Router::METHOD_GET, Router::METHOD_POST]
]
```
> **NOTE:** The [HTTP specification]() requires that a `405 Method Not Allowed` 
response include the `Allow:` header to detail available methods for the 
requested resource. Applications using `Atanvarno\Router` should use the 
second array element to add this header when relaying a `405` response.

For the found status the second array element is the handler that was 
associated with the route and the third array element is a dictionary of 
placeholder names to their values. For example:
```php
// Routing against GET /user/atanvarno/42
[
    FastRoute\Dispatcher::FOUND,
    'handler0',
    ['name' => 'atanvarno', 'id' => '42']
]
```

`dispatch()` can instead return only a found status array. For not found and 
not allowed statuses, exceptions can be thrown. Use the second parameter to 
enable this behaviour.

### Parameters
* [`RequestInterface`](http://www.php-fig.org/psr/psr-7/#psrhttpmessagerequestinterface) **$request**

  Required. [PSR-7](http://www.php-fig.org/psr/psr-7/) request to dispatch.

* `bool` **$exceptions**

  Optional. Defaults to `false`. Pass `true` to enable exceptions.

### Throws
* [`NotFoundException`](NotFoundException.md)

  The given request could not be matched. This is only thrown if `$exceptions` 
  is passed as `true`. Otherwise, an array is returned.
  
* [`MethodNotAllowedException`](MethodNotAllowedException.md)

  Requested HTTP method is not allowed. This is only thrown if `$exceptions` 
  is passed as `true`. Otherwise, an array is returned.

### Returns
* `array`

  The dispatch result array.

## get
Adds a `GET` method route.
```php
get(string $pattern, $handler): Router
```

### Parameters
* `string` **$pattern**

  Required. URL path pattern for the route.

* `mixed` **$handler**

  Required. Any arbitrary handler value.

### Throws
Nothing is thrown.

### Returns
* `Router` **$this**

  [Fluent interface](https://en.wikipedia.org/wiki/Fluent_interface), allowing
  multiple calls to be chained.

## head
Adds a `HEAD` method route.
```php
head(string $pattern, $handler): Router
```

### Parameters
* `string` **$pattern**

  Required. URL path pattern for the route.

* `mixed` **$handler**

  Required. Any arbitrary handler value.

### Throws
Nothing is thrown.

### Returns
* `Router` **$this**

  [Fluent interface](https://en.wikipedia.org/wiki/Fluent_interface), allowing
  multiple calls to be chained.

## options
Adds a `OPTIONS` method route.
```php
options(string $pattern, $handler): Router
```

### Parameters
* `string` **$pattern**

  Required. URL path pattern for the route.

* `mixed` **$handler**

  Required. Any arbitrary handler value.

### Throws
Nothing is thrown.

### Returns
* `Router` **$this**

  [Fluent interface](https://en.wikipedia.org/wiki/Fluent_interface), allowing
  multiple calls to be chained.

## patch
Adds a `PATCH` method route.
```php
patch(string $pattern, $handler): Router
```

### Parameters
* `string` **$pattern**

  Required. URL path pattern for the route.

* `mixed` **$handler**

  Required. Any arbitrary handler value.

### Throws
Nothing is thrown.

### Returns
* `Router` **$this**

  [Fluent interface](https://en.wikipedia.org/wiki/Fluent_interface), allowing
  multiple calls to be chained.

## post
Adds a `POST` method route.
```php
post(string $pattern, $handler): Router
```

### Parameters
* `string` **$pattern**

  Required. URL path pattern for the route.

* `mixed` **$handler**

  Required. Any arbitrary handler value.

### Throws
Nothing is thrown.

### Returns
* `Router` **$this**

  [Fluent interface](https://en.wikipedia.org/wiki/Fluent_interface), allowing
  multiple calls to be chained.

## purge
Adds a `PURGE` method route.
```php
purge(string $pattern, $handler): Router
```

### Parameters
* `string` **$pattern**

  Required. URL path pattern for the route.

* `mixed` **$handler**

  Required. Any arbitrary handler value.

### Throws
Nothing is thrown.

### Returns
* `Router` **$this**

  [Fluent interface](https://en.wikipedia.org/wiki/Fluent_interface), allowing
  multiple calls to be chained.

## put
Adds a `PUT` method route.
```php
put(string $pattern, $handler): Router
```

### Parameters
* `string` **$pattern**

  Required. URL path pattern for the route.

* `mixed` **$handler**

  Required. Any arbitrary handler value.

### Throws
Nothing is thrown.

### Returns
* `Router` **$this**

  [Fluent interface](https://en.wikipedia.org/wiki/Fluent_interface), allowing
  multiple calls to be chained.

## trace
Adds a `TRACE` method route.
```php
trace(string $pattern, $handler): Router
```

### Parameters
* `string` **$pattern**

  Required. URL path pattern for the route.

* `mixed` **$handler**

  Required. Any arbitrary handler value.

### Throws
Nothing is thrown.

### Returns
* `Router` **$this**

  [Fluent interface](https://en.wikipedia.org/wiki/Fluent_interface), allowing
  multiple calls to be chained.
