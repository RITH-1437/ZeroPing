<?php

namespace App\Providers;

use App\Core\Logging\FileLogger;
use App\Core\Logging\Logger;

class LoggingServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->container->singleton(
            Logger::class,
            fn () => new FileLogger()
        );
    }

    public function boot(): void
    {
        $logger = $this->container->make(Logger::class);

        $logger->info('ZeroPing Framework booted successfully.');
    }
}