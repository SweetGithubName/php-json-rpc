<?php

require_once dirname(__DIR__) . '/autoload.php';

spl_autoload_register('autoloadSource');
spl_autoload_register('autoloadExample');

use DattoApi\Oauth2\Server;

$server = new Server();
$server->run();
