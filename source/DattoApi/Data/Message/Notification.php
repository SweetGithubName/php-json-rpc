<?php

namespace DattoApi\Data\Message;

use DattoApi\Data\Message;

class Notification extends Message
{
    public function getType()
    {
        return self::TYPE_NOTIFICATION;
    }
}
