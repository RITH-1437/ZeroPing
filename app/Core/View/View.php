<?php

namespace App\Core\View;

class View
{
    private static bool $cacheEnabled = false;

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

        if (self::$cacheEnabled) {
            self::storeToCache(self::cacheKey($view, $layout), $output);
        }

        echo $output;

        return $output;
    }

    private static function basePath(): string
    {
        return dirname(__DIR__, 3);
    }

    public static function findView(string $view): ?string
    {
        $path = self::basePath() . "/views/{$view}.php";
        return file_exists($path) ? $path : null;
    }

    public static function findLayout(string $layout): ?string
    {
        $path = self::basePath() . "/views/layouts/{$layout}.php";
        return file_exists($path) ? $path : null;
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