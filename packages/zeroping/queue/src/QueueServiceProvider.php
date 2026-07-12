<?php

namespace Zeroping\Queue;

use Zeroping\Queue\Console\WorkCommand;
use Zeroping\Queue\Contracts\QueueManager;
use Zeroping\Support\ServiceProvider;

class QueueServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/queue.php', 'queue');

        $this->container->singleton(
            QueueManager::class,
            fn () => new QueueManager(config('queue', []))
        );
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

        $this->commands([
            WorkCommand::class,
        ]);

        $this->publishes([
            __DIR__ . '/../config/queue.php' => base_path('config/queue.php'),
        ], 'queue-config');
    }
}
