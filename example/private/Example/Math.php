<?php

namespace Example;

class Math
{
    public static function subtract()
    {
        $arguments = func_get_args();

        // Named arguments
        if (count($arguments) === 1) {
            $values = array_shift($arguments);
            return self::sub(@$values['minuend'], @$values['subtrahend']);
        }

        // Positional arguments
        return self::sub(@$arguments[0], @$arguments[1]);
    }

    private static function sub($a, $b)
    {
        if (!is_int($a) || !is_int($b)) {
            return null;
        }

        return $a - $b;
    }
}
