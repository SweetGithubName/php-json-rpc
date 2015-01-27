<?php

namespace DattoApi\Message;

use DattoApi\Message;

class Notification extends Message
{
    public function getType()
    {
        return self::TYPE_NOTIFICATION;
    }
}
