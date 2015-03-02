<?php

namespace JsonRpc\Transport\Http;

use JsonRpc\Data;
use JsonRpc\Transport;

class Client implements Transport\Client
{
    /** @var string */
    private $uri;

    /** @var Data\Client */
    private $client;

    public function __construct($uri)
    {
        $this->uri = $uri;
        $this->client = new Data\Client();
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
        $reply = $this->execute($message, 'POST', 'application/json');
        return $this->client->decode($reply);
    }

    private function execute($content, $method, $contentType)
    {
        $contentLength = strlen($content);

        $header = "Content-Type: {$contentType}\r\n" .
            "Content-Length: {$contentLength}\r\n" .
            "Accept: {$contentType}\r\n";

        $options = array(
            'http' => array(
                'method' => $method,
                'header' => $header,
                'content' => $content
            )
        );

        $context = stream_context_create($options);
        $reply = @file_get_contents($this->uri, false, $context);

        if ($reply === false) {
            return null;
        }

        return $reply;
    }
}
