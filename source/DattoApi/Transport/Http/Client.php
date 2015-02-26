<?php

namespace DattoApi\Transport\Http;

use DattoApi\Data\JsonRpc;

class Client
{
    /** @var string */
    private $uri;

    public function __construct($uri)
    {
        $this->uri = $uri;
    }

    public function send()
    {
        $arguments = func_get_args();

        $content = JsonRpc::encode($arguments);

        $header = 'Content-Type: application/json' . "\r\n" .
            'Content-Length: ' . strlen($content) . "\r\n" .
            'Accept: application/json' . "\r\n";

        $options = array(
            'http' => array(
                'method' => 'POST',
                'header' => $header,
                'content' => $content
            )
        );

        $context = stream_context_create($options);

        $json = @file_get_contents($this->uri, false, $context);
        return @json_decode($json, true);
    }
}
