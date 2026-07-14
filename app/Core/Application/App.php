<?php

namespace App\Core\Application;

use App\Core\Container\Container;
use App\Core\Routing\Router;

class App
{
    /**
     * The current ZeroPing Framework version.
     */
    public const VERSION = '2.0.0-beta';

    protected string $basePath;

    protected static ?Container $container = null;

    public function __construct(string $basePath)
    {
        $this->basePath = $basePath;
        static::$container = new Container();
        $this->bootstrap();
    }

    public static function boot(?string $basePath = null): static
    {
        return new static($basePath ?? dirname(__DIR__, 3));
    }

    public static function setContainer(Container $container): void
    {
        static::$container = $container;
    }

    public static function container(): Container
    {
        return self::$container ??= new Container();
    }

    public function basePath(): string
    {
        return $this->basePath;
    }

    public function handle($request): void
    {
        $kernelClass = class_exists('App\\Http\\Kernel')
            ? 'App\\Http\\Kernel'
            : \App\Core\Http\Kernel::class;

        (new $kernelClass($this))->handle();
    }

    protected function bootstrap(): void
    {
        require_once dirname(__DIR__, 2) . '/Helpers/helpers.php';

        \App\Core\View\View::setBasePath($this->basePath);

        if (PHP_SAPI !== 'cli' && session_status() === PHP_SESSION_NONE && !headers_sent()) {
            session_start();
        }

        // Load .env if not already loaded
        if (empty($_ENV['APP_NAME'])) {
            \App\Core\Config\Env::load($this->basePath . '/.env');
        }

        // Define DB/app constants if not already defined
        if (!defined('DB_HOST')) {
            define('DB_CONNECTION', $_ENV['DB_CONNECTION'] ?? 'sqlite');
            define('DB_HOST', $_ENV['DB_HOST']    ?? '127.0.0.1');
            define('DB_NAME', $_ENV['DB_NAME']    ?? '');
            define('DB_USER', $_ENV['DB_USER']    ?? '');
            define('DB_PASS', $_ENV['DB_PASS']    ?? '');
            define('DB_CHARSET', $_ENV['DB_CHARSET'] ?? 'utf8mb4');
            define('DB_PORT', $_ENV['DB_PORT']    ?? 3306);
            define('APP_NAME', $_ENV['APP_NAME']   ?? 'ZeroPing');
            define('APP_ENV', $_ENV['APP_ENV']    ?? 'local');
            define('APP_DEBUG', ($_ENV['APP_DEBUG'] ?? 'false') === 'true');
        }

        // Load config files into repository
        $this->loadConfig();

        $this->registerProviders();
    }

    protected function loadConfig(): void
    {
        $repository = new \App\Core\Config\ConfigRepository();
        $configDir = $this->basePath . '/config';
        $cacheFile = $this->basePath . '/bootstrap/cache/config.php';

        // When a compiled config cache exists and is at least as fresh as the
        // config directory, load it in a single require instead of globbing
        // and requiring every config file on each boot.
        if (
            is_dir($configDir) && file_exists($cacheFile)
            && filemtime($cacheFile) >= $this->configDirMtime($configDir)
        ) {
            $items = require $cacheFile;
            if (is_array($items)) {
                $repository->set($items);
                \App\Core\Config\Config::setRepository($repository);
                return;
            }
        }

        if (!is_dir($configDir)) {
            \App\Core\Config\Config::setRepository($repository);
            return;
        }

        $skipFiles = ['routes.php'];
        $items = [];
        foreach (glob($configDir . '/*.php') as $file) {
            $key = pathinfo($file, PATHINFO_FILENAME);
            if (in_array($key . '.php', $skipFiles, true)) {
                continue;
            }
            $value = require $file;
            if (is_array($value)) {
                $items[$key] = $value;
            }
        }

        $repository->set($items);
        \App\Core\Config\Config::setRepository($repository);
    }

    /**
     * Highest mtime of the config directory's PHP files.
     */
    private function configDirMtime(string $configDir): int
    {
        $files = glob($configDir . '/*.php') ?: [];
        $mtime = 0;
        foreach ($files as $file) {
            $mtime = max($mtime, filemtime($file));
        }
        return $mtime;
    }

    protected function registerProviders(): void
    {
        $providers = \App\Core\Config\Config::get('app.providers', []);

        $manifest = $this->loadPackageManifest();

        if ($manifest !== null) {
            foreach ($manifest as $pkg) {
                if (!($pkg['enabled'] ?? true)) {
                    continue;
                }
                foreach (($pkg['providers'] ?? []) as $provider) {
                    $providers[] = $provider;
                }
            }
        }

        $eager    = [];
        $deferred = [];
        $booted  = [];

        foreach ($providers as $providerClass) {
            if (!class_exists($providerClass)) {
                continue;
            }

            $provider = new $providerClass(static::$container);
            $provider->register();

            if ($provider->isDeferred()) {
                $deferred[] = $provider;

                foreach ($provider->provides() as $service) {
                    static::$container->resolving(
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

        foreach ($eager as $provider) {
            if (method_exists($provider, 'boot')) {
                $provider->boot();
            }
        }

        $this->registerPackageHooks(array_merge($eager, $deferred));
    }

    /**
     * Let booted providers plug into the Event Bus and Scheduler using the
     * same declarative style as the framework's own providers.
     *
     * @param array<int, object> $instances
     */
    protected function registerPackageHooks(array $instances): void
    {
        if (App::container()->bound(\App\Core\Events\EventDispatcher::class)) {
            $dispatcher = App::container()->make(\App\Core\Events\EventDispatcher::class);

            foreach ($instances as $provider) {
                if (!method_exists($provider, 'listens')) {
                    continue;
                }

                foreach ($provider->listens() as $event => $listeners) {
                    foreach ((array) $listeners as $listener) {
                        $dispatcher->listen($event, $listener);
                    }
                }
            }
        }

        if (App::container()->bound(\App\Core\Scheduling\ScheduleManager::class)) {
            $schedule = App::container()->make(\App\Core\Scheduling\ScheduleManager::class)->schedule();

            foreach ($instances as $provider) {
                if (method_exists($provider, 'schedules')) {
                    $provider->schedules($schedule);
                }
            }
        }
    }

    /**
     * Load the discovered package manifest (from cache when fresh, else
     * rebuild it). Returns null when auto-discovery is disabled.
     *
     * @return array<string, array>|null
     */
    private function loadPackageManifest(): ?array
    {
        if (!$this->packageAutoDiscoverEnabled()) {
            return null;
        }

        $repo = new \App\Core\Packages\ProviderRepository(
            $this->basePath,
            $this->basePath . '/bootstrap/cache/packages.php'
        );

        $enabled = \App\Core\Config\Config::has('packages')
            ? \App\Core\Config\Config::get('packages', [])
            : [];

        return $repo->getCached()
            ?? $repo->buildManifest($enabled, $this->packageAutoDiscoverEnabled());
    }

    private function packageAutoDiscoverEnabled(): bool
    {
        $flag = $_ENV['PACKAGE_AUTO_DISCOVER'] ?? getenv('PACKAGE_AUTO_DISCOVER') ?? 'true';

        return $flag !== 'false' && $flag !== '0';
    }
}
