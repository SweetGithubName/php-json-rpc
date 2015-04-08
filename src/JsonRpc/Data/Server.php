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

    /**
     * Processes an individual request, and prepares the response.
     *
     * @param array $request
     * Single request object to be processed.
     *
     * @return array|null
     * Returns a response object or an error object.
     * Returns null when no response is necessary.
     */
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

    /**
     * Processes a query request and prepares the response.
     *
     * @param mixed $id
     * Client-supplied value that allows the client to associate the server response
     * with the original query.
     *
     * @param string $name
     * String value representing a method to invoke on the server:
     * JSON-RPC intentionally leaves the internal format of this string unspecified.
     *
     * @param array $arguments
     * Array of arguments that will be passed to the server method.
     * This arguments array can be either a zero-indexed or an associative array:
     *
     * If the array is a zero-indexed array, then the elements of the array
     * are passed to the method as sequential, positional arguments.
     *
     * If the array is an associative array, then the entire array is passed
     * as a single argument to the server method.
     *
     * @return array
     * Returns a response object or an error object.
     */
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

    /**
     * Processes a notification. No response is necessary.
     *
     * @param string $name
     * @param array $arguments
     */
    private static function processNotification($name, $arguments)
    {
        $method = new Method($name, $arguments);
        $method->run();
    }

    /**
     * Returns an error object explaining that an error occurred while parsing
     * the JSON text input.
     *
     * @return array
     * Returns an error object.
     */
    private static function errorJson()
    {
        return self::error(null, -32700, 'Parse error');
    }

    /**
     * Returns an error object explaining that the JSON input is not a valid
     * request object.
     *
     * @return array
     * Returns an error object.
     */
    private static function errorRequest()
    {
        return self::error(null, -32600, 'Invalid Request');
    }

    /**
     * Returns an error object explaining that the requested method is unknown.
     *
     * @param mixed $id
     * Client-supplied value that allows the client to associate the server response
     * with the original query.
     *
     * @return array
     * Returns an error object.
     */
    private static function errorMethod($id)
    {
        return self::error($id, -32601, 'Method not found');
    }

    /**
     * Returns an error object explaining that the method arguments were invalid.
     *
     * @param mixed $id
     * Client-supplied value that allows the client to associate the server response
     * with the original query.
     *
     * @return array
     * Returns an error object.
     */
    private static function errorArguments($id)
    {
        return self::error($id, -32602, 'Invalid params');
    }

    /**
     * Returns a properly-formatted error object.
     *
     * @param mixed $id
     * Client-supplied value that allows the client to associate the server response
     * with the original query.
     *
     * @param int $code
     * Integer value representing the general type of error encountered.
     *
     * @param string $message
     * Concise description of the error (ideally a single sentence).
     *
     * @return array
     * Returns an error object.
     */
    private static function error($id, $code, $message)
    {
        $error = array(
            'code' => $code,
            'message' => $message
        );

        return array(
            'jsonrpc' => self::VERSION,
            'id' => $id,
            'error' => $error
        );
    }

    /**
     * Returns a properly-formatted response object.
     *
     * @param mixed $id
     * Client-supplied value that allows the client to associate the server response
     * with the original query.
     *
     * @param mixed $result
     * Return value from the server method, which will now be delivered to the user.
     *
     * @return array
     * Returns a response object.
     */
    private static function response($id, $result)
    {
        return array(
            'jsonrpc' => self::VERSION,
            'id' => $id,
            'result' => $result
        );
    }
}
