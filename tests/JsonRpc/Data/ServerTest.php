<?php

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
        $actualJsonOutput = $this->server->process($input);

        $expectedOutput = json_decode($expectedJsonOutput, true);
        $actualOutput = json_decode($actualJsonOutput, true);

        $this->assertEquals($expectedOutput, $actualOutput);
    }
}
