<?php
/**
 * @package   Atanvarno\Router
 * @author    atanvarno69 <https://github.com/atanvarno69>
 * @copyright 2017 atanvarno.com
 * @license   https://opensource.org/licenses/MIT The MIT License
 */

namespace Atanvarno\Router;

/** PSR-16 use block. */
use Psr\SimpleCache\CacheInterface;

class CachedRouter extends SimpleRouter
{
    private $cache, $cacheKey, $cacheDisable;

    public function __construct(
        CacheInterface $cache,
        string $cacheKey = 'routerData',
        $driver = Router::GROUP_COUNT,
        array $routes = [],
        bool $cacheDisable = false
    ) {
        $this->cache = $cache;
        $this->cacheKey = $cacheKey;
        $this->cacheDisable = $cacheDisable;
        parent::__construct($driver, $routes);
    }

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
