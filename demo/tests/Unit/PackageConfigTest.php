<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Core\Packages\PackageConfig;
use App\Core\Packages\StarterKit;
use PHPUnit\Framework\TestCase;

class PackageConfigTest extends TestCase
{
    private string $dir;

    protected function setUp(): void
    {
        $this->dir = sys_get_temp_dir() . '/zp_cfg_' . uniqid();
        mkdir($this->dir . '/config', 0777, true);
    }

    protected function tearDown(): void
    {
        $file = $this->dir . '/config/packages.php';

        if (file_exists($file)) {
            unlink($file);
        }

        rmdir($this->dir . '/config');
        rmdir($this->dir);
    }

    private function config(): PackageConfig
    {
        return new PackageConfig($this->dir);
    }

    public function testEnableWritesShortArraySyntax(): void
    {
        $this->config()->enable('zeroping/queue');

        $this->assertFileExists($this->dir . '/config/packages.php');
        $this->assertTrue($this->config()->isEnabled('zeroping/queue'));

        $contents = (string) file_get_contents($this->dir . '/config/packages.php');
        $this->assertStringContainsString("return [\n", $contents);
        $this->assertStringContainsString("'zeroping/queue' => true,", $contents);
    }

    public function testDisableFlipsState(): void
    {
        $this->config()->enable('zeroping/queue');
        $this->config()->disable('zeroping/queue');

        $this->assertFalse($this->config()->isEnabled('zeroping/queue'));
        $this->assertTrue($this->config()->has('zeroping/queue'));
    }

    public function testRemoveDeletesKey(): void
    {
        $this->config()->enable('zeroping/queue');
        $this->config()->remove('zeroping/queue');

        $this->assertFalse($this->config()->has('zeroping/queue'));
    }
}

class StarterKitTest extends TestCase
{
    public function testKitsAreDefined(): void
    {
        $kits = StarterKit::kits();

        foreach (['arena', 'ecommerce', 'api'] as $kit) {
            $this->assertArrayHasKey($kit, $kits, "Starter kit {$kit} should exist.");
            $this->assertNotEmpty($kits[$kit]['packages']);
            $this->assertNotEmpty($kits[$kit]['notes']);
        }
    }

    public function testExistsAndNames(): void
    {
        $this->assertTrue(StarterKit::exists('arena'));
        $this->assertFalse(StarterKit::exists('nope'));
        $this->assertTrue(in_array('api', StarterKit::names(), true));
    }
}
