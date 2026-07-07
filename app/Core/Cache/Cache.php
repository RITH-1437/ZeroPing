<?php

namespace App\Core\Cache;

use App\Core\Application\App;

class Cache
{
    public static function __callStatic(string $method, array $arguments)
    {
        $manager = App::container()->make(CacheManager::class);

        return $manager->$method(...$arguments);
    }
}
