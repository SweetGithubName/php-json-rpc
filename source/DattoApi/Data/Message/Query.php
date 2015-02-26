<?php

namespace DattoApi\Data\Message;

use DattoApi\Data\Message;

class Query extends Message
{
    /** @var mixed */
    private $id;

    /**
     * @param mixed $id
     * @param string $method
     * @param array $arguments
     */
    public function __construct($id, $method, $arguments = array())
    {
        $this->id = $id;

        parent::__construct($method, $arguments);

    }

    public function getType()
    {
        return self::TYPE_QUERY;
    }

    public function getId()
    {
        return $this->id;
    }
}
