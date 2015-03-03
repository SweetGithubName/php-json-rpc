<?php

/**
 * Copyright (C) 2015 Datto, Inc.
 *
 * This file is part of PHP JSON-RPC.
 *
 * PHP JSON-RPC is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License, version 3,
 * as published by the Free Software Foundation.
 *
 * PHP JSON-RPC is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with PHP JSON-RPC. If not, see <http://www.gnu.org/licenses/>.
 *
 * @author Spencer Mortensen <smortensen@datto.com>
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL-3.0
 * @copyright 2015 Datto, Inc.
 */

namespace JsonRpc\Transport\Local;

use JsonRpc\Data;
use JsonRpc\Transport;

class Client implements Transport\Client
{
    /** @var Data\Client */
    private $client;

    /** @var Data\Server */
    private $server;

    public function __construct()
    {
        $this->client = new Data\Client();
        $this->server = new Data\Server();
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
        $reply = $this->server->reply($message);
        return $this->client->decode($reply);
    }
}
