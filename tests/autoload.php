<?php

function autoloadSource($className)
{
    $path = dirname(__DIR__) . '/source/' . strtr($className, '\\', '/') . '.php';

    if (is_file($path)) {
        require $path;
    }
}

function autoloadTests($className)
{
    $path = __DIR__ . '/' . strtr($className, '\\', '/') . '.php';

    if (is_file($path)) {
        require $path;
    }
}
