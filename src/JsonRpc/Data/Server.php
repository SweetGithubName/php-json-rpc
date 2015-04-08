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

namespace Datto\JsonRpc\Data;

use Datto\Query\Method;

/**
 * Class Server
 *
 * @link http://www.jsonrpc.org/specification JSON-RPC 2.0 Specifications
 *
 * @package Datto\JsonRpc\Data
 */
class Server
{
    const VERSION = '2.0';

    /**
     * Processes the user input, and prepares a response (if necessary).
     *
     * @param string $json
     * Single request object, or an array of request objects, as a JSON string.
     *
     * @return string|null
     * Returns a response object (or an error object) as a JSON string, when a query is made.
     * Returns an array of response/error objects as a JSON string, when multiple queries are made.
     * Returns null, when no response is necessary.
     */
    public static function reply($json)
    {
        $input = @json_decode($json, true);

        $output = self::processInput($input);

        if ($output === null) {
            return null;
        }

        return json_encode($output);
    }

    /**
     * Processes the user input, and prepares a response (if necessary).
     *
     * @param array $input
     * Single request object, or an array of request objects.
     *
     * @return array|null
     * Returns a response object (or an error object) when a query is made.
     * Returns an array of response/error objects when multiple queries are made.
     * Returns null when no response is necessary.
     */
    private static function processInput($input)
    {
        if (!is_array($input)) {
            return self::errorJson();
        }

        if (count($input) === 0) {
            return self::errorRequest();
        }

        if (isset($input['jsonrpc'])) {
            return self::processRequest($input);
        }

        return self::processBatchRequests($input);
    }

    /**
     * Processes a batch of user requests, and prepares the response.
     *
     * @param array $input
     * Array of request objects.
     *
     * @return array|null
     * Returns a response/error object when a query is made.
     * Returns an array of response/error objects when multiple queries are made.
     * Returns null when no response is necessary.
     */
    private static function processBatchRequests($input)
    {
        $replies = array();

        foreach ($input as $request) {
            $reply = self::processRequest($request);

            if ($reply !== null) {
                $replies[] = $reply;
            }
        }

        if (count($replies) === 0) {
            return null;
        }

        return $replies;
    }

    private static function processRequest($request)
    {
        if (!is_array($request)) {
            return self::errorRequest();
        }

        $version = @$request['jsonrpc'];

        if (@$version !== self::VERSION) {
            return self::errorRequest();
        }

        $method = @$request['method'];

        if (!is_string($method)) {
            return self::errorRequest();
        }

        // The 'params' key is optional, but must be non-null when provided
        if (array_key_exists('params', $request)) {
            $arguments = $request['params'];

            if (!is_array($arguments)) {
                return self::errorRequest();
            }
        } else {
            $arguments = array();
        }

        // The presence of the 'id' key indicates that a response is expected
        if (array_key_exists('id', $request)) {
            $id = $request['id'];

            if (!is_int($id) && !is_float($id) && !is_string($id) && ($id !== null)) {
                return self::errorRequest();
            }

            return self::processQuery($id, $method, $arguments);
        }

        self::processNotification($method, $arguments);
        return null;
    }

    private static function processQuery($id, $name, $arguments)
    {
        $method = new Method($name, $arguments);

        if (!$method->isValid()) {
            return self::errorMethod($id);
        }

        $result = $method->run();

        // A callable must return null when invoked with invalid arguments
        if ($result === null) {
            return self::errorArguments($id);
        }

        return self::response($id, $result);
    }

    private static function processNotification($name, $arguments)
    {
        $method = new Method($name, $arguments);
        $method->run();
    }

    private static function errorJson()
    {
        // An error occurred on the server while parsing the JSON text.
        return self::error(null, -32700, 'Parse error');
    }

    private static function errorRequest()
    {
        // The JSON sent is not a valid Request object.
        return self::error(null, -32600, 'Invalid Request');
    }

    private static function errorMethod($id)
    {
        // The requested method is not available.
        return self::error($id, -32601, 'Method not found');
    }

    private static function errorArguments($id)
    {
        // Invalid arguments.
        return self::error($id, -32602, 'Invalid params');
    }

    private static function error($id, $code, $message, $data = null)
    {
        $error = array(
            'code' => $code,
            'message' => $message
        );

        if ($data !== null) {
            $error['data'] = $data;
        }

        return array(
            'jsonrpc' => self::VERSION,
            'id' => $id,
            'error' => $error
        );
    }

    private static function response($id, $result)
    {
        return array(
            'jsonrpc' => self::VERSION,
            'id' => $id,
            'result' => $result
        );
    }
}
