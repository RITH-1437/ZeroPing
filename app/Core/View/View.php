<?php

namespace App\Core\View;

class View
{
    private static bool $cacheEnabled = false;
    private static ?string $basePath = null;

    /**
     * Resolved view/layout paths, cached so repeated renders of the same view
     * (e.g. inside a loop) skip the file_exists() scan.
     *
     * @var array<string, string|null>
     */
    private static array $pathCache = [];

    public static function setBasePath(?string $path): void
    {
        self::$basePath = $path;

        // Cached view/layout paths are absolute and tied to the previous base
        // path, so they must be invalidated when the base path changes.
        self::$pathCache = [];
    }

    public static function enableCache(bool $enabled = true): void
    {
        self::$cacheEnabled = $enabled;
    }

    public static function render(
        string $view,
        array $data = [],
        string $layout = 'guest'
    ): string {

        if (self::$cacheEnabled) {
            $cacheKey = self::cacheKey($view, $layout);
            $cached = self::loadFromCache($cacheKey);
            if ($cached !== null) {
                echo $cached;
                return $cached;
            }
        }

        extract($data);

        $viewFile = self::findView($view);
        if ($viewFile === null) {
            throw new \RuntimeException("View {$view} not found.");
        }

        ob_start();
        require $viewFile;
        $content = ob_get_clean();

        $layoutFile = self::findLayout($layout);
        if ($layoutFile === null) {
            throw new \RuntimeException("Layout {$layout} not found.");
        }

        ob_start();
        require $layoutFile;
        $output = ob_get_clean();

        $output = str_replace('{{ slot }}', $content, $output);

        if (self::$cacheEnabled) {
            self::storeToCache(self::cacheKey($view, $layout), $output);
        }

        echo $output;

        return $output;
    }

    private static function basePath(): string
    {
        return self::$basePath ?? dirname(__DIR__, 3);
    }

    public static function findView(string $view): ?string
    {
        if (array_key_exists($view, self::$pathCache)) {
            return self::$pathCache[$view];
        }

        $path = self::basePath() . "/views/" . str_replace('.', '/', $view) . ".php";
        if (file_exists($path)) {
            return self::$pathCache[$view] = $path;
        }
        if (self::$basePath !== null) {
            $frameworkPath = dirname(__DIR__, 3) . "/views/" . str_replace('.', '/', $view) . ".php";
            if (file_exists($frameworkPath)) {
                return self::$pathCache[$view] = $frameworkPath;
            }
        }
        // Not found: do not cache the miss (a view may appear later).
        return null;
    }

    public static function findLayout(string $layout): ?string
    {
        $key = "layout:{$layout}";
        if (array_key_exists($key, self::$pathCache)) {
            return self::$pathCache[$key];
        }

        $path = self::basePath() . "/views/layouts/{$layout}.php";
        if (file_exists($path)) {
            return self::$pathCache[$key] = $path;
        }
        if (self::$basePath !== null) {
            $frameworkPath = dirname(__DIR__, 3) . "/views/layouts/{$layout}.php";
            if (file_exists($frameworkPath)) {
                return self::$pathCache[$key] = $frameworkPath;
            }
        }
        // Not found: do not cache the miss (a layout may appear later).
        return null;
    }

    public static function cachePath(): string
    {
        return self::basePath() . '/storage/cache/views';
    }

    public static function cacheEnabled(): bool
    {
        return self::$cacheEnabled;
    }

    private static function cacheKey(string $view, string $layout): string
    {
        return md5($view . '|' . $layout);
    }

    private static function loadFromCache(string $key): ?string
    {
        $file = self::cachePath() . '/' . $key . '.php';
        if (!file_exists($file)) {
            return null;
        }

        $cached = file_get_contents($file);
        return $cached !== false ? $cached : null;
    }

    private static function storeToCache(string $key, string $content): void
    {
        $dir = self::cachePath();
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }
        file_put_contents($dir . '/' . $key . '.php', $content);
    }
}
