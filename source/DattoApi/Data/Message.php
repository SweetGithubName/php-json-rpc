<?php

namespace DattoApi\Data;

abstract class Message
{
    const TYPE_NOTIFICATION = 1;
    const TYPE_QUERY = 2;

    /** @var string */
    private $method;

    /** @var array */
    private $arguments;

    /**
     * @param string $method
     * @param array $arguments
     */
    public function __construct($method, $arguments = array())
    {
        $this->method = $method;
        $this->arguments = $arguments;
    }

    abstract public function getType();

    public function getMethod()
    {
        return $this->method;
    }

    public function getArguments()
    {
        return $this->arguments;
    }
}
