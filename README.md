# JSON-RPC for PHP

## Features

* Fully compliant with the [JSON-RPC 2.0 specifications](http://www.jsonrpc.org/specification) with 100% unit-test coverage
* Customizable:
  * You can make JSON-RPC requests over HTTP or SSH, or locally through PHP
  * You can choose your own system for interpreting the JSON-RPC method strings
* Lightweight and flexible; works even when CURL is not installed

## Requirements

* PHP >= 5.3

## License

This package is released under an open-source license: [LGPL-3.0](https://www.gnu.org/licenses/lgpl-3.0.html)

## Installation

If you're using [Composer](https://getcomposer.org/) as your dependency
management system, you can install the source code like this:
```
composer require datto/php-json-rpc
```

## Examples

### Client

```php
$client = new Client('http://api.example.com');

$client->query(1, 'Math/subtract', [3, 2]);

$reply = $client->send();
```

### Server

```php
$translator = new MethodTranslator();
$server = new Server($translator);

$server->reply();
```

*See the "examples" folder for ready-to-use examples.*

## Unit tests

You can run the suite of unit tests from the project directory like this:
```
./vendor/bin/phpunit
```

## Author

[Spencer Mortensen](http://spencermortensen.com/contact/)
