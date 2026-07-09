<?php

namespace App\Providers;

use App\Core\Cache\CacheManager;
use App\Core\Container\Container;

class CacheServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->container->singleton(CacheManager::class, function (Container $app) {
            return new CacheManager($app);
        });
    }
}
