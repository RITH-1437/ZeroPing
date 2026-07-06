<?php

namespace App\Providers;

use App\Core\Events\EventDispatcher;
use App\Events\UserRegistered;
use App\Listeners\LogUserRegistered;

class EventServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->container->singleton(
            EventDispatcher::class,
            fn () => new EventDispatcher()
        );
    }

    public function boot(): void
    {
        $dispatcher = $this->container->make(EventDispatcher::class);

        $dispatcher->listen(
            UserRegistered::class,
            LogUserRegistered::class
        );
    }
}