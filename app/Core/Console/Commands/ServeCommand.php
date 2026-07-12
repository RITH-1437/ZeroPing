<?php

namespace App\Core\Console\Commands;

use App\Core\Console\Command;

class ServeCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected string $signature = 'serve';

    /**
     * The console command description.
     *
     * @var string
     */
    protected string $description = 'Serve the application on the PHP development server';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(array $args = []): void
    {
        $host = 'localhost';
        $port = (int) ($args[0] ?? 1437);

        $this->info("ZeroPing development server started on http://{$host}:{$port}");

        passthru("php -S {$host}:{$port} -t public");
    }
}
