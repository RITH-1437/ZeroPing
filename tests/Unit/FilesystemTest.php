<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Core\Application\App;
use App\Core\Filesystem\FilesystemRepository;
use App\Core\Filesystem\Drivers\LocalDriver;
use App\Core\Support\Config;
use App\Providers\FilesystemServiceProvider;
use PHPUnit\Framework\TestCase;

class FilesystemTest extends TestCase
{
    private string $root;

    protected function setUp(): void
    {
        $this->root = sys_get_temp_dir() . '/zp_fs_' . uniqid();
        mkdir($this->root, 0777, true);
    }

    protected function tearDown(): void
    {
        $this->deleteRecursive($this->root);
    }

    private function deleteRecursive(string $dir): void
    {
        if (!is_dir($dir)) {
            return;
        }

        foreach (array_diff(scandir($dir), ['.', '..']) as $item) {
            $path = $dir . '/' . $item;
            is_dir($path) ? $this->deleteRecursive($path) : unlink($path);
        }

        rmdir($dir);
    }

    private function repo(): FilesystemRepository
    {
        return new FilesystemRepository(new LocalDriver(['root' => $this->root]));
    }

    public function testPutGetExistsDelete(): void
    {
        $fs = $this->repo();

        $this->assertFalse($fs->exists('a.txt'));
        $this->assertTrue($fs->put('a.txt', 'hello'));
        $this->assertTrue($fs->exists('a.txt'));
        $this->assertSame('hello', $fs->get('a.txt'));
        $this->assertTrue($fs->delete('a.txt'));
        $this->assertFalse($fs->exists('a.txt'));
    }

    public function testCopyMoveSizeLastModified(): void
    {
        $fs = $this->repo();
        $fs->put('src.txt', 'content');

        $this->assertTrue($fs->copy('src.txt', 'copy.txt'));
        $this->assertSame('content', $fs->get('copy.txt'));
        $this->assertSame(7, $fs->size('src.txt'));

        $this->assertTrue($fs->move('src.txt', 'moved.txt'));
        $this->assertFalse($fs->exists('src.txt'));
        $this->assertTrue($fs->exists('moved.txt'));
        $this->assertGreaterThan(0, $fs->lastModified('moved.txt'));
    }

    public function testDirectoriesAndListing(): void
    {
        $fs = $this->repo();
        $fs->put('one.txt', '1');
        $fs->put('sub/two.txt', '2');

        $files = $fs->files('', true);
        sort($files);
        $this->assertSame(['one.txt', 'sub/two.txt'], $files);

        $this->assertTrue($fs->deleteDirectory('sub'));
        $this->assertSame(['one.txt'], $fs->files(''));
    }

    public function testStorageHelper(): void
    {
        (new FilesystemServiceProvider(App::container()))->register();

        Config::set('filesystem.disks.tmp', ['driver' => 'local', 'root' => $this->root]);

        storage('tmp')->put('via-helper.txt', 'ok');

        $this->assertSame('ok', storage('tmp')->get('via-helper.txt'));
    }
}
