<?php

require_once dirname(__DIR__) . '/autoload.php';

spl_autoload_register('autoloadSource');
spl_autoload_register('autoloadExample');

use DattoApi\Participant\Client;
use DattoApi\Message\Query;

$client = new Client('http://api/server.php');

$query = new Query(1, 'Example/Math/subtract', array(3, 2));
$reply = $client->send($query);

echo json_encode($reply), "\n";
