<?php
/**
 * @package   Atanvarno\Router
 * @author    atanvarno69 <https://github.com/atanvarno69>
 * @copyright 2017 atanvarno.com
 * @license   https://opensource.org/licenses/MIT The MIT License
 */

namespace Atanvarno\Router\Test;

/** PSR-16 use block. */
use Psr\SimpleCache\CacheInterface;

/** PSR-17 use block. */
use Http\Factory\Diactoros\{
    RequestFactory, UriFactory
};

/** HTTP Message Utilities use block. */
use Fig\Http\Message\RequestMethodInterface;

/** PHP Unit use block. */
use PHPUnit\Framework\TestCase;

/** FastRoute use block. */
use FastRoute\Dispatcher;

/** Package use block. */
use Atanvarno\Router\{
    Router,
    Router\CachedRouter
};

/** Dependency use block. */
use Atanvarno\Cache\{
    Apcu\APCuDriver, Cache
};

class CachedRouterTest extends TestCase
{
    /** @var CacheInterface $cache */
    private $cache;

    private $request;

    /** @var Router $router */
    private $router;

    public function setUp()
    {
        $this->cache = new Cache(new APCuDriver());
        $this->router = new CachedRouter($this->cache, 'routerData');
        $uri = (new UriFactory())->createUri('http://atanvarno.com/test/uri/');
        $this->request = (new RequestFactory())->createRequest(
            RequestMethodInterface::METHOD_HEAD, $uri
        );
    }

    public function tearDown()
    {
        $this->cache->clear();
    }

    public function testImplementsInterface()
    {
        $this->assertInstanceOf(Router::class, $this->router);
    }

    public function testFirstRunPopulatesCache()
    {
        $this->assertFalse($this->cache->has('routerData'));
        $this->router->add(
            RequestMethodInterface::METHOD_HEAD,
            '/{name}/uri',
            'handler'
        );
        $result = $this->router->dispatch($this->request);
        $expected = [Dispatcher::FOUND, 'handler', ['name' => 'test']];
        $this->assertSame($expected, $result);
        $this->assertTrue($this->cache->has('routerData'));
    }

    public function testCachedDataIsUsed()
    {
        $data = [
            0 => [],
            1 => [
                'HEAD' => [
                    0 => [
                        'regex' => '~^(?|/([^/]+)/uri)$~',
                        'routeMap' => [
                            2 => [
                                0 => 'handler',
                                1 => [
                                    'name' => 'name'
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];
        $this->cache->set('routerData', $data);
        $result = $this->router->dispatch($this->request);
        $expected = [Dispatcher::FOUND, 'handler', ['name' => 'test']];
        $this->assertSame($expected, $result);
    }

    public function testCacheDisabledUsesSimpleRouter()
    {
        $router = new CachedRouter(
            $this->cache,
            'routerData',
            Router::DRIVER_GROUP_COUNT,
            [
                [
                    RequestMethodInterface::METHOD_HEAD,
                    '/{name}/uri',
                    'handler',
                ],
            ],
            true
        );
        $result = $router->dispatch($this->request);
        $expected = [Dispatcher::FOUND, 'handler', ['name' => 'test']];
        $this->assertSame($expected, $result);
        $this->assertFalse($this->cache->has('routerData'));
    }
}
