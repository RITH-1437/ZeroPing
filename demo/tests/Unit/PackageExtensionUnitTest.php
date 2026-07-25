<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Core\Database\Connection;
use App\Core\Database\Drivers\SQLiteDriver;
use App\Core\Database\MigrationRunner;
use App\Core\Support\Config;
use App\Core\View\View;
use PHPUnit\Framework\TestCase;
use Zeroping\Support\Foundation\MigrationLoader;

class PackageExtensionUnitTest extends TestCase
{
    private array $tempDirs = [];

    protected function tearDown(): void
    {
        foreach ($this->tempDirs as $dir) {
            if (!is_dir($dir)) {
                continue;
            }

            foreach (array_reverse((array) @scandir($dir)) as $item) {
                if ($item === '.' || $item === '..') {
                    continue;
                }
                $path = $dir . '/' . $item;
                if (is_file($path)) {
                    @unlink($path);
                }
            }
            @rmdir($dir);
        }

        MigrationLoader::clear();
        MigrationRunner::clearPaths();
    }

    private function tempDir(string $name): string
    {
        $dir = sys_get_temp_dir() . '/zp_ext_' . $name . '_' . uniqid();
        mkdir($dir, 0777, true);
        $this->tempDirs[] = $dir;

        return $dir;
    }

    public function testConfigSetIsReadableThroughHelper(): void
    {
        Config::set('queue', ['driver' => 'sync', 'timeout' => 90]);

        $this->assertSame(['driver' => 'sync', 'timeout' => 90], Config::get('queue'));
        $this->assertSame('sync', Config::get('queue.driver'));
    }

    public function testViewNamespaceResolvesNamespacedView(): void
    {
        $dir = $this->tempDir('views') . '/pkg';
        mkdir($dir, 0777, true);
        file_put_contents($dir . '/index.php', '<?php echo "hi";');

        View::addNamespace('demo', $dir);

        $this->assertSame($dir . '/index.php', View::findView('demo::index'));
    }

    public function testMigrationRunnerDiscoversPackagePaths(): void
    {
        $dir = $this->tempDir('migrations');
        file_put_contents($dir . '/2024_01_01_000000_create_demo_table.php', '<?php return null;');

        MigrationRunner::addPath($dir);

        $runner  = new MigrationRunner(new Connection(new SQLiteDriver()));
        $migrations = $runner->getMigrations();

        $this->assertContains('2024_01_01_000000_create_demo_table.php', $migrations);
    }

    public function testMigrationLoaderPushesIntoRunner(): void
    {
        $dir = $this->tempDir('migrations2');
        file_put_contents($dir . '/2024_02_02_000000_create_other_table.php', '<?php return null;');

        MigrationLoader::addPath($dir);

        $runner  = new MigrationRunner(new Connection(new SQLiteDriver()));
        $migrations = $runner->getMigrations();

        $this->assertContains('2024_02_02_000000_create_other_table.php', $migrations);
    }
}
