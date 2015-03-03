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

namespace JsonRpc\Data;

use PHPUnit_Framework_TestCase;

class ServerTest extends PHPUnit_Framework_TestCase
{
    /** @var Server */
    private $server;

    public function setUp()
    {
        $this->server = new Server();
    }

    public function testArgumentsPositionalA()
    {
        $input = '{"jsonrpc": "2.0", "method": "Example/Math/subtract", "params": [3, 2], "id": 1}';

        $output = '{"jsonrpc": "2.0", "result": 1, "id": 1}';

        $this->compare($input, $output);
    }

    public function testArgumentsPositionalB()
    {
        $input = '{"jsonrpc": "2.0", "method": "Example/Math/subtract", "params": [2, 3], "id": 1}';

        $output = '{"jsonrpc": "2.0", "result": -1, "id": 1}';

        $this->compare($input, $output);
    }

    public function testArgumentsNamedA()
    {
        $input = '{"jsonrpc": "2.0", "method": "Example/Math/subtract", "params": {"minuend": 3, "subtrahend": 2}, "id": 1}';

        $output = '{"jsonrpc": "2.0", "result": 1, "id": 1}';

        $this->compare($input, $output);
    }

    public function testArgumentsNamedB()
    {
        $input = '{"jsonrpc": "2.0", "method": "Example/Math/subtract", "params": {"subtrahend": 2, "minuend": 3}, "id": 1}';

        $output = '{"jsonrpc": "2.0", "result": 1, "id": 1}';

        $this->compare($input, $output);
    }

    public function testNotificationArguments()
    {
        $input = '{"jsonrpc": "2.0", "method": "Example/Math/subtract", "params": [3, 2]}';

        $output = 'null';

        $this->compare($input, $output);
    }

    public function testNotification()
    {
        $input = '{"jsonrpc": "2.0", "method": "Example/Math/subtract"}';

        $output = 'null';

        $this->compare($input, $output);
    }

    public function testUndefinedMethod()
    {
        $input ='{"jsonrpc": "2.0", "method": "Example/Math/undefined", "id": "1"}';

        $output = '{"jsonrpc": "2.0", "error": {"code": -32601, "message": "Method not found"}, "id": "1"}';

        $this->compare($input, $output);
    }

    public function testInvalidJson()
    {
        $input = '{"jsonrpc": "2.0", "method": "foobar", "params": "bar", "baz]';

        $output = '{"jsonrpc": "2.0", "error": {"code": -32700, "message": "Parse error"}, "id": null}';

        $this->compare($input, $output);
    }

    public function testInvalidRequest()
    {
        $input = '{"jsonrpc": "2.0", "method": 1, "params": "bar"}';

        $output = '{"jsonrpc": "2.0", "error": {"code": -32600, "message": "Invalid Request"}, "id": null}';

        $this->compare($input, $output);
    }

    public function testBatchInvalidJson()
    {
        $input = ' [
            {"jsonrpc": "2.0", "method": "Example/Math/subtract", "params": [1, 2, 4], "id": "1"},
            {"jsonrpc": "2.0", "method"
        ]';

        $output = '{"jsonrpc": "2.0", "error": {"code": -32700, "message": "Parse error"}, "id": null}';

        $this->compare($input, $output);
    }

    public function testBatchEmpty()
    {
        $input = '[
        ]';

        $output = '{"jsonrpc": "2.0", "error": {"code": -32600, "message": "Invalid Request"}, "id": null}';

        $this->compare($input, $output);
    }

    public function testBatchInvalidElement()
    {
        $input = '[
            1
        ]';

        $output = '[
            {"jsonrpc": "2.0", "error": {"code": -32600, "message": "Invalid Request"}, "id": null}
        ]';

        $this->compare($input, $output);
    }

    public function testBatchInvalidElements()
    {
        $input = '[
            1,
            2,
            3
        ]';

        $output = '[
            {"jsonrpc": "2.0", "error": {"code": -32600, "message": "Invalid Request"}, "id": null},
            {"jsonrpc": "2.0", "error": {"code": -32600, "message": "Invalid Request"}, "id": null},
            {"jsonrpc": "2.0", "error": {"code": -32600, "message": "Invalid Request"}, "id": null}
        ]';

        $this->compare($input, $output);
    }

    public function testBatch()
    {
        $input = '[
            {"jsonrpc": "2.0", "method": "Example/Math/subtract", "params": [1, -1], "id": "1"},
            {"jsonrpc": "2.0", "method": "Example/Math/subtract", "params": [1, -1]},
            {"foo": "boo"},
            {"jsonrpc": "2.0", "method": "undefined", "params": {"name": "myself"}, "id": "5"}
        ]';

        $output = '[
            {"jsonrpc": "2.0", "result": 2, "id": "1"},
            {"jsonrpc": "2.0", "error": {"code": -32600, "message": "Invalid Request"}, "id": null},
            {"jsonrpc": "2.0", "error": {"code": -32601, "message": "Method not found"}, "id": "5"}
        ]';

        $this->compare($input, $output);
    }

    public function testBatchNotifications()
    {
        $input = '[
            {"jsonrpc": "2.0", "method": "Example/Math/subtract", "params": [4, 2]},
            {"jsonrpc": "2.0", "method": "Example/Math/subtract", "params": [3, 7]}
        ]';

        $output = 'null';

        $this->compare($input, $output);
    }

    private function compare($input, $expectedJsonOutput)
    {
        $actualJsonOutput = $this->server->reply($input);

        $expectedOutput = json_decode($expectedJsonOutput, true);
        $actualOutput = json_decode($actualJsonOutput, true);

        $this->assertEquals($expectedOutput, $actualOutput);
    }
}
