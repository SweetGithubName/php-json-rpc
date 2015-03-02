<?php

require_once dirname(__DIR__) . '/bootstrap.php';

use JsonRpc\Transport\Http\Client;

$client = new Client('http://api/server.php');
$client->query(1, 'Example/Math/subtract', array(3, 2));
$reply = $client->send();

echo json_encode($reply), "\n";
