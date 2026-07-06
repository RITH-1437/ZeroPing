<?php

namespace App\Core\Console\Commands;

use App\Core\Application\App;
use App\Core\Logging\Logger;

class LogTestCommand
{
    public function handle(): void
    {
        $logger = App::container()->make(Logger::class);

        $logger->info('Info message');

        $logger->warning('Warning message');

        $logger->error('Error message');

        echo "✅ Log created successfully.\n";
    }
}