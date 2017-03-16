# Atanvarno\Router\CachedRouter
Implementation of [`Router`](Router.md) using a [PSR-16](http://www.php-fig.org/psr/psr-16/) cache.
```php
class CachedRouter extends SimpleRouter
{
    // Methods
    public function __construct(CacheInterface $cache, string $cacheKey = 'routerData', $driver = Router::DRIVER_GROUP_COUNT, array $routes = [], bool $cacheDisable = false)
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
    public function refreshCache(): bool
    public function trace(string $pattern, $handler): Router
}
```
Extends [`SimpleRouter`](SimpleRouter.md).
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
* [refreshCache](#refreshcache)
* [trace](Router.md#trace)

## __construct
Builds a `CachedRouter` instance.
```php
__construct(CacheInterface $cache, string $cacheKey = 'routerData', $driver = Router::DRIVER_GROUP_COUNT, array $routes = [], bool $cacheDisable = false)
```

### Parameters
* [`CacheInterface`](http://www.php-fig.org/psr/psr-16/#cacheinterface) **$cache**

  Required. [PSR-16](http://www.php-fig.org/psr/psr-16/) cache to use.

* `string` **$cacheKey**

  Optional. Defaults to `'routerData'`. Key to use for cached data.

* `string` **$driver**

  Optional. Defaults to `Router::DRIVER_GROUP_COUNT`. FastRoute driver. You 
  should use the `Router::DRIVER_*` constants.

* `array` **$routes**

  Optional. Defaults to `[]`. Array of [`add()`](Router.md#add) parameter 
  values.

* `bool` **$cacheDisable**

  Optional. Defaults to `false`. Disable cache functionality.

### Throws
* [`InvalidArgumentException`](InvalidArgumentException.md)

  Driver is invalid.
  
* [`InvalidArgumentException`](InvalidArgumentException.md)

  Routes array is invalid.

### Returns
* `CachedRouter`

## refreshCache
Generates dispatch data and caches it.
```php
public function refreshCache(): bool
```
This is useful if the routes configuration has changed, for example.

Note this method will still work when `$cacheDisable = true` has been passed 
to the [constructor](#__construct).

### Parameters
None.

### Throws
Nothing is thrown.

### Returns
* `bool`

  The result of the [`CacheInterface::set()`](http://www.php-fig.org/psr/psr-16/#cacheinterface) call.
