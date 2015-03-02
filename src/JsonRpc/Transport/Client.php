<?php

namespace JsonRpc\Transport;

interface Client
{
    public function query($id, $method, $arguments);

    public function notification($method, $arguments);

    public function send();
}
