<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Core\View\View;

class ViewTest extends \Tests\TestCase
{
    protected string $basePath;

    protected function setUp(): void
    {
        parent::setUp();
        $this->basePath = sys_get_temp_dir() . '/zero_view_test_' . uniqid();
        mkdir($this->basePath . '/views/layouts', 0777, true);
        mkdir($this->basePath . '/storage/cache/views', 0777, true);
        View::setBasePath($this->basePath);
        View::enableCache(false);
    }

    protected function tearDown(): void
    {
        $this->removeDirectory($this->basePath);
        View::setBasePath(null);
        View::enableCache(false);
        parent::tearDown();
    }

    private function removeDirectory(string $dir): void
    {
        if (!is_dir($dir)) {
            return;
        }
        $items = array_diff(scandir($dir), ['.', '..']);
        foreach ($items as $item) {
            $path = $dir . '/' . $item;
            is_dir($path) ? $this->removeDirectory($path) : unlink($path);
        }
        rmdir($dir);
    }

    public function testSetBasePathAndEnableCache(): void
    {
        View::setBasePath('/tmp/foo');
        View::enableCache(true);

        $this->assertTrue(View::cacheEnabled());

        View::enableCache(false);
        $this->assertFalse(View::cacheEnabled());
    }

    public function testFindViewReturnsPathWhenFileExists(): void
    {
        file_put_contents($this->basePath . '/views/welcome.php', '<?php echo "hello"; ?>');

        $path = View::findView('welcome');

        $this->assertNotNull($path);
        $this->assertStringEndsWith('welcome.php', $path);
    }

    public function testFindViewReturnsNullWhenMissing(): void
    {
        $this->assertNull(View::findView('nonexistent'));
    }

    public function testFindViewSupportsDotNotation(): void
    {
        mkdir($this->basePath . '/views/pages', 0777, true);
        file_put_contents($this->basePath . '/views/pages/home.php', '<?php echo "home"; ?>');

        $path = View::findView('pages.home');

        $this->assertNotNull($path);
        $this->assertStringEndsWith('pages/home.php', $path);
    }

    public function testFindLayoutReturnsPathWhenFileExists(): void
    {
        file_put_contents($this->basePath . '/views/layouts/guest.php', '<?php echo "{{ slot }}"; ?>');

        $path = View::findLayout('guest');

        $this->assertNotNull($path);
        $this->assertStringEndsWith('layouts/guest.php', $path);
    }

    public function testFindLayoutReturnsNullWhenMissing(): void
    {
        $this->assertNull(View::findLayout('nonexistent'));
    }

    public function testCachePathReturnsCorrectPath(): void
    {
        $path = View::cachePath();

        $this->assertStringContainsString('storage/cache/views', $path);
    }

    public function testRenderThrowsWhenViewNotFound(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('View missing not found.');

        View::render('missing');
    }

    public function testRenderThrowsWhenLayoutNotFound(): void
    {
        file_put_contents($this->basePath . '/views/welcome.php', '<?php echo "content"; ?>');

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Layout nonexistent not found.');

        View::render('welcome', [], 'nonexistent');
    }

    public function testRenderReturnsOutputWithData(): void
    {
        file_put_contents($this->basePath . '/views/greeting.php', '<?php echo "Hello " . $name; ?>');
        file_put_contents($this->basePath . '/views/layouts/app.php', '<?php echo "{{ slot }}"; ?>');

        ob_start();
        $output = View::render('greeting', ['name' => 'World'], 'app');
        ob_end_clean();

        $this->assertStringContainsString('Hello World', $output);
        $this->assertStringContainsString('Hello World', $output);
    }

    public function testRenderInjectsDataIntoView(): void
    {
        file_put_contents($this->basePath . '/views/profile.php', '<?php echo $user["name"]; ?>');
        file_put_contents($this->basePath . '/views/layouts/minimal.php', '<?php echo "{{ slot }}"; ?>');

        ob_start();
        $output = View::render('profile', ['user' => ['name' => 'John']], 'minimal');
        ob_end_clean();

        $this->assertStringContainsString('John', $output);
    }

    public function testRenderWithCacheEnabledStoresAndReusesCache(): void
    {
        file_put_contents($this->basePath . '/views/cached.php', '<?php echo "cached-content"; ?>');
        file_put_contents($this->basePath . '/views/layouts/plain.php', '<?php echo "{{ slot }}"; ?>');

        View::enableCache(true);

        ob_start();
        $output1 = View::render('cached', [], 'plain');
        ob_end_clean();
        $this->assertStringContainsString('cached-content', $output1);

        $cacheFiles = glob(View::cachePath() . '/*.php');
        $this->assertNotEmpty($cacheFiles);

        ob_start();
        $output2 = View::render('cached', [], 'plain');
        ob_end_clean();
        $this->assertSame($output1, $output2);
    }
}
