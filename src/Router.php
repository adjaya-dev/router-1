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
use Atanvarno\Router\Exception\{
    MethodNotAllowedException, NotFoundException
};

/**
 * Atanvarno\Router\Router
 *
 * Interface for a PSR-7 HTTP request router built on top of FastRoute.
 *
 * @see https://github.com/nikic/FastRoute
 *
 * @api
 */
interface Router
{
    /** @const string CHAR_COUNT Specifies the character count based driver. */
    const CHAR_COUNT = 'CharCountBased';

    /** @const string GROUP_COUNT Specifies the group count based driver. */
    const GROUP_COUNT = 'GroupCountBased';

    /** @const string GROUP_POS Specifies the group position based driver. */
    const GROUP_POS = 'GroupPosBased';

    /** @const string MARK Specifies the mark based driver. */
    const MARK = 'MarkBased';

    /** @const string[] VALID_DRIVERS List of valid FastRoute drivers. */
    const VALID_DRIVERS = [
        Router::CHAR_COUNT,
        Router::GROUP_COUNT,
        Router::GROUP_POS,
        Router::MARK,
    ];

    /** @const string[] VALID_HTTP_METHODS List of valid HTTP methods. */
    const VALID_HTTP_METHODS = [
        RequestMethodInterface::METHOD_CONNECT,
        RequestMethodInterface::METHOD_DELETE,
        RequestMethodInterface::METHOD_GET,
        RequestMethodInterface::METHOD_HEAD,
        RequestMethodInterface::METHOD_OPTIONS,
        RequestMethodInterface::METHOD_PATCH,
        RequestMethodInterface::METHOD_POST,
        RequestMethodInterface::METHOD_PURGE,
        RequestMethodInterface::METHOD_PUT,
        RequestMethodInterface::METHOD_TRACE,
    ];

    /**
     * Adds a route.
     *
     * @param string|string[] $httpMethods  HTTP method(s) for the route.
     * @param string          $pattern      URL path pattern for the route.
     * @param mixed           $handler      Any arbitrary value for `dispatch()`
     *      to return when the route is matched. 
     *
     * @throws InvalidArgumentException HTTP method is not valid.
     *
     * @return bool `true` on successful add, `false` otherwise.
     */
    public function add($httpMethods, string $pattern, $handler): bool;

    /**
     * Dispatches a request.
     *
     * @param RequestInterface $request PSR-7 request to dispatch.
     *
     * @throws NotFoundException         The given request could not be matched.
     * @throws MethodNotAllowedException Requested HTTP method is not allowed.
     *
     * @return mixed The given handler for the matched route.
     */
    public function dispatch(RequestInterface $request);

    /**
     * Adds a CONNECT route.
     *
     * @param string $pattern URL path pattern for the route.
     * @param mixed  $handler Any arbitrary value for `dispatch()` to return
     *      when the route is matched.
     *
     * @return bool `true` on successful add, `false` otherwise.
     */
    public function connect(string $pattern, $handler): bool;

    /**
     * Adds a DELETE route.
     *
     * @param string $pattern URL path pattern for the route.
     * @param mixed  $handler Any arbitrary value for `dispatch()` to return
     *      when the route is matched.
     *
     * @return bool `true` on successful add, `false` otherwise.
     */
    public function delete(string $pattern, $handler): bool;

    /**
     * Adds a GET route.
     *
     * @param string $pattern URL path pattern for the route.
     * @param mixed  $handler Any arbitrary value for `dispatch()` to return
     *      when the route is matched.
     *
     * @return bool `true` on successful add, `false` otherwise.
     */
    public function get(string $pattern, $handler): bool;

    /**
     * Adds a HEAD route.
     *
     * @param string $pattern URL path pattern for the route.
     * @param mixed  $handler Any arbitrary value for `dispatch()` to return
     *      when the route is matched.
     *
     * @return bool `true` on successful add, `false` otherwise.
     */
    public function head(string $pattern, $handler): bool;

    /**
     * Adds a OPTIONS route.
     *
     * @param string $pattern URL path pattern for the route.
     * @param mixed  $handler Any arbitrary value for `dispatch()` to return
     *      when the route is matched.
     *
     * @return bool `true` on successful add, `false` otherwise.
     */
    public function options(string $pattern, $handler): bool;

    /**
     * Adds a PATCH route.
     *
     * @param string $pattern URL path pattern for the route.
     * @param mixed  $handler Any arbitrary value for `dispatch()` to return
     *      when the route is matched.
     *
     * @return bool `true` on successful add, `false` otherwise.
     */
    public function patch(string $pattern, $handler): bool;

    /**
     * Adds a POST route.
     *
     * @param string $pattern URL path pattern for the route.
     * @param mixed  $handler Any arbitrary value for `dispatch()` to return
     *      when the route is matched.
     *
     * @return bool `true` on successful add, `false` otherwise.
     */
    public function post(string $pattern, $handler): bool;

    /**
     * Adds a PURGE route.
     *
     * @param string $pattern URL path pattern for the route.
     * @param mixed  $handler Any arbitrary value for `dispatch()` to return
     *      when the route is matched.
     *
     * @return bool `true` on successful add, `false` otherwise.
     */
    public function purge(string $pattern, $handler): bool;

    /**
     * Adds a PUT route.
     *
     * @param string $pattern URL path pattern for the route.
     * @param mixed  $handler Any arbitrary value for `dispatch()` to return
     *      when the route is matched.
     *
     * @return bool `true` on successful add, `false` otherwise.
     */
    public function put(string $pattern, $handler): bool;

    /**
     * Adds a TRACE route.
     *
     * @param string $pattern URL path pattern for the route.
     * @param mixed  $handler Any arbitrary value for `dispatch()` to return
     *      when the route is matched.
     *
     * @return bool `true` on successful add, `false` otherwise.
     */
    public function trace(string $pattern, $handler): bool;
}
