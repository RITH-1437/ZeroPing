<?php

namespace App\Providers;

use App\Core\Container\Container;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->container->singleton(
            Container::class,
            fn () => $this->container
        );
    }
}