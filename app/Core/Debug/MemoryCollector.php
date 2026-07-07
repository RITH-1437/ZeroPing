<?php

namespace App\Core\Debug;

class MemoryCollector implements Collector
{
    public function getName(): string
    {
        return 'memory';
    }

    public function render(): string
    {
        $memory = round(memory_get_peak_usage() / 1024 / 1024, 2);

        return "<span>Memory: {$memory}MB</span>";
    }
}
