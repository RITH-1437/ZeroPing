<?php

namespace Zeroping\Support;

use App\Core\Config\Config;
use App\Core\Config\ConfigRepository;
use App\Core\Container\Container;
use App\Providers\ServiceProvider as BaseServiceProvider;
use Zeroping\Support\Console\CommandRegistry;
use Zeroping\Support\Foundation\MigrationLoader;
use Zeroping\Support\Foundation\ViewFinder;

/**
 * Base service provider for every ZeroPing package.
 *
 * Adds the standard laravel-style loaders (config merge, routes, views,
 * migrations, commands) and a publishable-asset manifest, all implemented on
 * top of the framework's real extension surface (container, Router, Config).
 */
abstract class ServiceProvider extends BaseServiceProvider
{
    /** @var array<string, array<string, string>> */
    protected array $publishGroups = [];

    /**
     * Merge a package config file into the host config repository without
     * overwriting values the host app already set.
     */
    protected function mergeConfigFrom(string $path, string $key): void
    {
        if (!file_exists($path)) {
            return;
        }

        $defaults = require $path;
        $existing  = Config::get($key, []);

        $merged = is_array($defaults) && is_array($existing)
            ? array_replace_recursive($defaults, $existing)
            : $existing;

        if (class_exists(ConfigRepository::class)) {
            // Prefer mutating the live repository when available.
            $repo = $this->resolveRepository();
            if ($repo !== null) {
                $repo->set([$key => $merged]);
                return;
            }
        }

        // Fallback: framework has Config::get only; host should re-read merged
        // values via config($key) after this runs.
        Config::set($key, $merged);
    }

    /**
     * Require a routes file that registers routes via Router::get/post/... .
     */
    protected function loadRoutesFrom(string $path): void
    {
        if (file_exists($path)) {
            require $path;
        }
    }

    /**
     * Register a namespaced view path: view('pkg::file') -> resources/views/file.php
     */
    protected function loadViewsFrom(string $path, string $namespace): void
    {
        if (method_exists(\App\Core\View\View::class, 'addNamespace')) {
            \App\Core\View\View::addNamespace($namespace, $path);
            return;
        }

        ViewFinder::addNamespace($namespace, $path);
    }

    /**
     * Register a migration directory with the aggregated loader.
     */
    protected function loadMigrationsFrom(string $path): void
    {
        MigrationLoader::addPath($path);
    }

    /**
     * Register console commands (classes extending Zeroping\Support\Console\Command).
     *
     * @param class-string[] $classes
     */
    protected function commands(array $classes): void
    {
        foreach ($classes as $class) {
            CommandRegistry::register($class);
        }
    }

    /**
     * Declare publishable assets for `php zero vendor:publish`.
     *
     * @param array<string, string> $paths  [from => to]
     */
    protected function publishes(array $paths, string $group = 'default'): void
    {
        foreach ($paths as $from => $to) {
            $this->publishGroups[$group][$from] = $to;
        }
    }

    public function publishesFor(string $group): array
    {
        return $this->publishGroups[$group] ?? [];
    }

    private function resolveRepository(): ?ConfigRepository
    {
        // The live repository is not exposed by Config today; this hook exists
        // so support works once Config exposes setRepository()/getRepository().
        return null;
    }
}
