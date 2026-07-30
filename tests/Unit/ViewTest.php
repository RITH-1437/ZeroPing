<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Core\View\View;

/**
 * @covers \App\Core\View\View
 */
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

    // ─── Configuration ───────────────────────────────────────────────

    public function testSetBasePathAndEnableCache(): void
    {
        View::setBasePath('/tmp/foo');
        View::enableCache(true);

        $this->assertTrue(View::cacheEnabled());

        View::enableCache(false);
        $this->assertFalse(View::cacheEnabled());
    }

    // ─── View Resolution ─────────────────────────────────────────────

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

    // ─── Layout Resolution ───────────────────────────────────────────

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

    // ─── exists() ────────────────────────────────────────────────────

    public function testExistsReturnsTrueForExistingView(): void
    {
        file_put_contents($this->basePath . '/views/about.php', '<?php echo "about"; ?>');

        $this->assertTrue(View::exists('about'));
    }

    public function testExistsReturnsFalseForMissingView(): void
    {
        $this->assertFalse(View::exists('nonexistent'));
    }

    public function testExistsReturnsTrueForDotNotationView(): void
    {
        mkdir($this->basePath . '/views/admin', 0777, true);
        file_put_contents($this->basePath . '/views/admin/dashboard.php', '<?php echo "dash"; ?>');

        $this->assertTrue(View::exists('admin.dashboard'));
    }

    public function testExistsReturnsFalseForTraversalAttempt(): void
    {
        $this->assertFalse(View::exists('../outside'));
    }

    // ─── Cache Path ──────────────────────────────────────────────────

    public function testCachePathReturnsCorrectPath(): void
    {
        $path = View::cachePath();

        $this->assertStringContainsString('storage/cache/views', $path);
    }

    // ─── Rendering ───────────────────────────────────────────────────

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

    public function testRenderReturnsContentWithoutLayout(): void
    {
        file_put_contents($this->basePath . '/views/simple.php', '<?php echo "Hello World"; ?>');

        $output = View::render('simple');

        $this->assertSame('Hello World', $output);
    }

    public function testRenderInjectsDataIntoView(): void
    {
        file_put_contents($this->basePath . '/views/greeting.php', '<?php echo "Hello " . $name; ?>');

        $output = View::render('greeting', ['name' => 'World']);

        $this->assertStringContainsString('Hello World', $output);
    }

    public function testRenderWrapsContentInLayout(): void
    {
        file_put_contents($this->basePath . '/views/content.php', '<?php echo "inner"; ?>');
        file_put_contents($this->basePath . '/views/layouts/app.php', '<div><?php echo "{{ slot }}"; ?></div>');

        $output = View::render('content', [], 'app');

        $this->assertStringContainsString('<div>inner</div>', $output);
    }

    public function testRenderInjectsDataIntoViewWithLayout(): void
    {
        file_put_contents($this->basePath . '/views/profile.php', '<?php echo $user["name"]; ?>');
        file_put_contents($this->basePath . '/views/layouts/minimal.php', '<?php echo "{{ slot }}"; ?>');

        $output = View::render('profile', ['user' => ['name' => 'John']], 'minimal');

        $this->assertStringContainsString('John', $output);
    }

    // ─── Caching ─────────────────────────────────────────────────────

    public function testRenderWithCacheEnabledStoresAndReusesCache(): void
    {
        file_put_contents($this->basePath . '/views/cached.php', '<?php echo "cached-content"; ?>');
        file_put_contents($this->basePath . '/views/layouts/plain.php', '<?php echo "{{ slot }}"; ?>');

        View::enableCache(true);

        $output1 = View::render('cached', [], 'plain');
        $this->assertStringContainsString('cached-content', $output1);

        $cacheFiles = glob(View::cachePath() . '/*.php');
        $this->assertNotEmpty($cacheFiles);

        $output2 = View::render('cached', [], 'plain');
        $this->assertSame($output1, $output2);
    }

    // ─── clearCache() ────────────────────────────────────────────────

    public function testClearCacheRemovesCachedViewFiles(): void
    {
        file_put_contents($this->basePath . '/views/temp.php', '<?php echo "temp"; ?>');
        file_put_contents($this->basePath . '/views/layouts/plain.php', '<?php echo "{{ slot }}"; ?>');

        View::enableCache(true);
        View::render('temp', [], 'plain');

        $cacheFiles = glob(View::cachePath() . '/*.php');
        $this->assertNotEmpty($cacheFiles);

        View::clearCache();

        $cacheFilesAfter = glob(View::cachePath() . '/*.php');
        $this->assertEmpty($cacheFilesAfter);
    }

    public function testClearCacheDoesNotThrowWhenCacheDirectoryEmpty(): void
    {
        // Should not throw even when nothing is cached
        View::clearCache();
        $this->assertTrue(true);
    }

    // ─── Security ────────────────────────────────────────────────────

    public function testTraversalStyleViewAndLayoutNamesAreRejected(): void
    {
        $this->assertNull(View::findView('../outside'));
        $this->assertNull(View::findLayout('../guest'));
    }
}
