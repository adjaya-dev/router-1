<?php
/**
 * @package   Atanvarno\Router
 * @author    atanvarno69 <https://github.com/atanvarno69>
 * @copyright 2017 atanvarno.com
 * @license   https://opensource.org/licenses/MIT The MIT License
 */

namespace Atanvarno\Router\Result;

/** HTTP Message Utilities use block. */
use Fig\Http\Message\StatusCodeInterface;

/** FastRoute use block. */
use FastRoute\Dispatcher;

/** Package use block. */
use Atanvarno\Router\Exception\InvalidArgumentException;

/**
 * Atanvarno\Router\Result\NotFoundResult
 *
 * Class representing a not found route result.
 *
 * @internal
 */
class NotFoundResult extends BaseResult
{
    /** @inheritdoc */
    public function __construct(array $resultsArray)
    {
        if ($resultsArray[0] !== Dispatcher::NOT_FOUND) {
            throw new InvalidArgumentException();
        }
        $this->allowed = [];
        $this->attributes = [];
        $this->handler = [];
        $this->status = StatusCodeInterface::STATUS_NOT_FOUND;
    }
}
