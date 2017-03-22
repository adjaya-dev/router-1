<?php
/**
 * @package   Atanvarno\Router
 * @author    atanvarno69 <https://github.com/atanvarno69>
 * @copyright 2017 atanvarno.com
 * @license   https://opensource.org/licenses/MIT The MIT License
 */

namespace Atanvarno\Router\Result;

/** Package use block. */
use Atanvarno\Router\Result;

/**
 * Atanvarno\Router\Result\BaseResult
 *
 * Base class containing `Result` implementation boilerplate.
 *
 * @internal
 */
abstract class BaseResult implements Result
{
    /**
     * @internal Class properties.
     *
     * @var string[] $allowed    Allowed HTTP methods.
     * @var string[] $attributes Placeholder name => value dictionary.
     * @var mixed    $handler    Handler associated with the route.
     * @var int      $status     HTTP status code.
     */
    protected $allowed, $attributes, $handler, $status;
    
    /**
     * Constructor MUST accept a results array from 
     * `FastRoute\Dispatcher::dispatch()`.
     */
    abstract public function __construct(array $resultArray);
    
    /** @inheritdoc */
    public function getAllowed(): array
    {
        return $this->allowed;
    }
    
    /** @inheritdoc */
    public function getAttributes(): array
    {
        return $this->attributes;
    }
    
    /** @inheritdoc */
    public function getHandler()
    {
        return $this->handler;
    }
    
    /** @inheritdoc */
    public function getStatus(): int
    {
        return $this->status;
    }
}
