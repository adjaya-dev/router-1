<?php
/**
 * @package   Atanvarno\Router
 * @author    atanvarno69 <https://github.com/atanvarno69>
 * @copyright 2017 atanvarno.com
 * @license   https://opensource.org/licenses/MIT The MIT License
 */

namespace Atanvarno\Router\Router;

/** PSR-16 use block. */
use Psr\SimpleCache\CacheInterface;

/**
 * Atanvarno\Router\CachedRouter
 *
 * Implementation of `Router` using a PSR-16 cache.
 *
 * @api
 */
class CachedRouter extends SimpleRouter
{
    /**
     * @internal Class properties.
     *
     * @var CacheInterface $cache        PSR-16 cache.
     * @var string         $cacheKey     Key for cached dispatch data.
     * @var bool           $cacheDisable Flag to disable caching.
     */
    private $cache, $cacheKey, $cacheDisable;

    /**
     * Builds a `CachedRouter` instance.
     *
     * @param CacheInterface $cache        PSR-16 cache to use.
     * @param string         $cacheKey     Key to use for cached data.
     * @param string         $driver       FastRoute driver. You should use the
     *      `Router::DRIVER_*` constants.
     * @param array          $routes       Array of `add()` parameter values.
     * @param bool           $cacheDisable Disable cache functionality.
     */
    public function __construct(
        CacheInterface $cache,
        string $cacheKey = 'routerData',
        $driver = Router::DRIVER_GROUP_COUNT,
        array $routes = [],
        bool $cacheDisable = false
    ) {
        $this->cache = $cache;
        $this->cacheKey = $cacheKey;
        $this->cacheDisable = $cacheDisable;
        parent::__construct($driver, $routes);
    }

    /**
     * Generates dispatch data and caches it.
     *
     * This is useful if the routes configuration has changed, for example.
     *
     * Note this method will still work when `$cacheDisable = true` has been
     * passed to the constructor.
     *
     * @return bool The result of the `CacheInterface::set()` call.
     */
    public function refreshCache(): bool
    {
        $dispatchData = parent::getDispatchData();
        return $this->cache->set($this->cacheKey, $dispatchData);
    }

    /** @internal */
    protected function getDispatchData(): array
    {
        if (!$this->cacheDisable) {
            $dispatchData = $this->cache->get($this->cacheKey, null);
            if (!is_array($dispatchData)) {
                $this->refreshCache();
                $dispatchData = $this->cache->get($this->cacheKey, null);
            }
            if (is_array($dispatchData)) {
                return $dispatchData;
            }
        }
        return parent::getDispatchData();
    }
}
