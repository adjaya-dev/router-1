<?php
/**
 * @package   Atanvarno\Router
 * @author    atanvarno69 <https://github.com/atanvarno69>
 * @copyright 2017 atanvarno.com
 * @license   https://opensource.org/licenses/MIT The MIT License
 */

namespace Atanvarno\Router;

use InvalidArgumentException;
use FastRoute\Dispatcher;
use FastRoute\RouteCollector;
use FastRoute\RouteParser\Std;
use Psr\Http\Message\ServerRequestInterface;
use Psr\SimpleCache\CacheInterface;

class Router
{
    const
        CHAR_COUNT = 'CharCountBased',
        GROUP_COUNT = 'GroupCountBased',
        GROUP_POS = 'GroupPosBased',
        MARK = 'MarkBased';

    /**
     * @var CacheInterface|null $cache
     * @var string|null         $cacheKey
     * @var RouteCollector      $collector
     * @var string              $driver
     * @var array               $routeData
     * @var bool                $update
     */
    private $cache, $cacheKey, $collector, $driver, $routeData, $update;

    public function __construct(
        array $routes,
        string $driver = Router::GROUP_COUNT,
        CacheInterface $cache = null,
        string $cacheKey = null,
        bool $updateCache = false
    ) {
        # todo: validate routes
        if (
            $driver !== Router::CHAR_COUNT
         && $driver !== Router::GROUP_COUNT
         && $driver !== Router::GROUP_POS
         && $driver !== Router::MARK
        ) {
            $msg = sprintf('%s is not a valid driver', $driver);
            throw new InvalidArgumentException($msg);
        }
        $this->routeData = $routes;
        $this->driver = $driver;
        $this->cache = $cache;
        $this->cacheKey = $cacheKey;
        $this->update = $updateCache;
    }

    public function add($httpMethod, string $routePattern, $handler)
    {
        $collector = $this->collector ?? $this->generateCollector();
        $collector->addRoute($httpMethod, $routePattern, $handler);
    }

    public function dispatch(ServerRequestInterface $request): array
    {
        $dispatcher = $this->getDispatcher();
        $method = $request->getMethod();
        $path = $request->getUri()->getPath();
        return $dispatcher->dispatch($method, $path);
    }

    public function connect($route, $handler)
    {
        $this->add('CONNECT', $route, $handler);
    }

    public function delete($route, $handler)
    {
        $this->add('DELETE', $route, $handler);
    }

    public function get(string $routePattern, $handler)
    {
        $this->add('GET', $routePattern, $handler);
    }

    public function head($route, $handler)
    {
        $this->add('HEAD', $route, $handler);
    }

    public function options($route, $handler)
    {
        $this->add('OPTIONS', $route, $handler);
    }

    public function patch($route, $handler)
    {
        $this->add('PATCH', $route, $handler);
    }

    public function post($route, $handler)
    {
        $this->add('POST', $route, $handler);
    }

    public function purge($route, $handler)
    {
        $this->add('PURGE', $route, $handler);
    }

    public function put($route, $handler)
    {
        $this->add('PUT', $route, $handler);
    }

    public function trace($route, $handler)
    {
        $this->add('TRACE', $route, $handler);
    }

    private function getDispatcher(): Dispatcher
    {
        if (isset($this->cache) && isset($this->cacheKey) && !$this->update) {
            $dispatcher = $this->cache->get($this->cacheKey);
            if ($dispatcher instanceof Dispatcher) {
                return $dispatcher;
            }
        }
        $collector = $this->collector ?? $this->generateCollector();
        $data = $collector->getData();
        $name = '\FastRoute\Dispatcher\\' . $this->driver;
        $dispatcher = new $name($data);
        if (isset($this->cache) && isset($this->cacheKey)) {
            $this->cache->set($this->cacheKey, $dispatcher);
        }
        return $dispatcher;
    }

    private function generateCollector(): RouteCollector
    {
        $name = '\FastRoute\DataGenerator\\' . $this->driver;
        $dataGenerator = new $name();
        $this->collector = new RouteCollector(new Std(), $dataGenerator);
        foreach ($this->routeData as $route) {
            $this->collector->addRoute($route[0], $route[1], $route[2]);
        }
        return $this->collector;
    }
}