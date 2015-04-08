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

namespace Datto\JsonRpc\Transport\Cli;

use Datto\JsonRpc\Data;
use Datto\JsonRpc\Transport;

class Client implements Transport\Client
{
    protected $command;

    /** @var Data\Client */
    protected $client;

    /**
     * @param string $command
     *   Path to a command that accepts JSON-RPC requests via stdin and outputs responses to stdout.
     */
    public function __construct($command)
    {
        $this->command = $command;
        $this->client = new Data\Client();
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
        $reply = $this->execute($message);
        return $this->client->decode($reply);
    }

    private function execute($content)
    {
        $descriptorspec = array(
            0 => array('pipe', 'r'),
            1 => array('pipe', 'w'),
            2 => array('pipe', 'w')
        );

        $process = proc_open($this->command, $descriptorspec, $pipes);

        fwrite($pipes[0], $content);
        fclose($pipes[0]);

        $result = stream_get_contents($pipes[1]);
        fclose($pipes[1]);

        $stderr = stream_get_contents($pipes[2]);
        fclose($pipes[2]);

        $retval = proc_close($process);

        if ($retval !== 0) {
            return null;
        }

        return $result;
    }
}
