<?php

declare(strict_types=1);

namespace App\Core\Console\Commands;

use App\Core\Console\Command;

class ServeCommand extends Command
{
    protected string $signature = 'serve';
    protected string $description = 'Serve the application on the PHP development server';

    public function handle(array $args = []): void
    {
        $host = 'localhost';
        $rawPort = (string) ($args[0] ?? '1437');

        if (preg_match('/^[0-9]+$/D', $rawPort) !== 1 || (int) $rawPort < 1 || (int) $rawPort > 65535) {
            $this->error('Port must be an integer between 1 and 65535.');
            return;
        }

        $port = (int) $rawPort;
        $this->info("ZeroPing development server started on http://{$host}:{$port}");

        passthru('php -S ' . $host . ':' . $port . ' -t public');
    }
}
