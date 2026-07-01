<?php

// require_once __DIR__ . '/Router.php';

class App
{
    public static function run(): void
    {
        Router::dispatch();
    }
}