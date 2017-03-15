<?php
/**
 * @package   Atanvarno\Router
 * @author    atanvarno69 <https://github.com/atanvarno69>
 * @copyright 2017 atanvarno.com
 * @license   https://opensource.org/licenses/MIT The MIT License
 */

namespace Atanvarno\Router\Exception;

/** SPL use block. */
use UnexpectedValueException;

class MethodNotAllowedException extends UnexpectedValueException
{
    /** @var string[] $allowed Allowed HTTP methods. */
    protected $allowed;

    public function __construct(array $allowed, string $actual)
    {
        $this->allowed = $allowed;
        $msg = sprintf('$s is not allowed for this route', $actual);
        parent::__construct($msg, 405);
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
