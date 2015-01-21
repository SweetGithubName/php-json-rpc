<?php

namespace DattoApi\JsonRpc;

class Method
{
    /** @var callable */
    private $callable;

    public function __construct($name, $arguments)
    {
        $callable = self::validCallable($name);
        $arguments = self::validArguments($arguments);

        if (!isset($callable, $arguments)) {
            return;
        }

        $this->callable = $callable;
        $this->arguments = $arguments;
    }

    public function isValid()
    {
        return $this->callable !== null;
    }

    public function run()
    {
        if (!is_callable($this->callable)) {
            return null;
        }

        if (self::isPositionalArguments($this->arguments)) {
            return call_user_func_array($this->callable, $this->arguments);
        }

        return call_user_func($this->callable, $this->arguments);
    }

    private static function validCallable($input)
    {
        if (!is_string($input)) {
            return null;
        }

        $colon = strrpos($input, ':');

        if (!is_int($colon)) {
            return null;
        }

        $class = '\\' . strtr(substr($input, 0, $colon), '/', '\\');
        $method = substr($input, $colon + 1);

        $callable = array($class, $method);

        if (!is_callable($callable)) {
            return null;
        }

        return $callable;
    }

    private static function validArguments($input)
    {
        if (!is_array($input)) {
            return null;
        }

        return $input;
    }

    private static function isPositionalArguments($arguments)
    {
        $i = 0;

        foreach ($arguments as $key => $value) {
            if ($key !== $i++) {
                return false;
            }
        }

        return true;
    }
}
