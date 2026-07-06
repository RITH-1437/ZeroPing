<?php

namespace App\Core\Application;

use App\Core\Container\Container;
use App\Core\Routing\Router;

class App
{
    private static Container $container;

    /**
     * Bootstrap the framework
     */
    public static function boot(): void
    {
        self::$container = new Container();

        self::register();
    }

    /**
     * Run the HTTP application
     */
    public static function run(): void
    {
        self::boot();

        Router::dispatch();
    }

    /**
     * Get the container instance
     */
    public static function container(): Container
    {
        return self::$container;
    }

    /**
     * Register all service providers
     */
    private static function register(): void
    {
        $providers = [

            \App\Providers\AppServiceProvider::class,

            \App\Providers\DatabaseServiceProvider::class,

            \App\Providers\EventServiceProvider::class,

            \App\Providers\LoggingServiceProvider::class,

        ];

        foreach ($providers as $provider) {

            $instance = new $provider(self::$container);

            $instance->register();

            $instance->boot();
        }
    }
}