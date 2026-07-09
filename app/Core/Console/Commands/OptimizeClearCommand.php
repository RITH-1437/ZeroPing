<?php

namespace App\Core\Console\Commands;

use App\Core\Console\Command;

class OptimizeClearCommand extends Command
{
    protected string $signature = 'optimize:clear';
    protected string $description = 'Remove all cached files';

    public function handle(): void
    {
        $this->call('config:clear');
        $this->call('route:clear');
        $this->call('view:clear');

        $searchCache = BASE_PATH . '/storage/cache/search.index';
        if (file_exists($searchCache)) {
            unlink($searchCache);
            $this->info('Search index cache cleared!');
        }

        $this->info('All caches cleared successfully!');
    }
}
