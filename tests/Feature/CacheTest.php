<?php

namespace Tests\Feature;

use App\Core\Cache\Cache;
use App\Core\Testing\TestCase;

class CacheTest extends TestCase
{
    public function test_can_put_and_get_from_cache()
    {
        Cache::put('test', 'value', 60);
        $this->assertEquals('value', Cache::get('test'));
    }
}
