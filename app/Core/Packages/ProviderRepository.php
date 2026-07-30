<?php

declare(strict_types=1);

namespace App\Core\Packages;

use App\Core\Support\Log;

/**
 * Discovers and resolves ZeroPing packages.
 *
 * A package is any Composer package (local in `packages/` or installed in
 * `vendor/`) whose `composer.json` declares service providers under
 * `extra.zeroping.providers`. Discovery has no hard-coded package names:
 * it simply scans manifests.
 *
 * The resolved manifest is cached to `bootstrap/cache/packages.php` by the
 * `post-autoload-dump` Composer hook (see scripts/discover-packages.php)
 * and honored at boot by {@see \App\Core\Application\App}.
 */
class ProviderRepository
{
    /** @var string Absolute path to the project root. */
    private readonly string $basePath;

    /** @var string Absolute path to the cache file. */
    private readonly string $cachePath;

    /**
     * Create a new ProviderRepository instance.
     *
     * @param string $basePath  Absolute path to the project root directory.
     * @param string $cachePath Absolute path to the packages cache file.
     */
    public function __construct(string $basePath, string $cachePath)
    {
        $this->basePath = $basePath;
        $this->cachePath = $cachePath;
    }

    /**
     * Scan local `packages/` and `vendor/composer/installed.json` for
     * ZeroPing packages.
     *
     * @return array<string, array{name: string, path: string, providers: string[]}>
     *    Discovered packages keyed by package name.
     */
    public function discover(): array
    {
        $packages = [];

        $packages = $this->discoverLocalPackages($packages);
        $packages = $this->discoverVendorPackages($packages);

        return $packages;
    }

    /**
     * Resolve the manifest, applying the config/packages.php enable/disable
     * map and the PACKAGE_AUTO_DISCOVER flag.
     *
     * @param array<string, bool> $enabledConfig Map of package names to enabled state.
     * @param bool                $autoDiscover  Whether to enable undeclared packages by default.
     *
     * @return array<string, array{name: string, path: string, providers: string[], enabled: bool}>
     *    The complete manifest with enabled state.
     */
    public function buildManifest(array $enabledConfig, bool $autoDiscover): array
    {
        $packages = $this->discover();

        foreach ($packages as $name => &$pkg) {
            $explicit = $enabledConfig[$name] ?? null;
            $pkg['enabled'] = $explicit !== null ? (bool) $explicit : $autoDiscover;
        }

        unset($pkg);

        return $packages;
    }

    /**
     * Extract a flat list of provider classes from a resolved manifest.
     *
     * Only includes providers from packages that are enabled.
     *
     * @param array<string, array{enabled?: bool, providers?: string[]}> $manifest
     *    The resolved manifest from {@see buildManifest()}.
     *
     * @return array<int, string> Flat list of fully-qualified provider class names.
     */
    public function resolveProviders(array $manifest): array
    {
        $providers = [];

        foreach ($manifest as $pkg) {
            if (!($pkg['enabled'] ?? true)) {
                continue;
            }

            foreach (($pkg['providers'] ?? []) as $provider) {
                if (is_string($provider) && $provider !== '') {
                    $providers[] = $provider;
                }
            }
        }

        return $providers;
    }

    /**
     * Write the manifest to the cache file.
     *
     * @param array<string, mixed> $manifest The manifest data to cache.
     *
     * @return bool True on success, false on failure.
     */
    public function cache(array $manifest): bool
    {
        $dir = dirname($this->cachePath);

        if (!is_dir($dir)) {
            if (!mkdir($dir, 0777, true) && !is_dir($dir)) {
                Log::warning("Failed to create cache directory: {$dir}");
                return false;
            }
        }

        $export = var_export($manifest, true);
        $content = "<?php\n\nreturn {$export};\n";

        $result = file_put_contents($this->cachePath, $content, LOCK_EX);

        if ($result === false) {
            Log::warning("Failed to write package cache: {$this->cachePath}");
            return false;
        }

        return true;
    }

