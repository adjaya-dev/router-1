<?php
/**
 * @package   Atanvarno\Router
 * @author    atanvarno69 <https://github.com/atanvarno69>
 * @copyright 2017 atanvarno.com
 * @license   https://opensource.org/licenses/MIT The MIT License
 */

namespace Atanvarno\Router;

/** SPL use block. */
use InvalidArgumentException;

/** PSR-7 use block */
use Psr\Http\Message\RequestInterface;

/** HTTP Message Utilities use block. */
use Fig\Http\Message\RequestMethodInterface;

/** Package use block. */
use Atanvarno\Router\Result;

/**
 * Atanvarno\Router\Router
 *
 * Interface for PSR-7 HTTP request routers built on top of FastRoute.
 *
 * @see https://github.com/nikic/FastRoute
 *
 * @api
 */
interface Router extends RequestMethodInterface
{
    /** @const string CHAR_COUNT Specifies the character count based driver. */
    const DRIVER_CHAR_COUNT = 'CharCountBased';

    /** @const string GROUP_COUNT Specifies the group count based driver. */
    const DRIVER_GROUP_COUNT = 'GroupCountBased';

    /** @const string GROUP_POS Specifies the group position based driver. */
    const DRIVER_GROUP_POS = 'GroupPosBased';

    /** @const string MARK Specifies the mark based driver. */
    const DRIVER_MARK = 'MarkBased';

    /**
     * @internal Driver validation constant.
     *
     * @const string[] VALID_DRIVERS List of valid FastRoute drivers.
     */
    const VALID_DRIVERS = [
        self::DRIVER_CHAR_COUNT,
        self::DRIVER_GROUP_COUNT,
        self::DRIVER_GROUP_POS,
        self::DRIVER_MARK,
    ];

    /**
     * @internal HTTP method validation constant.
     *
     * @const string[] VALID_HTTP_METHODS List of valid HTTP methods.
     */
    const VALID_HTTP_METHODS = [
        self::METHOD_CONNECT,
        self::METHOD_DELETE,
        self::METHOD_GET,
        self::METHOD_HEAD,
        self::METHOD_OPTIONS,
        self::METHOD_PATCH,
        self::METHOD_POST,
        self::METHOD_PURGE,
        self::METHOD_PUT,
        self::METHOD_TRACE,
    ];

    /**
     * Adds a route.
     *
     * Accepts an uppercase HTTP method string for which a certain route
     * should match. It is possible to specify multiple valid methods using
     * an array. You may use the `Router::METHOD_*` constants here.
     *
     * Accepts a pattern to match against a URL path. By default the pattern
     * uses a syntax where `{foo}` specifies a placeholder with name `foo` and
     * matching the regex `[^/]+`. To adjust the pattern the placeholder
     * matches, you can specify a custom pattern by writing `{bar:[0-9]+}`. Some
     * examples:
     *
     * ```php
     * // Matches /user/42, but not /user/xyz
     * $router->add(Router::METHOD_GET, '/user/{id:\d+}', 'handler');
     *
     * // Matches /user/foobar, but not /user/foo/bar
     * $router->add(Router::METHOD_GET, '/user/{name}', 'handler');
     *
     * // Matches /user/foo/bar as well
     * $router->add(Router::METHOD_GET, '/user/{name:.+}', 'handler');
     * ```
     *
     * Custom patterns for route placeholders cannot use capturing groups. For
     * example `{lang:(en|de)}` is not a valid placeholder, because `()` is a
     * capturing group. Instead you can use either `{lang:en|de}` or
     * `{lang:(?:en|de)}`.
     *
     * Furthermore parts of the route enclosed in `[...]` are considered
     * optional, so that `/foo[bar]` will match both `/foo` and `/foobar`.
     * Optional parts are only supported in a trailing position, not in the
     * middle of a route.
     *
     * ```php
     * // This route
     * $router->add(Router::METHOD_GET, '/user/{id:\d+}[/{name}]', 'handler');
     * // Is equivalent to these two routes
     * $router->add(Router::METHOD_GET, '/user/{id:\d+}', 'handler');
     * $router->add(Router::METHOD_GET, '/user/{id:\d+}/{name}', 'handler');
     *
     * // Multiple nested optional parts are possible as well
     * $router->add(Router::METHOD_GET, '/user[/{id:\d+}[/{name}]]', 'handler');
     *
     * // This route is NOT valid, as optional parts can only occur at the end
     * $router->add(Router::METHOD_GET, '/user[/{id:\d+}]/{name}', 'handler');
     * ```
     *
     * Accepts a handler of any value. The handler does not necessarily have to
     * be a callback, it could also be a controller class name or any other kind
     * of data you wish to associate with the route. The `dispatch()` method
     * only tells you which handler corresponds to your URI, how you interpret
     * it is up to you.
     *
     * @param string|string[] $methods HTTP method(s) for the route.
     * @param string          $pattern URL path pattern for the route.
     * @param mixed           $handler Any arbitrary handler value.
     *
     * @throws InvalidArgumentException HTTP method is invalid.
     *
     * @return $this Fluent interface.
     */
    public function add($methods, string $pattern, $handler): Router;

