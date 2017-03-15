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
    private $cache, $cacheKey, $cacheDisabled;

    public function __construct(
        CacheInterface $cache,
        string $cacheKey,
        $driver = Router::GROUP_COUNT,
        array $routes = [],
        bool $cacheDisabled = false
    ) {
        $this->cache = $cache;
        $this->cacheKey = $cacheKey;
        $this->cacheDisabled = $cacheDisabled;
        parent::__construct($driver, $routes);
    }

    /** @internal */
    protected function getDispatchData(): array
    {
        if ($this->cacheDisabled) {
            return parent::getDispatchData();
        }
        $dispatchData = $this->cache->get($this->cacheKey);
        if (is_array($dispatchData)) {
            return $dispatchData;
        }
        $dispatchData = parent::getDispatchData();
        $this->cache->set($this->cacheKey, $dispatchData);
        return $dispatchData;
    }
}
