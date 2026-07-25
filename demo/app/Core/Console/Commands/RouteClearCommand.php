<?php

namespace App\Core\Console\Commands;

use App\Core\Console\Command;

class RouteClearCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected string $signature = 'route:clear';

    /**
     * The console command description.
     *
     * @var string
     */
    protected string $description = 'Remove the route cache file';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        $file = BASE_PATH . '/bootstrap/cache/routes.php';

        if (file_exists($file)) {
            unlink($file);
        }

        $this->info('Route cache cleared!');
    }
}
