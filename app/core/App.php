<?php

namespace App\Core;
class App
{
    public static function run(): void
    {
        Router::dispatch();
    }
}