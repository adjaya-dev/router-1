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
 * Atanvarno\Router\Result\MatchedResult
 *
 * Class representing a matched route result.
 *
 * @internal
 */
class MatchedResult extends BaseResult
{
    /** @inheritdoc */
    public function __construct(array $resultArray)
    {
        if ($resultsArray[0] !== Dispatcher::FOUND) {
            throw new InvalidArgumentException();
        }
        $this->allowed = [];
        $this->attributes = $resultArray[2];
        $this->handler = $resultArray[1];
        $this->status = StatusCodeInterface::STATUS_OK;
    }
}
