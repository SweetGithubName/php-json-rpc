<?php

namespace Datto\JsonRpc;

interface Method
{
    /**
     * @param string $name
     * String value representing a method to invoke on the server.
     *
     * @return callable | null
     * Returns a callable object, or null on error.
     */
    public function getCallable($name);
}
