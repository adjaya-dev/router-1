# Atanvarno\Router\Exception\NotFoundException
Thrown when a route is matched but uses an invalid HTTP method. The user 
should return a `405 Method Not Allowed` error response.
```php
class MethodNotAllowedException extends Exception implements RouterException
{
    // Properties
    protected string $message
    protected int $code
    protected string $file
    protected int $line
    
    // Methods
    public function __construct(array $allowed, string $actual, int $code = 0, Throwable $previous = null)
    public function __toString(): string
    final private function __clone()
    final public function getAllowed(): array
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

> **Note:** a `405` response is required to include an `Allow:` header listing 
valid methods for the requested URL. You can use the [`getAllowed()`](#getallowed) 
method.

* [__construct](#construct)
* [__toString](http://php.net/manual/en/exception.tostring.php)
* [__clone](http://php.net/manual/en/exception.clone.php)
* [getAllowed](#getallowed)
* [getCode](http://php.net/manual/en/exception.getcode.php)
* [getFile](http://php.net/manual/en/exception.getfile.php)
* [getLine](http://php.net/manual/en/exception.getline.php)
* [getMessage](http://php.net/manual/en/exception.getmessage.php)
* [getPrevious](http://php.net/manual/en/exception.getprevious.php)
* [getTrace](http://php.net/manual/en/exception.gettrace.php)
* [getTraceAsString](http://php.net/manual/en/exception.gettraceasstring.php)

## construct
Builds a `MethodNotAllowedException` instance.
```php
__construct(array $allowed, string $actual, int $code = 0, Throwable $previous = null)
```

### Parameters
* `string[]` **$allowed**

  Required. Array of allowed HTTP methods to be returned by [`getAllowed()`](#getallowed).

* `string` **$actual**

  Required. The actual HTTP method. Used to generate the message returned by [`getMessage()`](http://php.net/manual/en/exception.getmessage.php).
  
* `int` **$code**

  The Exception code.
  
* `Throwable` **$previous**

  The previous exception used for the exception chaining.

### Throws
Nothing is thrown.

### Returns
* `MethodNotAllowedException`

## getAllowed
Gets a list of allowed HTTP methods.
```php
getAllowed(): array
```
### Parameters
None.

### Throws
Nothing is thrown.

### Returns
* `string[]`

  Allowed HTTP methods.
