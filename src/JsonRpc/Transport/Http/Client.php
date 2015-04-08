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
 * @license http://www.gnu.org/licenses/lgpl-3.0.html LGPL-3.0
 * @copyright 2015 Datto, Inc.
 */

namespace Datto\JsonRpc\Transport\Http;

use Datto\JsonRpc\Data;
use Datto\JsonRpc\Transport;

class Client implements Transport\Client
{
    /** @var string */
    private $uri;

    /** @var Data\Client */
    private $client;

    public function __construct($uri)
    {
        $this->uri = $uri;
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
        $reply = $this->execute($message, 'POST', 'application/json');
        return $this->client->decode($reply);
    }

    private function execute($content, $method, $contentType)
    {
        $contentLength = strlen($content);

        $header = "Content-Type: {$contentType}\r\n" .
            "Content-Length: {$contentLength}\r\n" .
            "Accept: {$contentType}\r\n";

        $options = array(
            'http' => array(
                'method' => $method,
                'header' => $header,
                'content' => $content
            )
        );

        $context = stream_context_create($options);
        $reply = @file_get_contents($this->uri, false, $context);

        if ($reply === false) {
            return null;
        }

        return $reply;
    }
}
