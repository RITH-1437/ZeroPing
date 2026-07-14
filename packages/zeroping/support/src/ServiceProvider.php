<?php

namespace Zeroping\Support;

use App\Core\Container\Container;
use App\Core\Support\Config;
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
     * Aggregated publish groups across every booted provider.
     *
     * @var array<string, array<string, string>>
     */
    protected static array $published = [];

    /**
     * Custom notification channels declared by packages, keyed by channel name.
     *
     * @var array<string, class-string<\App\Core\Notifications\Channels\Channel>>
     */
    protected static array $channelClasses = [];

    /**
     * Merge a package config file into the host config store without
     * overwriting values the host app already set.
     *
     * Writes into App\Core\Support\Config so that the global config()
     * helper (and everything reading from it) sees the merged values.
     */
    protected function mergeConfigFrom(string $path, string $key): void
    {
        if (!file_exists($path)) {
            return;
        }

        $defaults = require $path;

        if (!is_array($defaults)) {
            return;
        }

        $existing = Config::get($key, []);
        $existing = is_array($existing) ? $existing : [];

        $merged = array_replace_recursive($defaults, $existing);

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
            self::$published[$group][$from]      = $to;
        }
    }

    public function publishesFor(string $group): array
    {
        return $this->publishGroups[$group] ?? [];
    }

    /**
     * All publish groups declared by every booted provider.
     *
     * @return array<string, array<string, string>>
     */
    public static function allPublished(): array
    {
        return self::$published;
    }

    /**
     * Declare custom notification channels for this package.
     *
     * @param array<string, class-string<\App\Core\Notifications\Channels\Channel>> $channels
     */
    protected function channels(array $channels): void
    {
        foreach ($channels as $name => $class) {
            self::$channelClasses[$name] = $class;
        }
    }

    /**
     * @return array<string, class-string<\App\Core\Notifications\Channels\Channel>>
     */
    public static function allChannels(): array
    {
        return self::$channelClasses;
    }
}
