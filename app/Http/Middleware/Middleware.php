<?php

namespace App\Http\Middleware;

abstract class Middleware
{
    abstract public function handle(): void;
}
