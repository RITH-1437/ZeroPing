<?php

namespace App\Providers;

use App\Core\Queue\QueueManager;
use App\Core\Container\Container;

class QueueServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(QueueManager::class, function (Container $app) {
            return new QueueManager($app);
        });
    }
}
