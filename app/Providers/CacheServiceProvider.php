<?php

declare(strict_types=1);

namespace App\Providers;

use App\Core\Cache\CacheManager;

class CacheServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->container->singleton(CacheManager::class, function () {
            return new CacheManager();
        });
    }
}
