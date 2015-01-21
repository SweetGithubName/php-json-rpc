<?php

require_once dirname(__DIR__) . '/autoload.php';

spl_autoload_register('autoloadSource');
spl_autoload_register('autoloadExample');

use DattoApi\JsonRpc\Server;

$json = '{"jsonrpc":"2.0","id":1,"method":"Example/Math:subtract","params":[3,2]}';

$server = new Server();
$out = $server->evaluate($json);

var_dump($out);
