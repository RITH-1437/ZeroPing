<?php

/**
 * ZeroPing Framework – Global Helper Functions
 *
 * This file is loaded early in the bootstrap process (before the container is
 * fully wired). Every function guards itself with function_exists() so that
 * userland code can override any helper by defining it first.
 *
 * Sections:
 *  1. Path Helpers
 *  2. Application & Container
 *  3. Configuration & Environment
 *  4. HTTP & Routing
 *  5. Views & Assets
 *  6. Localization
 *  7. Cache & Storage
 *  8. Queue & Jobs
 *  9. Validation
 * 10. Debugging & Profiling
 * 11. Logging
 * 12. String & Encoding Utilities
 */

use App\Core\Application\App;
use App\Core\Cache\CacheManager;
use App\Core\Debug\Dumper;
use App\Core\Filesystem\FilesystemManager;
use App\Core\Http\Response;
use App\Core\Http\ResponseFactory;
use App\Core\Localization\Translator;
use App\Core\Queue\Dispatcher;
use App\Core\Queue\Job;
use App\Core\Routing\Router;
use App\Core\Support\Log;
use App\Core\View\View;

// ============================================================================
// 1. Path Helpers
// ============================================================================

if (!function_exists('resolve_base_path')) {
    /**
     * Resolve the application base path.
     *
     * Priority:
     *  1. The BASE_PATH constant (defined in public/index.php or config/config.php)
     *  2. The current working directory as a fallback
     *
     * @internal Used by path helper functions. Prefer base_path() in application code.
     */
    function resolve_base_path(): string
    {
        if (defined('BASE_PATH')) {
            return BASE_PATH;
        }

        return getcwd() ?: dirname(__DIR__, 2);
    }
}

if (!function_exists('base_path')) {
    /**
     * Get the application base path, optionally appending a relative path.
     *
     * @param  string  $path  Relative path to append (without leading slash).
     * @return string  Absolute path.
     */
    function base_path(string $path = ''): string
    {
        $base = resolve_base_path();

        return $base . ($path !== '' ? '/' . ltrim($path, '/') : '');
    }
}

if (!function_exists('storage_path')) {
    /**
     * Get the storage directory path, optionally appending a relative path.
     *
     * @param  string  $path  Relative path to append.
     * @return string  Absolute path to a location inside storage/.
     */
    function storage_path(string $path = ''): string
    {
        return base_path('storage' . ($path !== '' ? '/' . ltrim($path, '/') : ''));
    }
}

if (!function_exists('database_path')) {
    /**
     * Get the database directory path, optionally appending a relative path.
     *
     * @param  string  $path  Relative path to append.
     * @return string  Absolute path to a location inside database/.
     */
    function database_path(string $path = ''): string
    {
        return base_path('database' . ($path !== '' ? '/' . ltrim($path, '/') : ''));
    }
}

if (!function_exists('public_path')) {
    /**
     * Get the public directory path, optionally appending a relative path.
     *
     * @param  string  $path  Relative path to append.
     * @return string  Absolute path to a location inside public/.
     */
    function public_path(string $path = ''): string
    {
        return base_path('public' . ($path !== '' ? '/' . ltrim($path, '/') : ''));
    }
}

// ============================================================================
// 2. Application & Container
// ============================================================================

if (!function_exists('app')) {
    /**
     * Get the application container instance, or resolve a binding from it.
     *
     * @param  class-string|string|null  $abstract  Service identifier to resolve.
     * @return mixed  The container instance when called without arguments, or the resolved service.
     */
    function app(string|null $abstract = null): mixed
    {
        if ($abstract === null) {
            return App::container();
        }

        return App::container()->make($abstract);
    }
}

if (!function_exists('class_basename')) {
    /**
     * Get the "basename" of a fully-qualified class name.
     *
     * @param  string|object  $class  FQCN or object instance.
     * @return string  The class name without its namespace.
     */
    function class_basename(string|object $class): string
    {
        $class = is_object($class) ? get_class($class) : $class;

        return basename(str_replace('\\', '/', $class));
    }
}

// ============================================================================
// 3. Configuration & Environment
// ============================================================================

if (!function_exists('config')) {
    /**
     * Retrieve a configuration value using dot notation.
     *
     * @param  string  $key      Dot-notation config key (e.g. "app.name").
     * @param  mixed   $default  Default value when the key is not found.
     * @return mixed
     */
    function config(string $key, mixed $default = null): mixed
    {
        return \App\Core\Support\Config::get($key, $default);
    }
}

