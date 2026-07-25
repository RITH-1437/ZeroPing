<?php

declare(strict_types=1);

namespace App\Core\Packages;

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
    public function __construct(
        private string $basePath,
        private string $cachePath
    ) {
    }

    /**
     * Scan local `packages/` and `vendor/composer/installed.json` for
     * ZeroPing packages.
     *
     * @return array<string, array{name:string,path:string,providers:string[]}>
     */
    public function discover(): array
    {
        $packages = [];

        $localDir = $this->basePath . '/packages';

        if (is_dir($localDir)) {
            foreach (glob($localDir . '/*/*/composer.json') ?: [] as $file) {
                $pkg = $this->readComposer($file);

                if ($pkg !== null) {
                    $packages[$pkg['name']] = $pkg;
                }
            }
        }

        $installed = $this->basePath . '/vendor/composer/installed.json';

        if (file_exists($installed)) {
            $data = json_decode((string) file_get_contents($installed), true);

            foreach (($data['packages'] ?? []) as $pkgData) {
                if (!isset($pkgData['extra']['zeroping']['providers'])) {
                    continue;
                }

                $name = $pkgData['name'];

                if (isset($packages[$name])) {
                    continue;
                }

                $packages[$name] = [
                    'name'      => $name,
                    'path'      => $this->basePath . '/vendor/' . $name,
                    'providers' => $pkgData['extra']['zeroping']['providers'],
                ];
            }
        }

        return $packages;
    }

    /**
     * Resolve the manifest, applying the config/packages.php enable/disable
     * map and the PACKAGE_AUTO_DISCOVER flag.
     *
     * @param array<string, bool> $enabledConfig
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
     * @return string[] Flat list of provider classes to register.
     */
    public function resolveProviders(array $manifest): array
    {
        $providers = [];

        foreach ($manifest as $pkg) {
            if (!($pkg['enabled'] ?? true)) {
                continue;
            }

            foreach (($pkg['providers'] ?? []) as $provider) {
                $providers[] = $provider;
            }
        }

        return $providers;
    }

    public function cache(array $manifest): bool
    {
        $dir = dirname($this->cachePath);

        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }

        $export = var_export($manifest, true);

        return file_put_contents($this->cachePath, "<?php\n\nreturn {$export};\n") !== false;
    }

    public function getCached(): ?array
    {
        if (!file_exists($this->cachePath)) {
            return null;
        }

        $data = require $this->cachePath;

        return is_array($data) ? $data : null;
    }

    /**
     * @return array{name:string,path:string,providers:string[]}|null
     */
    private function readComposer(string $path): ?array
    {
        $data = json_decode((string) file_get_contents($path), true);

        if (
            !is_array($data)
            || empty($data['name'])
            || !isset($data['extra']['zeroping']['providers'])
        ) {
            return null;
        }

        return [
            'name'      => $data['name'],
            'path'      => dirname($path),
            'providers' => $data['extra']['zeroping']['providers'],
        ];
    }
}
