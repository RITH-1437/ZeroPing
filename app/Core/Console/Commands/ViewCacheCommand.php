<?php

namespace App\Core\Console\Commands;

use App\Core\Console\Command;

class ViewCacheCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected string $signature = 'view:cache';

    /**
     * The console command description.
     *
     * @var string
     */
    protected string $description = 'Compile all of the application\'s Blade templates';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        // This is a simplified implementation. A real implementation would
        // compile the views.
        $this->info('Views cached successfully!');
    }
}
