<?php
/**
 * @package   Atanvarno\Router
 * @author    atanvarno69 <https://github.com/atanvarno69>
 * @copyright 2017 atanvarno.com
 * @license   https://opensource.org/licenses/MIT The MIT License
 */

namespace Atanvarno\Router;


/** PSR-7 use block */
use Psr\Http\Message\RequestInterface;

/** HTTP Message Utilities use block. */
use Fig\Http\Message\RequestMethodInterface;

/** FastRoute use block. */
use FastRoute\{
    Dispatcher, RouteCollector, RouteParser\Std
};

/** Package use block. */
use Atanvarno\Router\Exception\{
    InvalidArgumentException, MethodNotAllowedException, NotFoundException
};

/**
 * Atanvarno\Router\SimpleRouter
 *
 * @api
 */
class SimpleRouter implements Router
{
    /**
     * @var string  $driver
     * @var array[] $routes
     */
    protected $driver, $routes;

    public function __construct(
        string $driver = Router::GROUP_COUNT,
        array $routes = []
    ) {
        // Set driver
        if (!in_array($driver, Router::VALID_DRIVERS)) {
            $msg = sprintf('%s is not a valid FastRoute driver', $driver);
            throw new InvalidArgumentException($msg);
        }
        $this->driver = $driver;

        // Ensure routes array is numerically indexed.
        $routes = array_values($routes);

        // Add given routes
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
    }

    /** @inheritdoc */
    public function add($httpMethods, string $pattern, $handler): bool
    {
        $methods = (array) $httpMethods;
        foreach ($methods as $key => $method) {
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
        $this->routes[] = [$httpMethods, $pattern, $handler];
        return true;
    }

    /** @inheritdoc */
    public function dispatch(RequestInterface $request)
    {
        $dispatcher = $this->getDispatcher();
        $method = $request->getMethod();
        $path = '/' . trim($request->getUri()->getPath(), ' /');
        $result =  $dispatcher->dispatch($method, $path);
        switch ($result[0]) {
            case Dispatcher::NOT_FOUND:
                $msg = sprintf('Could not match %s', $path);
                throw new NotFoundException($msg);
            case Dispatcher::METHOD_NOT_ALLOWED:
                throw new MethodNotAllowedException($result[1], $method);
            default: // No break
            case Dispatcher::FOUND:
                return $result;
        }
    }

    /** @inheritdoc */
    public function connect(string $pattern, $handler): bool
    {
        return $this->add(
            RequestMethodInterface::METHOD_CONNECT,
            $pattern,
            $handler
        );
    }

    /** @inheritdoc */
    public function delete(string $pattern, $handler): bool
    {
        return $this->add(
            RequestMethodInterface::METHOD_DELETE,
            $pattern,
            $handler
        );
    }

    /** @inheritdoc */
    public function get(string $pattern, $handler): bool
    {
        return $this->add(
            RequestMethodInterface::METHOD_GET,
            $pattern,
            $handler
        );
    }

    /** @inheritdoc */
    public function head(string $pattern, $handler): bool
    {
        return $this->add(
            RequestMethodInterface::METHOD_HEAD,
            $pattern,
            $handler
        );
    }

    /** @inheritdoc */
    public function options(string $pattern, $handler): bool
    {
        return $this->add(
            RequestMethodInterface::METHOD_OPTIONS,
            $pattern,
            $handler
        );
    }

    /** @inheritdoc */
    public function patch(string $pattern, $handler): bool
    {
        return $this->add(
            RequestMethodInterface::METHOD_PATCH,
            $pattern,
            $handler
        );
    }

    /** @inheritdoc */
    public function post(string $pattern, $handler): bool
    {
        return $this->add(
            RequestMethodInterface::METHOD_POST,
            $pattern,
            $handler
        );
    }

    /** @inheritdoc */
    public function purge(string $pattern, $handler): bool
    {
        return $this->add(
            RequestMethodInterface::METHOD_PURGE,
            $pattern,
            $handler
        );
    }

    /** @inheritdoc */
    public function put(string $pattern, $handler): bool
    {
        return $this->add(
            RequestMethodInterface::METHOD_PUT,
            $pattern,
            $handler
        );
    }

    /** @inheritdoc */
    public function trace(string $pattern, $handler): bool
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

    /** @internal */
    protected function getDispatcher(): Dispatcher
    {
        $dispatcherName = 'FastRoute\\Dispatcher\\' . $this->driver;
        return new $dispatcherName($this->getDispatchData());
    }
}
