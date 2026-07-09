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
        Router::dispatch();
    }

    protected function bootstrap(): void
    {
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

        $this->registerProviders();
    }

    protected function registerProviders(): void
    {
        $configPath = $this->basePath . '/config/app.php';
        if (!file_exists($configPath)) {
            return;
        }

        $config = require $configPath;
        $providers = $config['providers'] ?? [];

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
