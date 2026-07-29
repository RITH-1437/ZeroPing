<?php

declare(strict_types=1);

namespace App\Core\View;

class View
{
    private static bool $cacheEnabled = false;
    private static ?string $basePath = null;

    /** @var array<string, string> */
    private static array $namespaces = [];

    /** @var array<string, string|null> */
    private static array $pathCache = [];

    public static function setBasePath(?string $path): void
    {
        self::$basePath = $path;
        self::$pathCache = [];
    }

    public static function enableCache(bool $enabled = true): void
    {
        self::$cacheEnabled = $enabled;
    }

    public static function render(string $view, array $data = [], ?string $layout = 'guest'): string
    {
        if (self::$cacheEnabled) {
            $cached = self::loadFromCache(self::cacheKey($view, $layout));
            if ($cached !== null) {
                return $cached;
            }
        }

        $viewFile = self::findView($view);
        if ($viewFile === null) {
            throw new \RuntimeException("View {$view} not found.");
        }

        $layoutFile = null;
        if ($layout !== null) {
            $layoutFile = self::findLayout($layout);
            if ($layoutFile === null) {
                throw new \RuntimeException("Layout {$layout} not found.");
            }
        }

        // EXTR_SKIP preserves renderer variables such as $layout and $content.
        // This prevents untrusted data keys from selecting an arbitrary layout
        // or otherwise affecting the include paths below.
        extract($data, EXTR_SKIP);

        ob_start();
        require $viewFile;
        $content = (string) ob_get_clean();

        if ($layoutFile === null) {
            return $content;
        }

        ob_start();
        require $layoutFile;
        $output = (string) ob_get_clean();
        $output = str_replace('{{ slot }}', $content, $output);

        if (self::$cacheEnabled) {
            self::storeToCache(self::cacheKey($view, $layout), $output);
        }

        return $output;
    }

    private static function basePath(): string
    {
        return self::$basePath ?? dirname(__DIR__, 3);
    }

    public static function findView(string $view): ?string
    {
        if (!self::isSafeViewName($view, true)) {
            return null;
        }
        if (array_key_exists($view, self::$pathCache)) {
            return self::$pathCache[$view];
        }

        if (str_contains($view, '::')) {
            [$namespace, $name] = explode('::', $view, 2);
            if (isset(self::$namespaces[$namespace])) {
                $file = self::$namespaces[$namespace] . '/' . str_replace('.', '/', $name) . '.php';
                if (is_file($file)) {
                    return self::$pathCache[$view] = $file;
                }
            }

            return null;
        }

        $file = self::basePath() . '/views/' . str_replace('.', '/', $view) . '.php';
        if (is_file($file)) {
            return self::$pathCache[$view] = $file;
        }

        if (self::$basePath !== null) {
            $frameworkFile = dirname(__DIR__, 3) . '/views/' . str_replace('.', '/', $view) . '.php';
            if (is_file($frameworkFile)) {
                return self::$pathCache[$view] = $frameworkFile;
            }
        }

        return null;
    }

    public static function findLayout(string $layout): ?string
    {
        if (!self::isSafeViewName($layout, false)) {
            return null;
        }

        $key = "layout:{$layout}";
        if (array_key_exists($key, self::$pathCache)) {
            return self::$pathCache[$key];
        }

        $file = self::basePath() . "/views/layouts/{$layout}.php";
        if (is_file($file)) {
            return self::$pathCache[$key] = $file;
        }

        if (self::$basePath !== null) {
            $frameworkFile = dirname(__DIR__, 3) . "/views/layouts/{$layout}.php";
            if (is_file($frameworkFile)) {
                return self::$pathCache[$key] = $frameworkFile;
            }
        }

        return null;
    }

    public static function cachePath(): string
    {
        return self::basePath() . '/storage/cache/views';
    }

    public static function addNamespace(string $namespace, string $path): void
    {
        if (preg_match('/^[A-Za-z_][A-Za-z0-9_]*$/D', $namespace) !== 1) {
            throw new \InvalidArgumentException("Invalid view namespace: {$namespace}");
        }

        self::$namespaces[$namespace] = rtrim($path, '/\\');
    }

    /** @return array<string, string> */
    public static function namespaces(): array
    {
        return self::$namespaces;
    }

    public static function cacheEnabled(): bool
    {
        return self::$cacheEnabled;
    }

    private static function isSafeViewName(string $name, bool $allowNamespace): bool
    {
        $namespace = $allowNamespace ? '(?:[A-Za-z_][A-Za-z0-9_]*::)?' : '';
        return preg_match('/^' . $namespace . '[A-Za-z_][A-Za-z0-9_]*(?:\.[A-Za-z_0-9][A-Za-z0-9_]*)*$/D', $name) === 1;
    }

    private static function cacheKey(string $view, ?string $layout): string
    {
        return hash('sha256', $view . '|' . ($layout ?? '__none__'));
    }

    private static function loadFromCache(string $key): ?string
    {
        $file = self::cachePath() . '/' . $key . '.php';
        if (!is_file($file)) {
            return null;
        }

        $cached = file_get_contents($file);
        return $cached === false ? null : $cached;
    }

    private static function storeToCache(string $key, string $content): void
    {
        $directory = self::cachePath();
        if (!is_dir($directory) && !mkdir($directory, 0755, true) && !is_dir($directory)) {
            throw new \RuntimeException("Unable to create view cache directory: {$directory}");
        }

        if (file_put_contents($directory . '/' . $key . '.php', $content, LOCK_EX) === false) {
            throw new \RuntimeException('Unable to write the rendered view cache.');
        }
    }
}
