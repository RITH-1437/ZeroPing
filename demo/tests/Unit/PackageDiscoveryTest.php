<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Core\Config\Config;
use App\Core\Packages\ProviderRepository;
use PHPUnit\Framework\TestCase;

class PackageDiscoveryTest extends TestCase
{
    private string $basePath;

    protected function setUp(): void
    {
        $this->basePath = dirname(__DIR__, 2);
    }

    public function testDiscoversLocalPackages(): void
    {
        $repo = new ProviderRepository($this->basePath, $this->basePath . '/bootstrap/cache/_test_packages.php');

        $packages = $repo->discover();

        $this->assertArrayHasKey('zeroping/support', $packages);
        $this->assertArrayHasKey('zeroping/queue', $packages);

        $queue = $packages['zeroping/queue'];
        $this->assertSame(
            ['Zeroping\\Queue\\QueueServiceProvider'],
            $queue['providers']
        );
    }

    public function testBuildManifestAppliesEnabledConfig(): void
    {
        $repo = new ProviderRepository($this->basePath, $this->basePath . '/bootstrap/cache/_test_packages.php');

        $enabled = $repo->buildManifest(['zeroping/queue' => true], true);
        $this->assertTrue($enabled['zeroping/queue']['enabled']);
        $this->assertTrue($enabled['zeroping/support']['enabled']);

        $allDisabled = $repo->buildManifest([], false);
        $this->assertFalse($allDisabled['zeroping/queue']['enabled']);
        $this->assertFalse($allDisabled['zeroping/support']['enabled']);

        $override = $repo->buildManifest(['zeroping/support' => false], true);
        $this->assertFalse($override['zeroping/support']['enabled']);
        $this->assertTrue($override['zeroping/queue']['enabled']);
    }

    public function testResolveProvidersHonorsEnabled(): void
    {
        $repo = new ProviderRepository($this->basePath, $this->basePath . '/bootstrap/cache/_test_packages.php');

        $manifest = $repo->buildManifest(['zeroping/queue' => true], true);
        $this->assertSame(
            ['Zeroping\\Queue\\QueueServiceProvider'],
            $repo->resolveProviders($manifest)
        );

        $disabled = $repo->buildManifest([], false);
        $this->assertSame([], $repo->resolveProviders($disabled));
    }

    public function testCacheRoundTrip(): void
    {
        $cache = $this->basePath . '/bootstrap/cache/_test_packages.php';

        if (file_exists($cache)) {
            unlink($cache);
        }

        $repo = new ProviderRepository($this->basePath, $cache);
        $manifest = $repo->buildManifest([], true);

        $this->assertTrue($repo->cache($manifest));
        $this->assertSame($manifest, $repo->getCached());

        unlink($cache);
    }

    public function testConfigPackagesLoaded(): void
    {
        $file = $this->basePath . '/config/packages.php';

        $this->assertFileExists($file);

        $data = require $file;

        $this->assertSame(
            ['zeroping/support' => true, 'zeroping/queue' => false],
            $data
        );
    }
}
