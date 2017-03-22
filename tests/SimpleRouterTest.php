<?php
/**
 * @package   Atanvarno\Router
 * @author    atanvarno69 <https://github.com/atanvarno69>
 * @copyright 2017 atanvarno.com
 * @license   https://opensource.org/licenses/MIT The MIT License
 */

namespace Atanvarno\Router\Test;

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
    Result,
    Router,
    Router\SimpleRouter
};
use Atanvarno\Router\Exception\{
    InvalidArgumentException, MethodNotAllowedException, NotFoundException
};

class SimpleRouterTest extends TestCase
{
    /** @var Router $router */
    private $router;

    public function setUp()
    {
        $this->router = new SimpleRouter();
    }

    public function testImplementsInterface()
    {
        $this->assertInstanceOf(Router::class, $this->router);
    }

    public function testConstructorWithValidDrivers()
    {
        foreach (Router::VALID_DRIVERS as $driver) {
            $router = new SimpleRouter($driver);
            $this->assertInstanceOf(Router::class, $router);
        }
    }

    public function testConstructorRejectsInvalidDriver()
    {
        $this->expectException(InvalidArgumentException::class);
        new SimpleRouter('invalid');
    }

    public function testConstructorRejectsNonArrayRouteDefinition()
    {
        $this->expectException(InvalidArgumentException::class);
        new SimpleRouter(Router::DRIVER_GROUP_COUNT, ['route']);
    }

    public function testConstructorRejectsTooShortRouteDefinition()
    {
        $this->expectException(InvalidArgumentException::class);
        new SimpleRouter(
            Router::DRIVER_GROUP_COUNT,
            [[RequestMethodInterface::METHOD_HEAD, '/pattern']]
        );
    }

    public function testConstructorRejectsTooLongRouteDefinition()
    {
        $this->expectException(InvalidArgumentException::class);
        new SimpleRouter(
            Router::DRIVER_GROUP_COUNT,
            [
                [
                    RequestMethodInterface::METHOD_HEAD,
                    '/pattern',
                    'handler',
                    'extra'
                ]
            ]
        );
    }

    public function testConstructorCorrectlyBubblesAddException()
    {
        $this->expectException(InvalidArgumentException::class);
        new SimpleRouter(
            Router::DRIVER_GROUP_COUNT,
            [['invalid', '/pattern', 'handler']]
        );
    }

    public function testAdd()
    {
        $result = $this->router->add(
            RequestMethodInterface::METHOD_HEAD, '/pattern', 'handler'
        );
        $expected = [
            [RequestMethodInterface::METHOD_HEAD, '/pattern', 'handler'],
        ];
        $this->assertAttributeEquals($expected, 'routes', $this->router);
        $this->assertSame($this->router, $result);
    }

    public function testAddWithArrayMethods()
    {
        $this->router->add(
            [
                RequestMethodInterface::METHOD_HEAD,
                RequestMethodInterface::METHOD_POST
            ],
            '/pattern',
            'handler'
        );
        $expected = [
            [
                [
                    RequestMethodInterface::METHOD_HEAD,
                    RequestMethodInterface::METHOD_POST
                ],
                '/pattern',
                'handler'
            ],
        ];
        $this->assertAttributeEquals($expected, 'routes', $this->router);
    }

    public function testAddNormalisesPattern()
    {
        $this->router->add(
            RequestMethodInterface::METHOD_HEAD, 'pattern/', 'handler'
        );
        $expected = [
            [RequestMethodInterface::METHOD_HEAD, '/pattern', 'handler'],
        ];
        $this->assertAttributeEquals($expected, 'routes', $this->router);
    }

