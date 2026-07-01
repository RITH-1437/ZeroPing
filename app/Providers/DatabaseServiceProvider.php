<?php

namespace App\Providers;

use App\Core\Database\Database;

class DatabaseServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->container->singleton(
            Database::class,
            fn () => Database::connect()
        );
    }
}
