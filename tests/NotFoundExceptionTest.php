<?php
/**
 * @package   Atanvarno\Router
 * @author    atanvarno69 <https://github.com/atanvarno69>
 * @copyright 2017 atanvarno.com
 * @license   https://opensource.org/licenses/MIT The MIT License
 */

namespace Atanvarno\Router\Test;

/** SPL use block. */
use OutOfBoundsException;

/** PHP Unit use block. */
use PHPUnit\Framework\TestCase;

/** Package use block. */
use Atanvarno\Router\Exception\NotFoundException;

class NotFoundExceptionTest extends TestCase
{
    public function testExtendsOutOfBoundsException()
    {
        $exception = new NotFoundException();
        $this->assertInstanceOf(OutOfBoundsException::class, $exception);
    }
}
