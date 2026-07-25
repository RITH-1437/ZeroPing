<?php

namespace App\Core\Debug;

use App\Core\Database\Database;

class SQLCollector implements Collector
{
    public function getName(): string
    {
        return 'sql';
    }

    public function render(): string
    {
        $queries = Database::getLog();
        $count = count($queries);

        return "<span>SQL Queries: {$count}</span>";
    }
}
