<?php

namespace App\Providers;

use App\Core\Scheduling\ScheduleManager;
use App\Core\Container\Container;

class ScheduleServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(ScheduleManager::class, function (Container $app) {
            return new ScheduleManager();
        });
    }
}
