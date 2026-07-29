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

    public function testEmptyProjectExcludesFrameworkInternalsAndMigrates(): void
    {
        $repo = dirname(__DIR__, 2);
        $cmd = sprintf(
            'php -d opcache.enable_cli=0 %s new MyApp --type=empty --db=sqlite --no-auth --no-tailwind --no-crud --dir=%s 2>&1',
            escapeshellarg($repo . '/zero'),
            escapeshellarg($this->target)
        );

        exec($cmd, $output, $code);
        $this->assertSame(0, $code, "Wizard failed:\n" . implode("\n", $output));

        // The generated project must NOT contain the framework repo's own
        // dev/test fixtures, logs or baked caches (they leak framework
        // internals and bloat every generated app).
        $this->assertDirectoryDoesNotExist(
            $this->target . '/tests/Unit',
            'Generated project must not ship the framework\'s internal Unit test suite.'
        );
        $this->assertFileDoesNotExist(
            $this->target . '/storage/test.txt',
            'Generated project must not ship framework debug/test files.'
        );
        $this->assertFileDoesNotExist(
            $this->target . '/database/migrations/001_create_users_table.php',
            'Generated project must not ship the framework repo\'s own dev migrations.'
        );
        $this->assertFileDoesNotExist(
            $this->target . '/bootstrap/cache/search.php',
            'Generated project must not ship the framework repo\'s baked documentation search cache.'
        );
        $this->assertFileDoesNotExist(
            $this->target . '/storage/logs/app.log',
            'Generated project must not ship the framework repo\'s own dev log files.'
        );

        // config/database.php must define the connection selected by .env
        // (regression test: template stub previously only defined `mysql`,
        // breaking `php zero migrate` on the documented SQLite default).
        $config = (string) file_get_contents($this->target . '/config/database.php');
        $this->assertStringContainsString(
            "'sqlite' =>",
            $config,
            'config/database.php must define a sqlite connection for the zero-config default.'
        );

        $migrate = sprintf(
            'php -d opcache.enable_cli=0 %s migrate 2>&1',
            escapeshellarg($this->target . '/zero')
        );
        exec($migrate, $migrateOutput, $migrateCode);
        $this->assertSame(0, $migrateCode, "php zero migrate failed:\n" . implode("\n", $migrateOutput));
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
