<?php

declare(strict_types=1);

namespace Tests\Feature;

use PHPUnit\Framework\TestCase;

class NewCommandTest extends TestCase
{
    private string $target;

    protected function setUp(): void
    {
        $this->target = sys_get_temp_dir() . '/zeroping_wizard_' . uniqid();
    }

    protected function tearDown(): void
    {
        if (is_dir($this->target)) {
            $this->remove($this->target);
        }
    }

    public function testScaffoldsEmptyProjectNonInteractive(): void
    {
        $repo = dirname(__DIR__, 2);
        $cmd = sprintf(
            'php -d opcache.enable_cli=0 %s new MyApp --type=empty --db=sqlite --no-auth --no-tailwind --no-crud --dir=%s 2>&1',
            escapeshellarg($repo . '/zero'),
            escapeshellarg($this->target)
        );

        exec($cmd, $output, $code);

        $this->assertSame(0, $code, "Wizard failed:\n" . implode("\n", $output));

        $this->assertFileExists($this->target . '/composer.json');
        $this->assertFileExists($this->target . '/public/index.php');
        $this->assertFileExists($this->target . '/.env');

        $composer = json_decode((string) file_get_contents($this->target . '/composer.json'), true);
        $this->assertSame('zeroping/myapp', $composer['name'] ?? null);

        $env = (string) file_get_contents($this->target . '/.env');
        $this->assertStringContainsString('DB_CONNECTION=sqlite', $env);
        $this->assertStringContainsString('ZEROPING_AUTH=false', $env);
        $this->assertStringContainsString('APP_KEY=', $env);
    }

    public function testScaffoldsBlogTemplateWithFlags(): void
    {
        $repo = dirname(__DIR__, 2);
        $cmd = sprintf(
            'php -d opcache.enable_cli=0 %s new BlogApp --type=blog --db=mysql --auth --tailwind --crud --name=BlogApp --dir=%s 2>&1',
            escapeshellarg($repo . '/zero'),
            escapeshellarg($this->target)
        );

        exec($cmd, $output, $code);

        $this->assertSame(0, $code, "Wizard failed:\n" . implode("\n", $output));

        $this->assertFileExists($this->target . '/app/Controllers/HomeController.php');
        $this->assertFileExists($this->target . '/app/Models/Post.php');
        $this->assertFileExists($this->target . '/config/routes.php');

        $env = (string) file_get_contents($this->target . '/.env');
        $this->assertStringContainsString('DB_CONNECTION=mysql', $env);
        $this->assertStringContainsString('ZEROPING_AUTH=true', $env);
        $this->assertStringContainsString('ZEROPING_TAILWIND=true', $env);
        $this->assertStringContainsString('ZEROPING_EXAMPLE_CRUD=true', $env);
    }

    private function remove(string $dir): void
    {
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
