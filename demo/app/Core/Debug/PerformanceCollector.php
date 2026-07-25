<?php

namespace App\Core\Debug;

class PerformanceCollector implements Collector
{
    public function getName(): string
    {
        return 'performance';
    }

    public function render(): string
    {
        $start = defined('ZERO_PING_START') ? ZERO_PING_START : $_SERVER['REQUEST_TIME_FLOAT'] ?? microtime(true);
        $time = round((microtime(true) - $start) * 1000, 2);
        $memory = round(memory_get_peak_usage() / 1024 / 1024, 2);

        return "<span>Time: {$time}ms | Memory: {$memory}MB</span>";
    }
}
