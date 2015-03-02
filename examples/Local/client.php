<?php

require_once dirname(__DIR__) . '/bootstrap.php';

use JsonRpc\Transport\Local\Client;

$client = new Client();
$client->query(1, 'Example/Math/subtract', array(3, 2));
$reply = $client->send();

echo json_encode($reply), "\n";
