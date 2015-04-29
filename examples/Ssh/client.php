<?php

/**
 * Copyright (C) 2015 Datto, Inc.
 *
 * This file is part of PHP JSON-RPC.
 *
 * PHP JSON-RPC is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License, version 3,
 * as published by the Free Software Foundation.
 *
 * PHP JSON-RPC is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with PHP JSON-RPC. If not, see <http://www.gnu.org/licenses/>.
 *
 * @author Matt Coleman <matt@datto.com>
 * @author Spencer Mortensen <smortensen@datto.com>
 * @license http://www.gnu.org/licenses/lgpl-3.0.html LGPL-3.0
 * @copyright 2015 Datto, Inc.
 */

require __DIR__ . '/../../vendor/autoload.php';

use Datto\JsonRpc\Transport\Ssh\Client;

$destination = getSshDestination();
$command = getRemoteCommand();
$options = getSshOptions();

$client = new Client($destination, $command, $options);

$client->query(1, 'Math/subtract', array(3, 2));

$reply = $client->send();
echo json_encode($reply), "\n";


function getSshDestination()
{
    $server = 'localhost';
    $user = posix_getpwuid(posix_geteuid());
    $username = $user['name'];
    return "{$username}@{$server}";
}

function getRemoteCommand()
{
    $scriptPath = realpath(__DIR__ . '/../Ssh/server.php');
    return 'php ' . escapeshellarg($scriptPath);
}

function getSshOptions()
{
    // Custom SSH command-line options:
    return array(
        'p' => 22, // use port 22
        'q' => null // enable quiet mode (which will suppress most warnings)
    );
}
