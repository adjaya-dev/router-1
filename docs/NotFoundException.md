# Atanvarno\Router\Exception\NotFoundException
Thrown when a route could not be matched. The user SHOULD return a `404 Not 
Found` error response.
```php
class NotFoundException extends Exception implements RouterException
{
    // Properties
    protected string $message
    protected int $code
    protected string $file
    protected int $line
    
    // Methods
    public function __construct(string $message = '', int $code = 0, Throwable $previous = null)
    public function __toString(): string
    final private function __clone()
    final public function getCode()
    final public function getFile(): string
    final public function getLine(): int
    final public function getMessage(): string
    final public function getPrevious(): Throwable
    final public function getTrace(): array
    final public function getTraceAsString(): string
}
```
Extends [Exception](http://php.net/manual/en/class.exception.php).

Implements [RouterException](RouterException.md).

* [__construct](http://php.net/manual/en/exception.construct.php)
* [__toString](http://php.net/manual/en/exception.tostring.php)
* [__clone](http://php.net/manual/en/exception.clone.php)
* [getCode](http://php.net/manual/en/exception.getcode.php)
* [getFile](http://php.net/manual/en/exception.getfile.php)
* [getLine](http://php.net/manual/en/exception.getline.php)
* [getMessage](http://php.net/manual/en/exception.getmessage.php)
* [getPrevious](http://php.net/manual/en/exception.getprevious.php)
* [getTrace](http://php.net/manual/en/exception.gettrace.php)
* [getTraceAsString](http://php.net/manual/en/exception.gettraceasstring.php)
