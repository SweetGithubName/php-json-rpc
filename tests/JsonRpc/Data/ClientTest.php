<?php

namespace JsonRpc\Data;

use PHPUnit_Framework_TestCase;

class ClientTest extends PHPUnit_Framework_TestCase
{
    public function testNotification()
    {
        $client = new Client();
        $client->notification('Example/Math/subtract', array(3, 2));

        $this->compare($client, '{"jsonrpc":"2.0","method":"Example\/Math\/subtract","params":[3,2]}');
    }

    public function testQuery()
    {
        $client = new Client();
        $client->query(1, 'Example/Math/subtract', array(3, 2));

        $this->compare($client, '{"jsonrpc":"2.0","id":1,"method":"Example\/Math\/subtract","params":[3,2]}');
    }

    public function testBatch()
    {
        $client = new Client();
        $client->query(1, 'Example/Math/subtract', array(3, 2));
        $client->notification('Example/Math/subtract', array(4, 3));

        $this->compare($client, '[{"jsonrpc":"2.0","id":1,"method":"Example\/Math\/subtract","params":[3,2]},{"jsonrpc":"2.0","method":"Example\/Math\/subtract","params":[4,3]}]');
    }

    public function testEmpty()
    {
        $client = new Client();

        $this->compare($client, null);
    }

    public function testReset()
    {
        $client = new Client();
        $client->notification('Example/Math/subtract', array(3, 2));
        $client->encode();

        $this->compare($client, null);
    }

    private function compare(Client $client, $expectedJsonOutput)
    {
        $actualJsonOutput = $client->encode();

        $expectedOutput = @json_decode($expectedJsonOutput, true);
        $actualOutput = @json_decode($actualJsonOutput, true);

        $this->assertEquals($expectedOutput, $actualOutput);
    }
}
