<?php
/**
 * @package   Atanvarno\Router
 * @author    atanvarno69 <https://github.com/atanvarno69>
 * @copyright 2017 atanvarno.com
 * @license   https://opensource.org/licenses/MIT The MIT License
 */

namespace Atanvarno\Router;

/**
 * Atanvarno\Router\Result
 *
 * Interface for dispatch result value objects.
 *
 * @api
 */
interface Result
{
    /**
     * Gets the allowed HTTP methods for the matched route.
     *
     * This is useful when you need to populate a `405` response `Allow:` 
     * header.
     *
     * When the status is not `405`, returns an empty array.
     *
     * @return string[] Allowed HTTP methods.
     */
    public function getAllowed(): array;
    
    /**
     * Gets a dictionary of placeholder names and values for the matched route.
     *
     * When the status is not `404` or there were no placeholders in the matched
     * route, returns an empty array.
     *
     * @return string[] Placeholder name => value dictionary.
     */
    public function getAttributes(): array;
    
    /**
     * Gets a handler for the matched route.
     *
     * When the status is not `404`, returns `null`.
     *
     * @return mixed The handler associated with the route.
     */
    public function getHandler();
    
    /**
     * Gets the HTTP status code for the route match.
     *
     * + `200` is returned for a matched route.
     * + `404` is returned for a not matched route.
     * + `405` is returned for a method not allowed matched route.
     *
     * @return int HTTP status code.
     */
    public function getStatus(): int;
}
