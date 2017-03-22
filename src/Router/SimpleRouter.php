<?php
/**
 * @package   Atanvarno\Router
 * @author    atanvarno69 <https://github.com/atanvarno69>
 * @copyright 2017 atanvarno.com
 * @license   https://opensource.org/licenses/MIT The MIT License
 */

namespace Atanvarno\Router\Router;

/** PSR-7 use block */
use Psr\Http\Message\RequestInterface;

/** HTTP Message Utilities use block. */
use Fig\Http\Message\RequestMethodInterface;

/** FastRoute use block. */
use FastRoute\{
    Dispatcher, RouteCollector, RouteParser\Std
};

/** Package use block. */
use Atanvarno\Router\{
    Exception\InvalidArgumentException,
    Result\MatchedResult,
    Result\NotFoundResult,
    Result\MethodNotAllowedResult,
    Router
};

/**
 * Atanvarno\Router\SimpleRouter
 *
 * Simple implementation of `Router`.
 *
 * @api
 */
class SimpleRouter implements Router
{
    /**
     * @internal Class properties.
     *
     * @var string  $driver FastRoute driver for dispatcher and data generator.
     * @var array[] $routes Routes for the route collector.
     */
    protected $driver, $routes;

    /**
     * Builds a `SimpleRouter` instance.
     *
     * @throws InvalidArgumentException Driver is invalid.
     * @throws InvalidArgumentException Routes array is invalid.
     *
     * @param string $driver FastRoute driver. You should use the
     *      `Router::DRIVER_*` constants.
     * @param array  $routes Array of `add()` parameter values.
     */
    public function __construct(
        string $driver = Router::DRIVER_GROUP_COUNT,
        array $routes = []
    ) {
        if (!in_array($driver, Router::VALID_DRIVERS)) {
            $msg = sprintf('%s is not a valid FastRoute driver', $driver);
            throw new InvalidArgumentException($msg);
        }
        $this->driver = $driver;
        if (empty($routes)) {
            $this->routes = [];
        } else {
            $routes = array_values($routes);
            $this->addGroup('', $routes);
        }
    }

    /** @inheritdoc */
    public function add($methods, string $pattern, $handler): Router
    {
        $checkMethods = (array) $methods;
        foreach ($checkMethods as $key => $method) {
            if (!is_string($method)) {
                $msg = sprintf('Method %u is not a string', $key);
                throw new InvalidArgumentException($msg);
            }
            if (!in_array($method, Router::VALID_HTTP_METHODS, true)) {
                $msg = sprintf('%s is not a valid HTTP method', $method);
                throw new InvalidArgumentException($msg);
            }
        }
        $pattern = '/' . trim($pattern, ' /');
        $this->routes[] = [$methods, $pattern, $handler];
        return $this;
    }

    /** @inheritdoc */
    public function addGroup(string $patternPrefix, array $routes): Router
    {
        foreach ($routes as $key => $route) {
            if (!is_array($route)) {
                $msg = sprintf('Route %u is not an array', $key);
                throw new InvalidArgumentException($msg);
            }
            if (count($route) !== 3) {
                $msg = sprintf('Route %u does not contain 3 values', $key);
                throw new InvalidArgumentException($msg);
            }
            try {
                $this->add(...$route);
            } catch (InvalidArgumentException $caught) {
                $msg = sprintf('Route %u: %s', $key, $caught->getMessage());
                throw new InvalidArgumentException($msg);
            }
        }
        return $this;
    }

    /** @inheritdoc */
    public function dispatch(RequestInterface $request): array
    {
        $dispatcherName = 'FastRoute\\Dispatcher\\' . $this->driver;
        /** @var Dispatcher $dispatcher */
        $dispatcher = new $dispatcherName($this->getDispatchData());
        $method = $request->getMethod();
        $path = '/' . trim($request->getUri()->getPath(), ' /');
        $result = $dispatcher->dispatch($method, $path);
        switch ($result[0]) {
            default: // no break
            case Dispatcher::FOUND:
                $return = new MatchedResult($result);
                break;
            case Dispatcher::NOT_FOUND:
                $return = new NotFoundResult($result);
                break;
            case Dispatcher::METHOD_NOT_ALLOWED:
                $return = MethodNotAllowedResult($result);
        }
        return $return;
    }

    /** @inheritdoc */
    public function connect(string $pattern, $handler): Router
    {
        return $this->add(
            RequestMethodInterface::METHOD_CONNECT,
            $pattern,
            $handler
        );
    }

    /** @inheritdoc */
    public function delete(string $pattern, $handler): Router
    {
        return $this->add(
            RequestMethodInterface::METHOD_DELETE,
            $pattern,
            $handler
        );
    }

    /** @inheritdoc */
    public function get(string $pattern, $handler): Router
    {
        return $this->add(
            RequestMethodInterface::METHOD_GET,
            $pattern,
            $handler
        );
    }

    /** @inheritdoc */
    public function head(string $pattern, $handler): Router
    {
        return $this->add(
            RequestMethodInterface::METHOD_HEAD,
            $pattern,
            $handler
        );
    }

    /** @inheritdoc */
    public function options(string $pattern, $handler): Router
    {
        return $this->add(
            RequestMethodInterface::METHOD_OPTIONS,
            $pattern,
            $handler
        );
    }

    /** @inheritdoc */
    public function patch(string $pattern, $handler): Router
    {
        return $this->add(
            RequestMethodInterface::METHOD_PATCH,
            $pattern,
            $handler
        );
    }

    /** @inheritdoc */
    public function post(string $pattern, $handler): Router
    {
        return $this->add(
            RequestMethodInterface::METHOD_POST,
            $pattern,
            $handler
        );
    }

    /** @inheritdoc */
    public function purge(string $pattern, $handler): Router
    {
        return $this->add(
            RequestMethodInterface::METHOD_PURGE,
            $pattern,
            $handler
        );
    }

    /** @inheritdoc */
    public function put(string $pattern, $handler): Router
    {
        return $this->add(
            RequestMethodInterface::METHOD_PUT,
            $pattern,
            $handler
        );
    }

    /** @inheritdoc */
    public function trace(string $pattern, $handler): Router
    {
        return $this->add(
            RequestMethodInterface::METHOD_TRACE,
            $pattern,
            $handler
        );
    }

    /** @internal */
    protected function getDispatchData(): array
    {
        $dataGeneratorName = 'FastRoute\\DataGenerator\\' . $this->driver;
        $routeCollector = new RouteCollector(
            new Std(), new $dataGeneratorName()
        );
        foreach ($this->routes as $route) {
            $routeCollector->addRoute(...$route);
        }
        return $routeCollector->getData();
    }
}
