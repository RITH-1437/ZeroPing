<?php
use App\Core\Config\Config;
if (!function_exists('class_basename')) {

    function class_basename(string $class): string
    {
        return basename(
            str_replace('\\', '/', $class)
        );
    }
}

if (!function_exists('config')) {

    function config(
        string $key,
        mixed $default = null
    ): mixed {

        return Config::get(
            $key,
            $default
        );
    }
}