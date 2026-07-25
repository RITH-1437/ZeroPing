<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Core\Application\App;
use App\Core\Cache\CacheRepository;
use App\Core\Cache\Drivers\ArrayCacheDriver;
use App\Core\Cache\Drivers\FileCacheDriver;
use App\Providers\CacheServiceProvider;
use PHPUnit\Framework\TestCase;

class CacheTest extends TestCase
{
    protected function setUp(): void
    {
        (new CacheServiceProvider(App::container()))->register();
    }

    private function repo(): CacheRepository
    {
        return new CacheRepository(new ArrayCacheDriver());
    }

    public function testPutGetHasForget(): void
    {
        $cache = $this->repo();

        $this->assertFalse($cache->has('k'));
        $this->assertTrue($cache->put('k', 'v', 60));
        $this->assertTrue($cache->has('k'));
        $this->assertSame('v', $cache->get('k'));
        $this->assertSame('def', $cache->get('missing', 'def'));
        $this->assertTrue($cache->forget('k'));
        $this->assertFalse($cache->has('k'));
    }

    public function testRememberAndForever(): void
    {
        $cache = $this->repo();

        $value = $cache->remember('r', 60, fn () => 'computed');
        $this->assertSame('computed', $value);
        $this->assertSame('computed', $cache->get('r'));

        $cache->rememberForever('f', fn () => 'perm');

        $this->assertSame('perm', $cache->get('f'));
    }

    public function testIncrementDecrement(): void
    {
        $cache = $this->repo();
        $cache->put('c', 5, 3600);

        $this->assertSame(6, $cache->increment('c'));
        $this->assertSame(5, $cache->decrement('c'));
    }

    public function testFlush(): void
    {
        $cache = $this->repo();
        $cache->put('a', 1, 60);
        $cache->put('b', 2, 60);

        $this->assertTrue($cache->flush());
        $this->assertFalse($cache->has('a'));
        $this->assertFalse($cache->has('b'));
    }

    public function testFileDriverPersists(): void
    {
        $path = sys_get_temp_dir() . '/zp_cache_' . uniqid();
        $cache = new CacheRepository(new FileCacheDriver(['path' => $path]));

        $cache->put('key', ['hello' => 'world'], 60);

        $this->assertSame(['hello' => 'world'], $cache->get('key'));
        $this->assertTrue($cache->forget('key'));
        $this->assertNull($cache->get('key'));
    }

    public function testCacheHelperUsesArrayStore(): void
    {
        cache()->store('array')->put('h', 'v', 60);

        $this->assertSame('v', cache()->store('array')->get('h'));
    }
}
