<?php

/**
 * Copyright (C) 2015 Datto, Inc.
 *
 * This file is part of PHP JSON-RPC.
 *
 * PHP JSON-RPC is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License, version 3,
 * as published by the Free Software Foundation.
 *
 * PHP JSON-RPC is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with PHP JSON-RPC. If not, see <http://www.gnu.org/licenses/>.
 *
 * @author Spencer Mortensen <smortensen@datto.com>
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL-3.0
 * @copyright 2015 Datto, Inc.
 */

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
