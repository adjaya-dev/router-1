<?php
/**
 * @package   Atanvarno\Router
 * @author    atanvarno69 <https://github.com/atanvarno69>
 * @copyright 2017 atanvarno.com
 * @license   https://opensource.org/licenses/MIT The MIT License
 */

namespace Atanvarno\Router\Exception;

/** SPL use block. */
use Exception;

/** Package use block. */
use Atanvarno\Router\RouterException;

/**
 * Atanvarno\Router\Exception\InvalidArgumentException
 *
 * Thrown when an argument passed by the user is invalid.
 *
 * @api
 */
class InvalidArgumentException extends Exception implements RouterException
{

}
