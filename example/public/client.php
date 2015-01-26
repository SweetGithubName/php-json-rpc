<?php

require_once dirname(__DIR__) . '/autoload.php';

spl_autoload_register('autoloadSource');
spl_autoload_register('autoloadExample');

use DattoApi\Participant\Client;

$uri = 'http://api/server.php';

$arguments = array(
    'jsonrpc' => '2.0',
    'id' => 1,
    'method' => 'Example/Math:subtract',
    'params' => [3, 2]
);

$client = new Client($uri);
$reply = $client->send($arguments);

var_dump($reply);