<?php

namespace App\Core\Console\Commands;

use App\Core\Console\Command;
use FrameworkSite\SearchIndex;

class SearchIndexCommand extends Command
{
    protected string $signature = 'search:index';

    protected string $description = 'Build the documentation search index';

    public function handle(): void
    {
        $index = new SearchIndex();

        $index->build();

        $this->info('Search index built successfully!');
    }
}
