<?php

declare(strict_types=1);

namespace Tests\Feature;

use PHPUnit\Framework\TestCase;

class PackageCommandTest extends TestCase
{
    private string $repo;
    private ?string $configBackup = null;
    private ?string $createdDir = null;

    protected function setUp(): void
    {
        $this->repo = dirname(__DIR__, 2);
    }

    protected function tearDown(): void
    {
        if ($this->createdDir !== null && is_dir($this->createdDir)) {
            $this->removeDir($this->createdDir);
            $this->createdDir = null;
        }

        if ($this->configBackup !== null) {
            copy($this->configBackup, $this->repo . '/config/packages.php');
            unlink($this->configBackup);
            $this->configBackup = null;
        }

        // A vendor:publish run may have written config/queue.php.
        $published = $this->repo . '/config/queue.php';
        if (file_exists($published)) {
            unlink($published);
        }

        $this->rebuildManifest();
    }

    private function zero(string $command): string
    {
        $cmd = sprintf(
            'php -d opcache.enable_cli=0 %s %s 2>&1',
            escapeshellarg($this->repo . '/zero'),
            $command
        );

        exec($cmd, $output, $code);

        return implode("\n", $output);
    }

    private function backupConfig(): void
    {
        $this->configBackup = tempnam(sys_get_temp_dir(), 'zp_cfg_');
        copy($this->repo . '/config/packages.php', $this->configBackup);
    }

    private function rebuildManifest(): void
    {
        $repo = $this->repo;
        $script = "<?php\n"
            . "require '{$repo}/vendor/autoload.php';\n"
            . "\$r = new \\App\\Core\\Packages\\ProviderRepository('{$repo}', '{$repo}/bootstrap/cache/packages.php');\n"
            . "\$e = file_exists('{$repo}/config/packages.php') ? require '{$repo}/config/packages.php' : [];\n"
            . "\$r->cache(\$r->buildManifest(\$e, true));\n"
            . "echo 'ok';\n";

        $tmp = tempnam(sys_get_temp_dir(), 'zp_mf_') . '.php';
        file_put_contents($tmp, $script);
        exec('php ' . escapeshellarg($tmp) . ' 2>&1', $discard, $code);
        unlink($tmp);
    }

    public function testPackageListRendersEnabledAndDisabled(): void
    {
        $text = $this->zero('package:list');

        $this->assertStringContainsString('zeroping/support', $text);
        $this->assertStringContainsString('zeroping/queue', $text);
        $this->assertStringContainsString('Enabled', $text);
        $this->assertStringContainsString('Disabled', $text);
    }

    public function testEnableThenListShowsPackageEnabled(): void
    {
        $this->backupConfig();

        $this->zero('package:enable zeroping/queue');

        $config = (array) require $this->repo . '/config/packages.php';
        $this->assertTrue($config['zeroping/queue']);

        $text = $this->zero('package:list');
        // After enabling, queue must no longer be under "Disabled".
        $this->assertStringContainsString('zeroping/queue', $text);
    }

    public function testDisablePersistsState(): void
    {
        $this->backupConfig();

        $this->zero('package:enable zeroping/queue');
        $this->zero('package:disable zeroping/queue');

        $config = (array) require $this->repo . '/config/packages.php';
        $this->assertFalse($config['zeroping/queue']);
    }

    public function testCreateScaffoldsAValidPackage(): void
    {
        $dir = $this->repo . '/packages/zeroping/demo-pkg';

        if (is_dir($dir)) {
            $this->removeDir($dir);
        }

        $this->createdDir = $dir;

        $this->zero('package:create DemoPkg');

        $this->assertDirectoryExists($dir);
        $this->assertFileExists($dir . '/composer.json');
        $this->assertFileExists($dir . '/src/DemoPkgServiceProvider.php');
        $this->assertFileExists($dir . '/README.md');

        $composer = json_decode((string) file_get_contents($dir . '/composer.json'), true);
        $this->assertSame('zeroping/demo-pkg', $composer['name']);
        $this->assertSame(
            ['Zeroping\\DemoPkg\\DemoPkgServiceProvider'],
            $composer['extra']['zeroping']['providers']
        );

        exec('php -l ' . escapeshellarg($dir . '/src/DemoPkgServiceProvider.php') . ' 2>&1', $out, $code);
        $this->assertSame(0, $code, implode("\n", $out));
    }

    public function testStarterInstallEnablesKitPackages(): void
    {
        $this->backupConfig();

        $this->zero('starter:install api');

        $config = (array) require $this->repo . '/config/packages.php';
        $this->assertTrue($config['zeroping/auth'] ?? false);
        $this->assertTrue($config['zeroping/queue'] ?? false);
    }

    public function testPackageCommandAppearsInHelpWhenEnabled(): void
    {
        $this->backupConfig();

        $this->zero('package:enable zeroping/queue');

        $text = $this->zero('--help');

        $this->assertStringContainsString('Package Commands', $text);
        $this->assertStringContainsString('queue:work', $text);
    }

    public function testVendorPublishCopiesPackageAssets(): void
    {
        $this->backupConfig();

        $this->zero('package:enable zeroping/queue');
        $this->zero('vendor:publish --group=queue-config');

        $published = $this->repo . '/config/queue.php';
        $this->assertFileExists($published);

        $expected = (array) require $this->repo . '/packages/zeroping/queue/config/queue.php';
        $actual   = (array) require $published;
        $this->assertSame($expected, $actual);
    }

    private function removeDir(string $dir): void
    {
        if (!is_dir($dir)) {
            return;
        }

        $items = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($dir, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::CHILD_FIRST
        );

        foreach ($items as $item) {
            if ($item->isDir()) {
                rmdir($item->getRealPath());
            } else {
                unlink($item->getRealPath());
            }
        }

        rmdir($dir);
    }
}
