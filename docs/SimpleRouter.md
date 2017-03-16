# Atanvarno\Router\SimpleRouter
Simple implementation of [`Router`](Router.md).
```php
class SimpleRouter implements Router
{
    // Methods
    public function __construct(string $driver = Router::DRIVER_GROUP_COUNT, array $routes = [])
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
Implements [`Router`](Router.md).
* [__construct](#construct)
* [add](Router.md#add)
* [addGroup](Router.md#addGroup)
* [connect](Router.md#connect)
* [delete](Router.md#delete)
* [dispatch](Router.md#dispatch)
* [get](Router.md#get)
* [head](Router.md#head)
* [options](Router.md#options)
* [patch](Router.md#patch)
* [post](Router.md#post)
* [purge](Router.md#purge)
* [put](Router.md#put)
* [trace](Router.md#trace)

## __construct
Builds a `SimpleRouter` instance.
```php
__construct(string $driver = Router::DRIVER_GROUP_COUNT, array $routes = [])
```

### Parameters
* `string` **$driver**

  Optional. Defaults to `Router::DRIVER_GROUP_COUNT`. FastRoute driver. You 
  should use the `Router::DRIVER_*` constants.

* `array` **$routes**

  Optional. Defaults to `[]`. Array of [`add()`](Router.md#add) parameter 
  values.

### Throws
* [`InvalidArgumentException`](InvalidArgumentException.md)

  Driver is invalid.
  
* [`InvalidArgumentException`](InvalidArgumentException.md)

  Routes array is invalid.

### Returns
* `SimpleRouter`
