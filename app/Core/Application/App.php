<?php

namespace App\Core\Application;

use App\Core\Container\Container;
use App\Core\Routing\Router;

class App
{
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

    public static function container(): Container
    {
        if (static::$container === null) {
            static::$container = new Container();
        }

        return static::$container;
    }

    public function handle($request): void
    {
        Router::dispatch($this->basePath);
    }

    protected function bootstrap(): void
    {
        require_once dirname(__DIR__, 2) . '/Helpers/helpers.php';

        \App\Core\View\View::setBasePath($this->basePath);

        if (session_status() === PHP_SESSION_NONE && !headers_sent()) {
            session_start();
        }

        // Load .env if not already loaded
        if (empty($_ENV['APP_NAME'])) {
            \App\Core\Config\Env::load($this->basePath . '/.env');
        }

        // Define DB/app constants if not already defined
        if (!defined('DB_HOST')) {
            define('DB_HOST',    $_ENV['DB_HOST']    ?? '127.0.0.1');
            define('DB_NAME',    $_ENV['DB_NAME']    ?? '');
            define('DB_USER',    $_ENV['DB_USER']    ?? '');
            define('DB_PASS',    $_ENV['DB_PASS']    ?? '');
            define('DB_CHARSET', $_ENV['DB_CHARSET'] ?? 'utf8mb4');
            define('DB_PORT',    $_ENV['DB_PORT']    ?? 3306);
            define('APP_NAME',   $_ENV['APP_NAME']   ?? 'ZeroPing');
            define('APP_ENV',    $_ENV['APP_ENV']    ?? 'local');
            define('APP_DEBUG',  ($_ENV['APP_DEBUG'] ?? 'false') === 'true');
        }

        // Load config files into repository
        $this->loadConfig();

        $this->registerProviders();
    }

    protected function loadConfig(): void
    {
        $repository = new \App\Core\Config\ConfigRepository();
        $configDir = $this->basePath . '/config';

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

    protected function registerProviders(): void
    {
        $providers = \App\Core\Config\Config::get('app.providers', []);

        $instances = [];
        foreach ($providers as $providerClass) {
            if (!class_exists($providerClass)) {
                continue;
            }
            $provider = new $providerClass(static::$container);
            $provider->register();
            $instances[] = $provider;
        }

        foreach ($instances as $provider) {
            if (method_exists($provider, 'boot')) {
                $provider->boot();
            }
        }
    }
}
