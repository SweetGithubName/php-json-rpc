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

namespace Datto\JsonRpc\Data;

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
