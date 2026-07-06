<?php

namespace App\Providers;

use App\Core\Config\Config;
use App\Core\Config\ConfigLoader;
use App\Core\Config\ConfigRepository;

class ConfigServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $loader = new ConfigLoader();

        $repository = new ConfigRepository();

        $repository->set(
            $loader->load()
        );

        $this->container->singleton(
            ConfigRepository::class,
            fn () => $repository
        );

        Config::setRepository($repository);
    }

    public function boot(): void
    {
        // Nothing to boot yet.
    }
}