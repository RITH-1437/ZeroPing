<?php

namespace App\Providers;

use App\Core\Queue\QueueManager;

class QueueServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->container->singleton(QueueManager::class, function () {
            return new QueueManager();
        });
    }
}
