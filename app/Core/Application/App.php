<?php

declare(strict_types=1);

namespace App\Core\Application;

use App\Core\Config\Config;
use App\Core\Config\ConfigRepository;
use App\Core\Config\Env;
use App\Core\Container\Container;
use App\Core\Events\EventDispatcher;
use App\Core\Packages\ProviderRepository;
use App\Core\Routing\Router;
use App\Core\Scheduling\ScheduleManager;
use App\Core\View\View;
use App\Providers\ServiceProvider;

/**
 * Application bootstrap.
 *
 * Responsible for booting the framework: loading configuration, registering
 * service providers (eager and deferred), discovering packages, and wiring
 * event/schedule hooks declared by providers.
 */
class App
{
    /**
     * The current ZeroPing Framework version.
     */
    public const VERSION = '2.0.1';

    /** @var string Absolute path to the project root. */
    protected string $basePath;

    /** @var Container|null The global service container instance. */
    protected static ?Container $container = null;

    public function __construct(string $basePath)
    {
        $this->basePath = $basePath;
        static::$container = new Container();
        $this->bootstrap();
    }

    /**
     * Create and boot a new application instance.
     *
     * @param string|null $basePath Project root; defaults to three levels above this file.
     */
    public static function boot(?string $basePath = null): static
    {
        return new static($basePath ?? dirname(__DIR__, 3));
    }

    /**
     * Replace the global container (useful for testing).
     */
    public static function setContainer(Container $container): void
    {
        static::$container = $container;
    }

    /**
     * Retrieve the global container, creating one if none exists.
     */
    public static function container(): Container
    {
        return static::$container ??= new Container();
    }

    /**
     * Get the application base path.
     */
    public function basePath(): string
    {
        return $this->basePath;
    }

    /**
     * Dispatch the incoming HTTP request through the kernel.
     *
     * @param mixed $request Reserved for future request-object support.
     */
    public function handle(mixed $request = null): void
    {
        $kernelClass = class_exists('App\\Http\\Kernel')
            ? 'App\\Http\\Kernel'
            : \App\Core\Http\Kernel::class;

        (new $kernelClass($this))->handle();
    }

    /**
     * Run the full application bootstrap sequence.
     */
    protected function bootstrap(): void
    {
        require_once dirname(__DIR__, 2) . '/Helpers/helpers.php';

        View::setBasePath($this->basePath);

        if (PHP_SAPI !== 'cli' && session_status() === PHP_SESSION_NONE && !headers_sent()) {
            session_start();
        }

        if (empty($_ENV['APP_NAME'])) {
            Env::load($this->basePath . '/.env');
        }

        $this->loadConfig();
        $this->registerProviders();
    }

    /**
     * Load configuration files into the global Config repository.
     *
     * Uses a compiled cache file when available and fresh; otherwise reads
     * individual PHP files from the config directory.
     *
     * Optimization: When the cache file exists, we only stat the cache file
     * first. The expensive configDirMtime() (which globs and stats every
     * config file) is only called if the cache file actually exists and we
     * need to validate freshness.
     */
    protected function loadConfig(): void
    {
        $repository = new ConfigRepository();
        $configDir  = $this->basePath . '/config';
        $cacheFile  = $this->basePath . '/bootstrap/cache/config.php';

        // Fast path: try loading from cache first.
        // Only proceed if the config directory exists (common case).
        if (is_file($cacheFile)) {
            // In production, APP_ENV=production skips mtime validation entirely.
            $skipMtimeCheck = (($_ENV['APP_ENV'] ?? getenv('APP_ENV') ?: '') === 'production');

            if ($skipMtimeCheck || !is_dir($configDir) || filemtime($cacheFile) >= $this->configDirMtime($configDir)) {
                $items = require $cacheFile;

                if (is_array($items)) {
                    $repository->set($items);
                    Config::setRepository($repository);
                    return;
                }
            }
        }

        if (!is_dir($configDir)) {
            Config::setRepository($repository);
            return;
        }

        $items = [];

        $files = glob($configDir . '/*.php');
        if ($files === false) {
            $files = [];
        }

        foreach ($files as $file) {
            $basename = basename($file);

            if ($basename === 'routes.php') {
                continue;
            }

            $key = substr($basename, 0, -4); // Strip .php faster than pathinfo()

            $value = require $file;

            if (is_array($value)) {
                $items[$key] = $value;
            }
        }

        $repository->set($items);
        Config::setRepository($repository);
    }

    /**
     * Return the most recent modification time among config PHP files.
     *
     * Optimized: uses a single loop with direct filemtime calls,
     * avoiding intermediate array operations.
     */
    private function configDirMtime(string $configDir): int
    {
        $files = glob($configDir . '/*.php');
        if (!$files) {
            return 0;
        }

        $mtime = 0;
        foreach ($files as $file) {
            $t = filemtime($file);
            if ($t > $mtime) {
                $mtime = $t;
            }
        }

        return $mtime;
    }

    // -------------------------------------------------------------------------
    // Service Provider Registration
    // -------------------------------------------------------------------------

    /**
     * Collect, register, and boot all service providers (app + packages).
     */
    protected function registerProviders(): void
    {
        $providers = $this->collectProviderClasses();

        [$eager, $deferred] = $this->instantiateProviders($providers);

        $this->bootEagerProviders($eager);
        $this->registerPackageHooks($eager, $deferred);
    }

