<?php

namespace JsonRpc\Data;

class Client
{
    const VERSION = '2.0';

    /** @var array */
    private $messages;

    public function query($id, $method, $arguments)
    {
        $this->messages[] = array(
            'jsonrpc' => self::VERSION,
            'id' => $id,
            'method' => $method,
            'params' => $arguments
        );
    }

    public function notification($method, $arguments)
    {
        $this->messages[] = array(
            'jsonrpc' => self::VERSION,
            'method' => $method,
            'params' => $arguments
        );
    }

    public function encode()
    {
        $count = count($this->messages);

        if ($count === 0) {
            return null;
        }

        if ($count === 1) {
            $output = array_shift($this->messages);
        } else {
            $output = $this->messages;
        }

        return json_encode($output);
    }

    public function decode($reply)
    {
        return @json_decode($reply);
    }
}
