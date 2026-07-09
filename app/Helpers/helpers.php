<?php

use App\Core\Application\App;
use App\Core\Cache\CacheManager;
use App\Core\Config\Config;
use App\Core\Debug\Dumper;
use App\Core\Debug\Performance;
use App\Core\Filesystem\FilesystemManager;
use App\Core\Queue\Dispatcher;
use App\Core\Queue\Job;
use App\Core\Routing\Router;
use App\Core\Support\Log;
use App\Core\View\View;

if (!function_exists('class_basename')) {

    function class_basename(string $class): string
    {
        return basename(
            str_replace('\\', '/', $class)
        );
    }
}

if (!function_exists('base_path')) {
    function base_path(string $path = ''): string
    {
        return BASE_PATH . ($path ? '/' . $path : '');
    }
}

if (!function_exists('config')) {

    function config(
        string $key,
        mixed $default = null
    ): mixed {

        return Config::get(
            $key,
            $default
        );
    }
}

if (!function_exists('cache')) {
    function cache()
    {
        $manager = App::container()->make(CacheManager::class);

        $arguments = func_get_args();

        if (empty($arguments)) {
            return $manager;
        }

        if (is_string($arguments[0])) {
            return $manager->get($arguments[0], $arguments[1] ?? null);
        }

        if (is_array($arguments[0])) {
            return $manager->put(key($arguments[0]), reset($arguments[0]), $arguments[1] ?? null);
        }

        throw new \InvalidArgumentException(
            'When calling the cache helper with arguments, you must specify a key.'
        );
    }
}

if (!function_exists('storage')) {
    function storage(string $disk = null)
    {
        return App::container()->make(FilesystemManager::class)->disk($disk);
    }
}

if (!function_exists('storage_path')) {
    function storage_path(string $path = ''): string
    {
        return BASE_PATH . '/storage' . ($path ? '/' . $path : '');
    }
}

if (!function_exists('public_path')) {
    function public_path(string $path = ''): string
    {
        return BASE_PATH . '/public' . ($path ? '/' . $path : '');
    }
}

if (!function_exists('view')) {
    function view(string $view, array $data = []): string
    {
        return View::render($view, $data);
    }
}

if (!function_exists('dispatch')) {
    function dispatch(Job $job): void
    {
        Dispatcher::dispatch($job);
    }
}

if (!function_exists('route')) {
    function route(string $name, array $parameters = [], bool $absolute = true, int $expiration = null): string
    {
        $router = App::container()->make(Router::class);
        $url = $router->route($name, $parameters);

        if ($absolute) {
            $url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://{$_SERVER['HTTP_HOST']}" . $url;
        }

        if ($expiration) {
            $url .= (strpos($url, '?') === false ? '?' : '&') . 'expires=' . (time() + $expiration);
        }

        return $url;
    }
}

if (!function_exists('app')) {
    function app($abstract = null)
    {
        if (is_null($abstract)) {
            return App::container();
        }

        return App::container()->make($abstract);
    }
}

if (!function_exists('dd')) {
    function dd(...$args)
    {
        foreach ($args as $arg) {
            (new Dumper())->dump($arg);
        }
        die(1);
    }
}

if (!function_exists('dump')) {
    function dump(...$args)
    {
        foreach ($args as $arg) {
            (new Dumper())->dump($arg);
        }
    }
}

if (!function_exists('ray')) {
    function ray(...$args)
    {
        // This is a placeholder for a real ray implementation.
    }
}

if (!function_exists('logger')) {
    function logger(string $message = null, array $context = [])
    {
        if (is_null($message)) {
            return app(Log::class);
        }

        return app(Log::class)->debug($message, $context);
    }
}

if (!function_exists('validator')) {
    function validator(array $data, array $rules): \App\Core\Validation\Validator
    {
        return \App\Core\Validation\Validator::make($data, $rules);
    }
}

if (!function_exists('benchmark')) {
    function benchmark(callable $callback, int $iterations = 1)
    {
        $total = 0;

        for ($i = 0; $i < $iterations; $i++) {
            $start = microtime(true);
            $callback();
            $total += microtime(true) - $start;
        }

        return $total / $iterations;
    }
}
