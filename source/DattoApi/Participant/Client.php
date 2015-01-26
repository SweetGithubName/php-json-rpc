<?php

namespace DattoApi\Participant;

class Client
{
    /** @var string */
    private $uri;

    public function __construct($uri)
    {
        $this->uri = $uri;
    }

    public function send($arguments)
    {
        $options = array(
            'http' => array(
                'method' => 'GET',
                'header' => 'Content-type: application/json',
                'content' => json_encode($arguments)
            )
        );

        $context = stream_context_create($options);

        $json = @file_get_contents($this->uri, false, $context);
        return @json_decode($json, true);
    }
}
