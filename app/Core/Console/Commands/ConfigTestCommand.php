<?php

namespace App\Core\Console\Commands;

use App\Core\Console\Command;

class ConfigTestCommand extends Command
{
    public function handle(): void
    {
        echo "App Name: " . config('app.name') . PHP_EOL;
        echo "Environment: " . config('app.env') . PHP_EOL;
        echo "Database Host: " . config('database.host') . PHP_EOL;
    }
}