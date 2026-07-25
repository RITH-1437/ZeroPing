<?php

namespace App\Core\Debug;

class Stopwatch
{
    public static function start(string $name): void
    {
        Performance::start($name);
    }

    public static function stop(string $name): void
    {
        Performance::stop($name);
    }
}
