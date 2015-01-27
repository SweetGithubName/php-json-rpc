<?php

namespace DattoApi;

class Method
{
    /** @var callable */
    private $callable;

    /** @var array */
    private $arguments;

    public function __construct($name, $arguments)
    {
        $name = self::validName($name);
        $arguments = self::validArguments($arguments);

        if (!isset($name, $arguments)) {
            return;
        }

        $this->callable = self::getCallable($name);
        $this->arguments = $arguments;
    }

    public function isValid()
    {
        return $this->callable !== null;
    }

    public function run()
    {
        if ($this->callable === null) {
            return null;
        }

        if (self::isPositionalArguments($this->arguments)) {
            return call_user_func_array($this->callable, $this->arguments);
        }

        return call_user_func($this->callable, $this->arguments);
    }

    private static function getCallable($name)
    {
        $parts = explode('/', $name);

        $method = array_pop($parts);
        $class = '\\' . implode('\\', $parts);

        $callable = array($class, $method);

        if (!@is_callable($callable)) {
            return null;
        }

        return $callable;
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

    private static function validName($input)
    {
        if (!is_string($input)) {
            return null;
        }

        $validPattern = '~^[a-zA-Z0-9]+(/[a-zA-Z0-9]+)+$~';

        if (preg_match($validPattern, $input) !== 1) {
            return null;
        }

        return $input;
    }

    private static function validArguments($input)
    {
        if (!is_array($input)) {
            return null;
        }

        return $input;
    }
}