    /**
     * Adds a group of routes.
     *
     * All routes defined inside a group will have a common prefix. For example:
     * ```php
     * $router->addGroup(
     *     '/admin',
     *     [
     *         [Router::METHOD_GET, '/do-something', 'handler'],
     *         [Router::METHOD_GET, '/do-another-thing', 'handler'],
     *         [Router::METHOD_GET, '/do-something-else', 'handler'],
     *     ]
     * );
     * // Will have the same result as:
     * $router->add(Router::METHOD_GET, '/admin/do-something', 'handler');
     * $router->add(Router::METHOD_GET, '/admin/do-another-thing', 'handler');
     * $router->add(Router::METHOD_GET, '/admin/do-something-else', 'handler');
     * ```
     *
     * @param string $patternPrefix Group prefix pattern.
     * @param array  $routes        Array of `add()` parameter values.
     *
     * @throws InvalidArgumentException Routes array is invalid.
     *
     * @return $this Fluent interface.
     */
    public function addGroup(string $patternPrefix, array $routes): Router;

    /**
     * Dispatches a PSR-7 request.
     *
     * @param RequestInterface $request    PSR-7 request to dispatch.
     *
     * @return Result The dispatch result.
     */
    public function dispatch(RequestInterface $request): Result;

    /**
     * Adds a `CONNECT` method route.
     *
     * @param string $pattern URL path pattern for the route.
     * @param mixed  $handler Any arbitrary handler value.
     *
     * @return $this Fluent interface.
     */
    public function connect(string $pattern, $handler): Router;

    /**
     * Adds a `DELETE` method route.
     *
     * @param string $pattern URL path pattern for the route.
     * @param mixed  $handler Any arbitrary handler value.
     *
     * @return $this Fluent interface.
     */
    public function delete(string $pattern, $handler): Router;

    /**
     * Adds a `GET` method route.
     *
     * @param string $pattern URL path pattern for the route.
     * @param mixed  $handler Any arbitrary handler value.
     *
     * @return $this Fluent interface.
     */
    public function get(string $pattern, $handler): Router;

    /**
     * Adds a `HEAD` method route.
     *
     * @param string $pattern URL path pattern for the route.
     * @param mixed  $handler Any arbitrary handler value.
     *
     * @return $this Fluent interface.
     */
    public function head(string $pattern, $handler): Router;

    /**
     * Adds a `OPTIONS` method route.
     *
     * @param string $pattern URL path pattern for the route.
     * @param mixed  $handler Any arbitrary handler value.
     *
     * @return $this Fluent interface.
     */
    public function options(string $pattern, $handler): Router;

    /**
     * Adds a `PATCH` method route.
     *
     * @param string $pattern URL path pattern for the route.
     * @param mixed  $handler Any arbitrary handler value.
     *
     * @return $this Fluent interface.
     */
    public function patch(string $pattern, $handler): Router;

    /**
     * Adds a `POST` method route.
     *
     * @param string $pattern URL path pattern for the route.
     * @param mixed  $handler Any arbitrary handler value.
     *
     * @return $this Fluent interface.
     */
    public function post(string $pattern, $handler): Router;

    /**
     * Adds a `PURGE` method route.
     *
     * @param string $pattern URL path pattern for the route.
     * @param mixed  $handler Any arbitrary handler value.
     *
     * @return $this Fluent interface.
     */
    public function purge(string $pattern, $handler): Router;

    /**
     * Adds a `PUT` method route.
     *
     * @param string $pattern URL path pattern for the route.
     * @param mixed  $handler Any arbitrary handler value.
     *
     * @return $this Fluent interface.
     */
    public function put(string $pattern, $handler): Router;

    /**
     * Adds a `TRACE` method route.
     *
     * @param string $pattern URL path pattern for the route.
     * @param mixed  $handler Any arbitrary handler value.
     *
     * @return $this Fluent interface.
     */
    public function trace(string $pattern, $handler): Router;
}
