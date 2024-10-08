<?php

function autoloaderFunction(string $class): void
{
    $file = __DIR__ . '/' . str_replace('\\', '/', $class) . '.php';

    if (file_exists($file)) {
        require_once $file;
    }
}

spl_autoload_register('autoloaderFunction');

