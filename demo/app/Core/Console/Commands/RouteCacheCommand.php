<?php

namespace App\Core\Console\Commands;

use App\Core\Console\Command;
use App\Core\Routing\Router;

class RouteCacheCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected string $signature = 'route:cache';

    /**
     * The console command description.
     *
     * @var string
     */
    protected string $description = 'Create a route cache file for faster route registration';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        $routes = Router::routes();
        $this->write(
            BASE_PATH . '/bootstrap/cache/routes.php',
            '<?php return ' . var_export($routes, true) . ';'
        );

        $this->info('Routes cached successfully!');
    }
}
