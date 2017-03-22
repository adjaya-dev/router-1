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
 * Atanvarno\Router\Result\MethodNotAllowedResult
 *
 * Class representing a matched route result.
 *
 * @internal
 */
class MethodNotAllowedResult extends BaseResult
{
    /** @inheritdoc */
    public function __construct(array $resultsArray)
    {
        if ($resultsArray[0] !== Dispatcher::METHOD_NOT_ALLOWED) {
            throw new InvalidArgumentException();
        }
        $this->allowed = $resultsArray[1];
        $this->attributes = [];
        $this->handler = null;
        $this->status = StatusCodeInterface::STATUS_METHOD_NOT_ALLOWED;
        if (in_array(Router::METHOD_GET, $this->allowed)
         && !in_array(Router::METHOD_HEAD, $this->allowed)
        ) {
            $this->allowed[] = Router::METHOD_HEAD;
        }
    }
}