    /**
     * Retrieve the cached manifest, or null if not available.
     *
     * Handles corruption gracefully: if the cache file exists but contains
     * invalid data (syntax errors, non-array content, or truncated writes),
     * the corrupted file is removed and null is returned, forcing re-discovery.
     *
     * @return array<string, mixed>|null The cached manifest, or null if cache miss/corrupt.
     */
    public function getCached(): ?array
    {
        if (!file_exists($this->cachePath)) {
            return null;
        }

        try {
            $data = @include $this->cachePath;
        } catch (\Throwable $e) {
            Log::warning(
                "Package cache corrupted (parse error), removing: {$this->cachePath}",
                ['exception' => $e->getMessage()]
            );
            $this->clearCache();
            return null;
        }

        if (!is_array($data)) {
            Log::warning(
                "Package cache corrupted (invalid data type), removing: {$this->cachePath}"
            );
            $this->clearCache();
            return null;
        }

        // Validate structure: each entry should have 'name' and 'providers'
        foreach ($data as $key => $entry) {
            if (!is_array($entry) || !isset($entry['name']) || !isset($entry['providers'])) {
                Log::warning(
                    "Package cache contains malformed entry '{$key}', removing cache: {$this->cachePath}"
                );
                $this->clearCache();
                return null;
            }
        }

        return $data;
    }

    /**
     * Remove the cached manifest file.
     *
     * @return bool True if the file was removed (or didn't exist), false on failure.
     */
    public function clearCache(): bool
    {
        if (!file_exists($this->cachePath)) {
            return true;
        }

        $result = @unlink($this->cachePath);

        if (!$result) {
            Log::warning("Failed to remove corrupted package cache: {$this->cachePath}");
        }

        return $result;
    }

    /**
     * Get the configured cache file path.
     *
     * @return string The absolute path to the cache file.
     */
    public function getCachePath(): string
    {
        return $this->cachePath;
    }

    /**
     * Discover packages from the local `packages/` directory.
     *
     * @param array<string, array{name: string, path: string, providers: string[]}> $packages
     *    Existing packages to merge into.
     *
     * @return array<string, array{name: string, path: string, providers: string[]}>
     *    Merged packages.
     */
    private function discoverLocalPackages(array $packages): array
    {
        $localDir = $this->basePath . '/packages';

        if (!is_dir($localDir)) {
            return $packages;
        }

        $composerFiles = glob($localDir . '/*/*/composer.json') ?: [];

        foreach ($composerFiles as $file) {
            $pkg = $this->readComposer($file);

            if ($pkg !== null) {
                $packages[$pkg['name']] = $pkg;
            }
        }

        return $packages;
    }

    /**
     * Discover packages from `vendor/composer/installed.json`.
     *
     * @param array<string, array{name: string, path: string, providers: string[]}> $packages
     *    Existing packages to merge into (local packages take priority).
     *
     * @return array<string, array{name: string, path: string, providers: string[]}>
     *    Merged packages.
     */
    private function discoverVendorPackages(array $packages): array
    {
        $installed = $this->basePath . '/vendor/composer/installed.json';

        if (!file_exists($installed)) {
            return $packages;
        }

        $rawContent = file_get_contents($installed);
        if ($rawContent === false) {
            Log::warning("Unable to read: {$installed}");
            return $packages;
        }

        $data = json_decode($rawContent, true);

        if (!is_array($data)) {
            Log::warning("Malformed installed.json: {$installed}");
            return $packages;
        }

        $packageList = $data['packages'] ?? [];

        if (!is_array($packageList)) {
            return $packages;
        }

        foreach ($packageList as $pkgData) {
            if (!is_array($pkgData)) {
                continue;
            }

            if (!isset($pkgData['extra']['zeroping']['providers'])) {
                continue;
            }

            $name = $pkgData['name'] ?? null;

            if (!is_string($name) || $name === '') {
                continue;
            }

            // Local packages take priority
            if (isset($packages[$name])) {
                continue;
            }

            $providers = $pkgData['extra']['zeroping']['providers'];

            if (!is_array($providers)) {
                continue;
            }

            $packages[$name] = [
                'name'      => $name,
                'path'      => $this->basePath . '/vendor/' . $name,
                'providers' => array_filter($providers, 'is_string'),
            ];
        }

        return $packages;
    }

    /**
     * Read and parse a composer.json file for ZeroPing package metadata.
     *
     * @param string $path Absolute path to the composer.json file.
     *
     * @return array{name: string, path: string, providers: string[]}|null
     *    The package metadata, or null if not a valid ZeroPing package.
     */
    private function readComposer(string $path): ?array
    {
        $rawContent = file_get_contents($path);

        if ($rawContent === false) {
            Log::warning("Unable to read composer.json: {$path}");
            return null;
        }

        $data = json_decode($rawContent, true);

        if (
            !is_array($data)
            || empty($data['name'])
            || !is_string($data['name'])
            || !isset($data['extra']['zeroping']['providers'])
            || !is_array($data['extra']['zeroping']['providers'])
        ) {
            return null;
        }

        return [
            'name'      => $data['name'],
            'path'      => dirname($path),
            'providers' => array_filter($data['extra']['zeroping']['providers'], 'is_string'),
        ];
    }
}
