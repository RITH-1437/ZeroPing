<?php

namespace App\Core\Console\Commands;

use App\Core\Console\Command;

class OptimizeCommand extends Command
{
    protected string $signature = 'optimize';
    protected string $description = 'Cache config, routes, views, and search index';

    public function handle(): void
    {
        $this->call('config:cache');
        $this->call('route:cache');
        $this->call('view:cache');
        $this->call('search:index');

        $this->info('Application optimized successfully!');
    }
}
