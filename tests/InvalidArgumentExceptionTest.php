<?php
/**
 * @package   Atanvarno\Router
 * @author    atanvarno69 <https://github.com/atanvarno69>
 * @copyright 2017 atanvarno.com
 * @license   https://opensource.org/licenses/MIT The MIT License
 */

namespace Atanvarno\Router\Test;

/** SPL use block. */
use Throwable;

/** PHP Unit use block. */
use PHPUnit\Framework\TestCase;

/** Package use block. */
use Atanvarno\Router\{
    RouterException,
    Exception\InvalidArgumentException
};

class InvalidArgumentExceptionTest extends TestCase
{
    public function testImplementsRouterException()
    {
        $exception = new InvalidArgumentException();
        $this->assertInstanceOf(RouterException::class, $exception);
    }

    public function testIsThrowable()
    {
        $exception = new InvalidArgumentException();
        $this->assertInstanceOf(Throwable::class, $exception);
    }
}
