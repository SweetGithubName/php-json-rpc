<?php

namespace JsonRpc\Transport\Local;

use JsonRpc\Data;
use JsonRpc\Transport;

class Client implements Transport\Client
{
    /** @var Data\Client */
    private $client;

    /** @var Data\Server */
    private $server;

    public function __construct()
    {
        $this->client = new Data\Client();
        $this->server = new Data\Server();
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
        $reply = $this->server->reply($message);
        return $this->client->decode($reply);
    }
}