    /**
     * Merge application providers with auto-discovered package providers.
     *
     * @return list<class-string<ServiceProvider>>
     */
    private function collectProviderClasses(): array
    {
        $providers = Config::get('app.providers', []);
        $manifest  = $this->loadPackageManifest();

        if ($manifest !== null) {
            foreach ($manifest as $pkg) {
                if (!($pkg['enabled'] ?? true)) {
                    continue;
                }
                if (isset($pkg['providers'])) {
                    foreach ($pkg['providers'] as $provider) {
                        $providers[] = $provider;
                    }
                }
            }
        }

        return $providers;
    }

    /**
     * Register all providers, separating eager from deferred.
     *
     * Deferred providers have their boot() called lazily via container
     * resolving callbacks on the services they declare.
     *
     * @param list<class-string<ServiceProvider>> $providerClasses
     * @return array{0: list<ServiceProvider>, 1: list<ServiceProvider>}
     */
    private function instantiateProviders(array $providerClasses): array
    {
        $eager    = [];
        $deferred = [];
        $booted   = [];

        foreach ($providerClasses as $providerClass) {
            if (!class_exists($providerClass)) {
                continue;
            }

            $provider = new $providerClass(static::$container);
            $provider->register();

            if ($provider->isDeferred()) {
                $deferred[] = $provider;

                foreach ($provider->provides() as $service) {
                    static::container()->resolving(
                        $service,
                        function (object $object, Container $container) use ($provider, &$booted): void {
                            if (!in_array($provider, $booted, true)) {
                                $provider->boot();
                                $booted[] = $provider;
                            }
                        }
                    );
                }
            } else {
                $eager[] = $provider;
            }
        }

        return [$eager, $deferred];
    }

    /**
     * Boot all eager (non-deferred) providers.
     *
     * @param list<ServiceProvider> $providers
     */
    private function bootEagerProviders(array $providers): void
    {
        foreach ($providers as $provider) {
            if (method_exists($provider, 'boot')) {
                $provider->boot();
            }
        }
    }

    /**
     * Wire provider-declared event listeners and scheduled tasks.
     *
     * Providers may implement `listens()` to return an event-to-listener map,
     * and `schedules(Schedule)` to register recurring tasks.
     *
     * @param list<ServiceProvider> $eager    Eager provider instances.
     * @param list<ServiceProvider> $deferred Deferred provider instances.
     */
    protected function registerPackageHooks(array $eager, array $deferred): void
    {
        $hasEventDispatcher = static::container()->bound(EventDispatcher::class);
        $hasScheduleManager = static::container()->bound(ScheduleManager::class);

        // Early return if neither hook system is available.
        if (!$hasEventDispatcher && !$hasScheduleManager) {
            return;
        }

        $dispatcher = $hasEventDispatcher ? static::container()->make(EventDispatcher::class) : null;
        $schedule = $hasScheduleManager ? static::container()->make(ScheduleManager::class)->schedule() : null;

        // Process all provider instances (eager + deferred).
        foreach ($eager as $provider) {
            if ($dispatcher !== null && method_exists($provider, 'listens')) {
                foreach ($provider->listens() as $event => $listeners) {
                    foreach ((array) $listeners as $listener) {
                        $dispatcher->listen($event, $listener);
                    }
                }
            }

            if ($schedule !== null && method_exists($provider, 'schedules')) {
                $provider->schedules($schedule);
            }
        }

        foreach ($deferred as $provider) {
            if ($dispatcher !== null && method_exists($provider, 'listens')) {
                foreach ($provider->listens() as $event => $listeners) {
                    foreach ((array) $listeners as $listener) {
                        $dispatcher->listen($event, $listener);
                    }
                }
            }

            if ($schedule !== null && method_exists($provider, 'schedules')) {
                $provider->schedules($schedule);
            }
        }
    }

    // -------------------------------------------------------------------------
    // Package Auto-Discovery
    // -------------------------------------------------------------------------

    /**
     * Load the discovered package manifest (from cache when fresh, else rebuild).
     *
     * @return array<string, array{enabled?: bool, providers?: list<class-string>}>|null
     */
    private function loadPackageManifest(): ?array
    {
        if (!$this->packageAutoDiscoverEnabled()) {
            return null;
        }

        $repo = new ProviderRepository(
            $this->basePath,
            $this->basePath . '/bootstrap/cache/packages.php'
        );

        $enabled = Config::has('packages')
            ? Config::get('packages', [])
            : [];

        return $repo->getCached()
            ?? $repo->buildManifest($enabled, $this->packageAutoDiscoverEnabled());
    }

    /**
     * Check whether package auto-discovery is enabled via environment.
     */
    private function packageAutoDiscoverEnabled(): bool
    {
        $envVal = $_ENV['PACKAGE_AUTO_DISCOVER'] ?? null;
        if ($envVal === null) {
            $envVal = getenv('PACKAGE_AUTO_DISCOVER');
        }
        $flag = ($envVal !== false && $envVal !== '') ? (string) $envVal : 'true';

        return $flag !== 'false' && $flag !== '0';
    }
}
