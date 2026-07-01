<?php

namespace App\Core;

class App
{
    private static Container $container;

    public static function run(): void
    {
        self::$container = new Container();

        self::register();

        Router::dispatch();
    }

    public static function container(): Container
    {
        return self::$container;
    }

    private static function register(): void
    {
        $providers = [

            \App\Providers\AppServiceProvider::class,

            \App\Providers\DatabaseServiceProvider::class,

        ];

        foreach ($providers as $provider) {

            $instance = new $provider(self::$container);

            $instance->register();

            $instance->boot();
        }
    }
}