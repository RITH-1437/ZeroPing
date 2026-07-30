<?php

declare(strict_types=1);

namespace App\Core\View;

/**
 * Static view rendering engine for ZeroPing.
 *
 * Supports layouts, namespaces (vendor::view syntax), file-based caching,
 * and safe view-name validation. All methods are static for global convenience.
 *
 * @example
 *   View::setBasePath('/var/www/app');
 *   echo View::render('home', ['title' => 'Welcome']);
 *   echo View::renderWithLayout('dashboard', ['user' => $user], 'app');
 */
class View
{
    /**
     * The placeholder token used inside layout files to mark where
     * the rendered view content will be injected.
     */
    public const SLOT_PLACEHOLDER = '{{ slot }}';

    /** @var bool Whether file-based caching of rendered output is enabled. */
    private static bool $cacheEnabled = false;

    /** @var string|null Application base path override (null = auto-detect). */
    private static ?string $basePath = null;

    /** @var array<string, string> Registered view namespaces (namespace => directory). */
    private static array $namespaces = [];

    /** @var array<string, string|null> Resolved view/layout file path cache. */
    private static array $pathCache = [];

    /** @var bool Whether the cache directory has been verified to exist this request. */
    private static bool $cacheDirVerified = false;

    /**
     * Whether xxh128 hash algorithm is available (cached on first use).
     * @var bool|null
     */
    private static ?bool $hasXxh128 = null;

    /**
     * Pre-compiled regex pattern for view name validation (with namespace).
     */
    private const VIEW_NAME_PATTERN =
        '/^(?:[A-Za-z_][A-Za-z0-9_]*::)?[A-Za-z_][A-Za-z0-9_]*(?:[.\/][A-Za-z_0-9][A-Za-z0-9_-]*)*$/D';

    /**
     * Pre-compiled regex pattern for layout name validation (without namespace).
     */
    private const LAYOUT_NAME_PATTERN = '/^[A-Za-z_][A-Za-z0-9_]*(?:[.\/][A-Za-z_0-9][A-Za-z0-9_-]*)*$/D';

    // -------------------------------------------------------------------------
    // Configuration
    // -------------------------------------------------------------------------

    /**
     * Set the application base path used to locate views and layouts.
     *
     * Passing null resets to auto-detection (three directories above this file).
     * Clears the path cache so previously resolved paths are re-evaluated.
     *
     * @param string|null $path Absolute path to the application root, or null.
     */
    public static function setBasePath(?string $path): void
    {
        self::$basePath = $path;
        self::$pathCache = [];
        self::$cacheDirVerified = false;
    }

    /**
     * Enable or disable file-based view caching.
     *
     * When enabled, fully rendered output (view + layout) is stored to disk
     * and served on subsequent requests without re-executing the PHP templates.
     *
     * @param bool $enabled Whether caching should be active.
     */
    public static function enableCache(bool $enabled = true): void
    {
        self::$cacheEnabled = $enabled;

        if (!$enabled) {
            self::$cacheDirVerified = false;
        }
    }

    /**
     * Register a view namespace.
     *
     * Namespaced views are referenced as "namespace::view.name" and resolved
     * from the registered directory.
     *
     * @param string $namespace A valid PHP-style identifier (letters, digits, underscores).
     * @param string $path      Absolute path to the namespace's view directory.
     *
     * @throws \InvalidArgumentException If the namespace identifier is invalid.
     */
    public static function addNamespace(string $namespace, string $path): void
    {
        // Use ctype for fast validation of simple identifiers before regex.
        if ($namespace === '' || (!ctype_alpha($namespace[0]) && $namespace[0] !== '_')) {
            throw new \InvalidArgumentException("Invalid view namespace: {$namespace}");
        }

        if (!ctype_alnum(str_replace('_', '', $namespace))) {
            throw new \InvalidArgumentException("Invalid view namespace: {$namespace}");
        }

        self::$namespaces[$namespace] = rtrim($path, '/\\');
    }

    /**
     * Return all registered view namespaces.
     *
     * @return array<string, string> Namespace => directory mapping.
     */
    public static function namespaces(): array
    {
        return self::$namespaces;
    }

    /**
     * Check whether view caching is currently enabled.
     */
    public static function cacheEnabled(): bool
    {
        return self::$cacheEnabled;
    }

