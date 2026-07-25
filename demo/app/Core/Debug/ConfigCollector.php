<?php

namespace App\Core\Debug;

use App\Core\Support\Config;

class ConfigCollector implements Collector
{
    public function getName(): string
    {
        return 'config';
    }

    public function render(): string
    {
        $count = count(Config::all());

        return "<span>Config files: {$count}</span>";
    }
}
