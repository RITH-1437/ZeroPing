<?php

namespace App\Core\Filesystem;

use App\Core\Application\App;

class Storage
{
    public static function __callStatic(string $method, array $arguments)
    {
        $manager = App::container()->make(FilesystemManager::class);

        return $manager->$method(...$arguments);
    }
}
