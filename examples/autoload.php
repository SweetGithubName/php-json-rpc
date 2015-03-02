<?php

function autoloadSource($className)
{
    $path = dirname(__DIR__) . '/src/' . strtr($className, '\\', '/') . '.php';
    @include $path;
}

function autoloadExamples($className)
{
    $path = __DIR__ . '/src/' . strtr($className, '\\', '/') . '.php';
    @include $path;
}
