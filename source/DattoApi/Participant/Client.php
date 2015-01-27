<?php

namespace DattoApi\Participant;

use DattoApi\JsonRpc;

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

        $options = array(
            'http' => array(
                'method' => 'GET',
                'header' => 'Content-Type: application/json',
                'content' => JsonRpc::encode($arguments)
            )
        );

        $context = stream_context_create($options);

        $json = @file_get_contents($this->uri, false, $context);
        return @json_decode($json, true);
    }
}