if (!function_exists('env')) {
    /**
     * Get an environment variable value with an optional default.
     *
     * Checks $_ENV, getenv(), then $_SERVER in order.
     *
     * @param  string  $key      Environment variable name.
     * @param  mixed   $default  Fallback value if not set.
     * @return mixed
     */
    function env(string $key, mixed $default = null): mixed
    {
        $value = $_ENV[$key] ?? getenv($key) ?: null;

        if ($value === null) {
            $value = $_SERVER[$key] ?? null;
        }

        return $value ?? $default;
    }
}

// ============================================================================
// 4. HTTP & Routing
// ============================================================================

if (!function_exists('response')) {
    /**
     * Create a response or get the response factory.
     *
     * Called with no arguments it returns the ResponseFactory instance.
     * Called with arguments it builds and returns a Response directly.
     *
     * @param  mixed   $content  Response body content.
     * @param  int     $status   HTTP status code.
     * @param  array   $headers  Additional response headers.
     * @return ResponseFactory|Response
     */
    function response(mixed $content = null, int $status = 200, array $headers = []): ResponseFactory|Response
    {
        $factory = new ResponseFactory();

        if (func_num_args() === 0) {
            return $factory;
        }

        return $factory->make($content, $status, $headers);
    }
}

if (!function_exists('redirect')) {
    /**
     * Create a redirect response.
     *
     * @param  string  $to      The URL or path to redirect to.
     * @param  int     $status  HTTP status code (default 302).
     * @return Response
     */
    function redirect(string $to, int $status = 302): Response
    {
        return (new ResponseFactory())->redirect($to, $status);
    }
}

if (!function_exists('route')) {
    /**
     * Generate a URL for a named route.
     *
     * @param  string    $name        The route name.
     * @param  array     $parameters  Route parameters to substitute.
     * @param  bool      $absolute    Whether to generate an absolute URL.
     * @param  int|null  $expiration  Optional expiration time in seconds (appended as query param).
     * @return string    The generated URL.
     */
    function route(string $name, array $parameters = [], bool $absolute = true, ?int $expiration = null): string
    {
        $url = Router::route($name, $parameters);

        if ($absolute) {
            $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
            $host   = $_SERVER['HTTP_HOST'] ?? 'localhost';
            $url    = $scheme . '://' . $host . $url;
        }

        if ($expiration !== null) {
            $separator = str_contains($url, '?') ? '&' : '?';
            $url .= $separator . 'expires=' . (time() + $expiration);
        }

        return $url;
    }
}

if (!function_exists('url')) {
    /**
     * Generate a fully-qualified URL.
     *
     * Called without a path, returns the current request URL.
     * Called with a path, returns the absolute URL for that path.
     *
     * @param  string|null  $path  Optional relative path.
     * @return string
     */
    function url(?string $path = null): string
    {
        $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $host   = $_SERVER['HTTP_HOST'] ?? 'localhost';

        if ($path === null) {
            return $scheme . '://' . $host . ($_SERVER['REQUEST_URI'] ?? '/');
        }

        return $scheme . '://' . $host . '/' . ltrim($path, '/');
    }
}

if (!function_exists('asset')) {
    /**
     * Generate a URL to a public asset.
     *
     * @param  string  $path  Relative path within the public directory.
     * @return string  Fully-qualified URL to the asset.
     */
    function asset(string $path): string
    {
        $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $host   = $_SERVER['HTTP_HOST'] ?? 'localhost';

        return $scheme . '://' . $host . '/' . ltrim($path, '/');
    }
}

// ============================================================================
// 5. Views & Assets
// ============================================================================

if (!function_exists('view')) {
    /**
     * Render a view template with the given data.
     *
     * @param  string  $view  View name (dot or slash notation).
     * @param  array   $data  Data to pass to the view.
     * @return string  The rendered HTML.
     */
    function view(string $view, array $data = []): string
    {
        return View::render($view, $data);
    }
}

// ============================================================================
// 6. Localization
// ============================================================================

if (!function_exists('trans')) {
    /**
     * Translate a given key.
     *
     * @param  string       $key      Translation key.
     * @param  array        $replace  Replacement values for placeholders.
     * @param  string|null  $locale   Locale override.
     * @return string
     */
    function trans(string $key, array $replace = [], ?string $locale = null): string
    {
        return app(Translator::class)->get($key, $replace, $locale);
    }
}

if (!function_exists('__')) {
    /**
     * Alias of trans() – translate a given key.
     *
     * @param  string       $key      Translation key.
     * @param  array        $replace  Replacement values for placeholders.
     * @param  string|null  $locale   Locale override.
     * @return string
     */
    function __(string $key, array $replace = [], ?string $locale = null): string
    {
        return trans($key, $replace, $locale);
    }
}

// ============================================================================
// 7. Cache & Storage
// ============================================================================

