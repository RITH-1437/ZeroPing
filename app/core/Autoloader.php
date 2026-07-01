<?php

class Autoloader
{
    public static function register(): void
    {
        spl_autoload_register(function ($class) {

            $directories = [
                __DIR__,
                __DIR__ . '/../controllers',
                __DIR__ . '/../models',
                __DIR__ . '/../middleware',
                __DIR__ . '/../helpers',
                __DIR__ . '/../services',
            ];

            foreach ($directories as $directory) {

                $file = $directory . '/' . $class . '.php';

                if (file_exists($file)) {
                    require_once $file;
                    return;
                }
            }
        });
    }
}