    public function testAddRejectsNonStringSingleMethod()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->router->add(5, '/pattern', 'handler');
    }

    public function testAddRejectsNonStringArrayMethod()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->router->add(
            [RequestMethodInterface::METHOD_HEAD, 5], '/pattern', 'handler'
        );
    }

    public function testAddRejectsInvalidSingleMethod()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->router->add('INVALID', '/pattern', 'handler');
    }

    public function testAddRejectsInvalidArrayMethod()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->router->add(
            [RequestMethodInterface::METHOD_HEAD, 'INVALID'],
            '/pattern',
            'handler'
        );
    }

    public function testDispatch()
    {
        $uri = (new UriFactory())->createUri('http://atanvarno.com/test/uri/');
        foreach (Router::VALID_HTTP_METHODS as $method) {
            $this->router->add($method, '/{name}/uri', 'handler');
            $request = (new RequestFactory())->createRequest($method, $uri);
            $result = $this->router->dispatch($request);
            $this->assertInstanceOf(Result::class, $result);
            $this->assertSame([], $result->getAllowed());
            $this->assertSame(['name' => 'test'], $result->getAttributes());
            $this->assertSame('handler', $result->getHandler());
            $this->assertSame(200, $result->getStatus());
            $this->setUp();
        }
    }

    public function testDispatchWithNotFound()
    {
        $uri = (new UriFactory())->createUri('http://atanvarno.com/test/uri/');
        $request = (new RequestFactory())
            ->createRequest(RequestMethodInterface::METHOD_HEAD, $uri);
        $result = $this->router->dispatch($request);
        $this->assertInstanceOf(Result::class, $result);
        $this->assertSame([], $result->getAllowed());
        $this->assertSame([], $result->getAttributes());
        $this->assertSame(null, $result->getHandler());
        $this->assertSame(404, $result->getStatus());
    }

    public function testDispatchWithNotAllowed()
    {
        $this->router->add(
            RequestMethodInterface::METHOD_GET, '/{name}/uri', 'handler'
        );
        $uri = (new UriFactory())->createUri('http://atanvarno.com/test/uri/');
        $request = (new RequestFactory())
            ->createRequest(RequestMethodInterface::METHOD_POST, $uri);
        $result = $this->router->dispatch($request);
        $this->assertInstanceOf(Result::class, $result);
        $this->assertSame(
            [RequestMethodInterface::METHOD_GET, RequestMethodInterface::METHOD_HEAD],
            $result->getAllowed()
        );
        $this->assertSame([], $result->getAttributes());
        $this->assertSame(null, $result->getHandler());
        $this->assertSame(405, $result->getStatus());
        $expected = [
            Dispatcher::METHOD_NOT_ALLOWED, [RequestMethodInterface::METHOD_GET]
        ];
        $this->assertSame($expected, $result);
    }

    public function testConnect()
    {
        $result = $this->router->connect('/pattern', 'handler');
        $expected = [
            [RequestMethodInterface::METHOD_CONNECT, '/pattern', 'handler'],
        ];
        $this->assertAttributeEquals($expected, 'routes', $this->router);
        $this->assertSame($this->router, $result);
    }

    public function testDelete()
    {
        $result = $this->router->delete('/pattern', 'handler');
        $expected = [
            [RequestMethodInterface::METHOD_DELETE, '/pattern', 'handler'],
        ];
        $this->assertAttributeEquals($expected, 'routes', $this->router);
        $this->assertSame($this->router, $result);
    }

    public function testGet()
    {
        $result = $this->router->get('/pattern', 'handler');
        $expected = [
            [RequestMethodInterface::METHOD_GET, '/pattern', 'handler'],
        ];
        $this->assertAttributeEquals($expected, 'routes', $this->router);
        $this->assertSame($this->router, $result);
    }

    public function testHead()
    {
        $result = $this->router->head('/pattern', 'handler');
        $expected = [
            [RequestMethodInterface::METHOD_HEAD, '/pattern', 'handler'],
        ];
        $this->assertAttributeEquals($expected, 'routes', $this->router);
        $this->assertSame($this->router, $result);
    }

    public function testOptions()
    {
        $result = $this->router->options('/pattern', 'handler');
        $expected = [
            [RequestMethodInterface::METHOD_OPTIONS, '/pattern', 'handler'],
        ];
        $this->assertAttributeEquals($expected, 'routes', $this->router);
        $this->assertSame($this->router, $result);
    }

    public function testPatch()
    {
        $result = $this->router->patch('/pattern', 'handler');
        $expected = [
            [RequestMethodInterface::METHOD_PATCH, '/pattern', 'handler'],
        ];
        $this->assertAttributeEquals($expected, 'routes', $this->router);
        $this->assertSame($this->router, $result);
    }

    public function testPost()
    {
        $result = $this->router->post('/pattern', 'handler');
        $expected = [
            [RequestMethodInterface::METHOD_POST, '/pattern', 'handler'],
        ];
        $this->assertAttributeEquals($expected, 'routes', $this->router);
        $this->assertSame($this->router, $result);
    }

    public function testPurge()
    {
        $result = $this->router->purge('/pattern', 'handler');
        $expected = [
            [RequestMethodInterface::METHOD_PURGE, '/pattern', 'handler'],
        ];
        $this->assertAttributeEquals($expected, 'routes', $this->router);
        $this->assertSame($this->router, $result);
    }

    public function testPut()
    {
        $result = $this->router->put('/pattern', 'handler');
        $expected = [
            [RequestMethodInterface::METHOD_PUT, '/pattern', 'handler'],
        ];
        $this->assertAttributeEquals($expected, 'routes', $this->router);
        $this->assertSame($this->router, $result);
    }

    public function testTrace()
    {
        $result = $this->router->trace('/pattern', 'handler');
        $expected = [
            [RequestMethodInterface::METHOD_TRACE, '/pattern', 'handler'],
        ];
        $this->assertAttributeEquals($expected, 'routes', $this->router);
        $this->assertSame($this->router, $result);
    }
}