    // -------------------------------------------------------------------------
    // Rendering
    // -------------------------------------------------------------------------

    /**
     * Render a view template, optionally wrapped in a layout.
     *
     * Data keys are extracted into the view's local scope using EXTR_SKIP to
     * prevent overwriting internal renderer variables.
     *
     * @param string      $view   Dot-notation view name (e.g. "pages.home") or namespaced ("vendor::view").
     * @param array       $data   Associative array of variables available in the view.
     * @param string|null $layout Layout name (resolved from views/layouts/), or null for no layout.
     *
     * @return string The fully rendered HTML output.
     *
     * @throws \RuntimeException If the view or layout file cannot be found.
     */
    public static function render(string $view, array $data = [], ?string $layout = null): string
    {
        if (self::$cacheEnabled) {
            $cacheKey = self::cacheKey($view, $layout);
            $cached = self::loadFromCache($cacheKey);
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
        $output = str_replace(self::SLOT_PLACEHOLDER, $content, $output);

        if (self::$cacheEnabled) {
            self::storeToCache($cacheKey ?? self::cacheKey($view, $layout), $output);
        }

        return $output;
    }

    /**
     * Render a view wrapped in a specific layout.
     *
     * Convenience method that enforces a non-null layout parameter.
     *
     * @param string               $view   Dot-notation view name or namespaced view.
     * @param array<string, mixed> $data   Variables available in the view.
     * @param string               $layout Layout name (e.g. "app", "guest", "admin").
     *
     * @return string The fully rendered HTML output.
     *
     * @throws \RuntimeException If the view or layout file cannot be found.
     */
    public static function renderWithLayout(string $view, array $data, string $layout): string
    {
        return self::render($view, $data, $layout);
    }

    // -------------------------------------------------------------------------
    // View resolution
    // -------------------------------------------------------------------------

    /**
     * Check whether a view exists and can be resolved to a file.
     *
     * @param string $view Dot-notation view name or namespaced view.
     *
     * @return bool True if the view file exists, false otherwise.
     */
    public static function exists(string $view): bool
    {
        return self::findView($view) !== null;
    }

    /**
     * Resolve a view name to its absolute file path.
     *
     * Resolution order:
     * 1. If namespaced (vendor::view), looks in registered namespace directory.
     * 2. Looks in {basePath}/views/.
     * 3. If a custom basePath is set, falls back to the framework's own views directory.
     *
     * Results are cached in memory for the duration of the request.
     *
     * @param string $view Dot-notation view name or namespaced view.
     *
     * @return string|null Absolute path to the view file, or null if not found.
     */
    public static function findView(string $view): ?string
    {
        // Check path cache first (most common hot path).
        if (array_key_exists($view, self::$pathCache)) {
            return self::$pathCache[$view];
        }

        if (!self::isSafeViewName($view, true)) {
            return null;
        }

        if (str_contains($view, '::')) {
            [$namespace, $name] = explode('::', $view, 2);
            if (isset(self::$namespaces[$namespace])) {
                $file = self::$namespaces[$namespace] . '/' . str_replace('.', '/', $name) . '.php';
                if (is_file($file)) {
                    return self::$pathCache[$view] = $file;
                }
            }

            return self::$pathCache[$view] = null;
        }

        $relativePath = str_replace('.', '/', $view) . '.php';

        $file = self::basePath() . '/views/' . $relativePath;
        if (is_file($file)) {
            return self::$pathCache[$view] = $file;
        }

        if (self::$basePath !== null) {
            $frameworkFile = dirname(__DIR__, 3) . '/views/' . $relativePath;
            if (is_file($frameworkFile)) {
                return self::$pathCache[$view] = $frameworkFile;
            }
        }

        return self::$pathCache[$view] = null;
    }

    /**
     * Resolve a layout name to its absolute file path.
     *
     * Layouts are expected to reside in views/layouts/ under the base path.
     * Falls back to the framework's own layouts directory if a custom basePath is set.
     *
     * @param string $layout Layout name (without .php extension or path separators).
     *
     * @return string|null Absolute path to the layout file, or null if not found.
     */
    public static function findLayout(string $layout): ?string
    {
        $key = "layout:{$layout}";
        if (array_key_exists($key, self::$pathCache)) {
            return self::$pathCache[$key];
        }

        if (!self::isSafeViewName($layout, false)) {
            return self::$pathCache[$key] = null;
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

        return self::$pathCache[$key] = null;
    }

    // -------------------------------------------------------------------------
    // Cache management
    // -------------------------------------------------------------------------

    /**
     * Get the absolute path to the view cache directory.
     *
     * @return string Directory path (may not exist yet).
     */
    public static function cachePath(): string
    {
        return self::basePath() . '/storage/cache/views';
    }

    /**
     * Clear all cached view files from disk.
     *
     * Removes all .php files from the cache directory. Silently does nothing
     * if the cache directory does not exist.
     */
    public static function clearCache(): void
    {
        $directory = self::cachePath();

        if (!is_dir($directory)) {
            return;
        }

        $files = glob($directory . '/*.php');
        if ($files === false) {
            return;
        }

        foreach ($files as $file) {
            @unlink($file);
        }

        self::$cacheDirVerified = false;
    }

    // -------------------------------------------------------------------------
    // Internal helpers
    // -------------------------------------------------------------------------

    /**
     * Get the effective application base path.
     *
     * @return string Absolute base path (custom or auto-detected).
     */
    private static function basePath(): string
    {
        return self::$basePath ?? dirname(__DIR__, 3);
    }

    /**
     * Validate a view or layout name against allowed characters.
     *
     * Prevents directory traversal and other path manipulation attacks.
     * Uses pre-compiled constant patterns to avoid building regex on every call.
     *
     * @param string $name           The view/layout name to validate.
     * @param bool   $allowNamespace Whether the "namespace::" prefix is permitted.
     *
     * @return bool True if the name is safe to use in path resolution.
     */
    private static function isSafeViewName(string $name, bool $allowNamespace): bool
    {
        // Fast rejection: empty string or starts with invalid char.
        if ($name === '') {
            return false;
        }

        // Quick check: reject obvious path traversal without regex.
        if (str_contains($name, '..')) {
            return false;
        }

        return preg_match(
            $allowNamespace ? self::VIEW_NAME_PATTERN : self::LAYOUT_NAME_PATTERN,
            $name
        ) === 1;
    }

    /**
     * Generate a cache key from the view name and layout.
     *
     * Uses xxh128 (fast, non-cryptographic) when available, falling back to md5.
     * Neither is used for security purposes — only for unique file naming.
     *
     * @param string      $view   The view name.
     * @param string|null $layout The layout name, or null.
     *
     * @return string A hex string suitable for use as a filename.
     */
    private static function cacheKey(string $view, ?string $layout): string
    {
        $input = $view . '|' . ($layout ?? '__none__');

        // Cache the xxh128 availability check — only call hash_algos() once.
        if (self::$hasXxh128 === null) {
            self::$hasXxh128 = \in_array('xxh128', hash_algos(), true);
        }

        return self::$hasXxh128 ? hash('xxh128', $input) : md5($input);
    }

    /**
     * Attempt to load a rendered view from the file cache.
     *
     * @param string $key The cache key (filename without extension).
     *
     * @return string|null The cached content, or null if not available.
     */
    private static function loadFromCache(string $key): ?string
    {
        $file = self::cachePath() . '/' . $key . '.php';
        if (!is_file($file)) {
            return null;
        }

        $cached = file_get_contents($file);
        return $cached === false ? null : $cached;
    }

    /**
     * Store rendered view output to the file cache.
     *
     * Creates the cache directory only on the first write per request
     * (tracked via $cacheDirVerified flag).
     *
     * @param string $key     The cache key (filename without extension).
     * @param string $content The rendered HTML to cache.
     *
     * @throws \RuntimeException If the cache directory cannot be created or written to.
     */
    private static function storeToCache(string $key, string $content): void
    {
        $directory = self::cachePath();

        if (!self::$cacheDirVerified) {
            if (!is_dir($directory) && !mkdir($directory, 0755, true) && !is_dir($directory)) {
                throw new \RuntimeException("Unable to create view cache directory: {$directory}");
            }
            self::$cacheDirVerified = true;
        }

        if (file_put_contents($directory . '/' . $key . '.php', $content, LOCK_EX) === false) {
            throw new \RuntimeException('Unable to write the rendered view cache.');
        }
    }
}
