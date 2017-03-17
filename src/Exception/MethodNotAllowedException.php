<?php
/**
 * @package   Atanvarno\Router
 * @author    atanvarno69 <https://github.com/atanvarno69>
 * @copyright 2017 atanvarno.com
 * @license   https://opensource.org/licenses/MIT The MIT License
 */

namespace Atanvarno\Router\Exception;

/** SPL use block. */
use Exception, Throwable;

/**
 * Atanvarno\Router\Exception\MethodNotAllowedException
 *
 * Thrown when a route is matched but uses an invalid HTTP method. The user
 * should return a `405 Method Not Allowed` error response.
 *
 * Note: a `405` response is required to include an `Allow:` header listing
 * valid methods for the requested URL. You can use the `getAllowed()` method.
 *
 * @api
 */
class MethodNotAllowedException extends Exception implements RouterException
{
    /**
     * @internal class property.
     *
     * @var string[] $allowed Allowed HTTP methods.
     */
    protected $allowed;

    /** @internal */
    public function __construct(
        array $allowed,
        string $actual,
        int $code = 0,
        Throwable $previous  = null
    ) {
        $this->allowed = $allowed;
        $msg = sprintf('Method %s is not allowed for this route', $actual);
        parent::__construct($msg, $code, $previous);
    }

    /**
     * Gets a list of allowed HTTP methods.
     *
     * @return string[] Allowed HTTP methods.
     */
    final public function getAllowed(): array
    {
        return $this->allowed;
    }
}
