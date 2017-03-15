<?php
/**
 * @package   Atanvarno\Router
 * @author    atanvarno69 <https://github.com/atanvarno69>
 * @copyright 2017 atanvarno.com
 * @license   https://opensource.org/licenses/MIT The MIT License
 */

namespace Atanvarno\Router\Test;

/** SPL use block. */
use InvalidArgumentException as SplInvalidArgumentException;

/** PHP Unit use block. */
use PHPUnit\Framework\TestCase;

/** Package use block. */
use Atanvarno\Router\Exception\InvalidArgumentException;

class InvalidArgumentExceptionTest extends TestCase
{
    public function testExtendsSplInvalidArgumentException()
    {
        $exception = new InvalidArgumentException();
        $this->assertInstanceOf(SplInvalidArgumentException::class, $exception);
    }
}
