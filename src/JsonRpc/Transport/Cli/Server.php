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

/**
 * Command-line server.
 *
 * Receives JSON-RPC messages from stdin.
 * Writes responses to stdout.
 */
abstract class Server implements Transport\Server
{
    public static function reply()
    {
        $message = @file_get_contents('php://stdin');

        if (!is_array(json_decode($message, true))) {
            self::errorInvalidContentType();
        }

        $reply = Data\Server::reply($message);

        if ($reply === null) {
            self::successNoContent();
        }

        self::successContent($reply);
    }

    private static function errorInvalidContentType()
    {
        fwrite(STDERR, 'Non-JSON input data.');
        exit(1);
    }

    private static function successNoContent()
    {
        exit();
    }

    private static function successContent($content)
    {
        echo $content;
        exit();
    }
}
