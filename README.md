# JSON-RPC for PHP

## Features

* Fully unit tested and fully compliant with the [JSON-RPC 2.0 specifications](http://www.jsonrpc.org/specification)
* Minimalistic and free from external dependencies: works even without CURL
* Modular, allowing you to make JSON-RPC calls over several transport mechanisms
* Customizable, allowing you to choose your own system for evaluating the "method" strings

## Requirements

* PHP >= 5.3

## Installation

If you're using [Composer](https://getcomposer.org/) as your dependency
management system, you can install the source code like this:
```
composer require datto/php-json-rpc
```

## Examples

### Client

```php
$client = new Client();
$client->query(1, 'Example/Math/subtract', array(3, 2));
$reply = $client->send();
```

### Server

```php
Server::reply();
```

> See the "examples" directory for more examples.

## Unit tests

You can run the suite of unit tests like this:
```
cd tests
php phpunit.phar .
```

## License

[GPL-3.0](http://www.gnu.org/licenses/gpl-3.0.html)


## Author

[Spencer Mortensen](http://spencermortensen.com)
