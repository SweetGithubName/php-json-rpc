<?php

require_once dirname(__DIR__) . '/bootstrap.php';

use JsonRpc\Transport\Http\Server;

$server = new Server();
$server->reply();