if (!function_exists('cache')) {
    /**
     * Interact with the cache.
     *
     * Called with no arguments: returns the CacheManager instance.
     * Called with a string key: retrieves the cached value.
     * Called with an array: stores the key/value pair.
     *
     * @return mixed  CacheManager instance or cached value.
     *
     * @throws \InvalidArgumentException When arguments are invalid.
     */
    function cache(): mixed
    {
        $manager   = App::container()->make(CacheManager::class);
        $arguments = func_get_args();

        if (empty($arguments)) {
            return $manager;
        }

        if (is_string($arguments[0])) {
            return $manager->get($arguments[0], $arguments[1] ?? null);
        }

        if (is_array($arguments[0])) {
            return $manager->put(
                key($arguments[0]),
                reset($arguments[0]),
                $arguments[1] ?? null,
            );
        }

        throw new \InvalidArgumentException(
            'When calling the cache helper with arguments, you must pass a string key or an array.'
        );
    }
}

if (!function_exists('storage')) {
    /**
     * Get a filesystem disk instance.
     *
     * @param  string|null  $disk  Disk name (null for the default disk).
     * @return mixed  The filesystem disk instance.
     */
    function storage(?string $disk = null): mixed
    {
        return App::container()->make(FilesystemManager::class)->disk($disk);
    }
}

// ============================================================================
// 8. Queue & Jobs
// ============================================================================

if (!function_exists('dispatch')) {
    /**
     * Dispatch a job to the queue.
     *
     * @param  Job  $job  The job instance to dispatch.
     * @return void
     */
    function dispatch(Job $job): void
    {
        Dispatcher::dispatch($job);
    }
}

// ============================================================================
// 9. Validation
// ============================================================================

if (!function_exists('validator')) {
    /**
     * Create a new Validator instance for the given data and rules.
     *
     * @param  array  $data   Input data to validate.
     * @param  array  $rules  Validation rules.
     * @return \App\Core\Validation\Validator
     */
    function validator(array $data, array $rules): \App\Core\Validation\Validator
    {
        return \App\Core\Validation\Validator::make($data, $rules);
    }
}

// ============================================================================
// 10. Debugging & Profiling (development only)
// ============================================================================

if (!function_exists('dump')) {
    /**
     * Dump one or more values to the output (does not halt execution).
     *
     * @param  mixed  ...$args  Values to dump.
     * @return void
     */
    function dump(mixed ...$args): void
    {
        $dumper = new Dumper();

        foreach ($args as $arg) {
            $dumper->dump($arg);
        }
    }
}

if (!function_exists('dd')) {
    /**
     * Dump and die – dump one or more values then terminate.
     *
     * Uses exit(0) to signal clean termination rather than die(1) which
     * can confuse process managers and test harnesses.
     *
     * @param  mixed  ...$args  Values to dump.
     * @return never
     */
    function dd(mixed ...$args): never
    {
        $dumper = new Dumper();

        foreach ($args as $arg) {
            $dumper->dump($arg);
        }

        exit(0);
    }
}

if (!function_exists('ray')) {
    /**
     * Debug helper – dumps values for inspection.
     *
     * @deprecated Use dump() instead. This function is retained for backward compatibility.
     *
     * @param  mixed  ...$args  Values to dump.
     * @return void
     */
    function ray(mixed ...$args): void
    {
        (new Dumper())->dump($args);
    }
}

if (!function_exists('benchmark')) {
    /**
     * Benchmark a callback over N iterations and return the average execution time.
     *
     * @param  callable  $callback    The code to benchmark.
     * @param  int       $iterations  Number of times to run the callback.
     * @return float     Average execution time in seconds.
     */
    function benchmark(callable $callback, int $iterations = 1): float
    {
        $total = 0.0;

        for ($i = 0; $i < $iterations; $i++) {
            $start = microtime(true);
            $callback();
            $total += microtime(true) - $start;
        }

        return $total / $iterations;
    }
}

// ============================================================================
// 11. Logging
// ============================================================================

if (!function_exists('logger')) {
    /**
     * Log a debug message or get the logger instance.
     *
     * Called with no arguments: returns the Log instance.
     * Called with a message: logs it at debug level.
     *
     * @param  string|null  $message  Message to log.
     * @param  array        $context  Contextual data.
     * @return mixed  The Log instance or the result of the log call.
     */
    function logger(?string $message = null, array $context = []): mixed
    {
        if ($message === null) {
            return app(Log::class);
        }

        return app(Log::class)->debug($message, $context);
    }
}

// ============================================================================
// 12. String & Encoding Utilities
// ============================================================================

if (!function_exists('e')) {
    /**
     * Escape a value for safe HTML output.
     *
     * @param  mixed  $value  The value to escape (cast to string).
     * @return string  HTML-safe string.
     */
    function e(mixed $value): string
    {
        return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}
