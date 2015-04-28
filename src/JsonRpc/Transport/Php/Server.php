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

namespace Datto\JsonRpc\Transport\Php;

use Datto\JsonRpc\Transport;
use Datto\JsonRpc\Message;
use Datto\JsonRpc\MethodTranslator;

/**
 * Reads a JSON-RPC 2.0 request from STDIN and replies to STDOUT.
 */
class Server implements Transport\Server
{
    /** @var MethodTranslator */
    private $method;

    public function __construct(MethodTranslator $method)
    {
        $this->method = $method;
    }

    public function reply()
    {
        $message = @file_get_contents('php://stdin');

        if ($message === false) {
            self::errorInvalidBody();
        }

        $server = new Message\Server($this->method);
        $reply = $server->reply($message);

        if ($reply !== null) {
            echo $reply;
        }
    }

    private static function errorInvalidBody()
    {
        @file_put_contents('php://stderr', 'Invalid body');
        exit(1);
    }
}
