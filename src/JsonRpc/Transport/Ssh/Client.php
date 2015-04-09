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
 * @author Spencer Mortensen <smortensen@datto.com>
 * @author Matt Coleman <matt@datto.com>
 * @license http://www.gnu.org/licenses/lgpl-3.0.html LGPL-3.0
 * @copyright 2015 Datto, Inc.
 */

namespace Datto\JsonRpc\Transport\Ssh;

use Datto\JsonRpc\Message;
use Datto\JsonRpc\Transport;

class Client implements Transport\Client
{
    /** @var string */
    protected $command;

    /** @var Message\Client */
    protected $client;

    public function __construct($host, $user, $command, $keyfile = null)
    {
        $this->command = self::getSshCommand($host, $user, $keyfile, $command);
        $this->client = new Message\Client();
    }

    public function notification($method, $arguments)
    {
        $this->client->notification($method, $arguments);
    }

    public function query($id, $method, $arguments)
    {
        $this->client->query($id, $method, $arguments);
    }

    public function send()
    {
        $message = $this->client->encode();
        $reply = $this->execute($this->command, $message);
        return $this->client->decode($reply);
    }

    private static function execute($executable, $input)
    {
        $descriptorSpec = array(
            array('pipe', 'r'),
            array('pipe', 'w')
        );

        $process = proc_open($executable, $descriptorSpec, $pipes);

        if (!is_resource($process)) {
            return null;
        }

        $stdin = &$pipes[0];
        fwrite($stdin, $input);
        fclose($stdin);

        $stdout = &$pipes[1];
        $result = stream_get_contents($stdout);
        fclose($stdout);

        $exitCode = proc_close($process);

        if ($exitCode !== 0) {
            return null;
        }

        return $result;
    }

    private static function getSshCommand($host, $user, $keyfile, $command)
    {
        $sshCommand = 'ssh';

        if ($keyfile !== null) {
            $sshCommand .= ' -i ' . escapeshellarg($keyfile);
        }

        $sshCommand .=
            ' -l ' . escapeshellarg($user) .
            ' ' . escapeshellarg($host) .
            ' -- ' . escapeshellarg($command);

        return $sshCommand;
    }
}
