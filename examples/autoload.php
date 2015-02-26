<?php

function autoloadSource($className)
{
    $path = dirname(__DIR__) . '/source/' . strtr($className, '\\', '/') . '.php';
    @include $path;
}

function autoloadExample($className)
{
    $path = __DIR__ . '/private/' . strtr($className, '\\', '/') . '.php';
    @include $path;
}
