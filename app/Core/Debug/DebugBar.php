<?php

namespace App\Core\Debug;

class DebugBar
{
    protected array $collectors = [];

    public function __construct()
    {
        $this->addCollector(new PerformanceCollector());
        $this->addCollector(new SQLCollector());
        $this->addCollector(new RouteCollector());
        $this->addCollector(new MemoryCollector());
        $this->addCollector(new RequestCollector());
        $this->addCollector(new ConfigCollector());
    }

    public function addCollector(Collector $collector): void
    {
        $this->collectors[$collector->getName()] = $collector;
    }

    public function render(): string
    {
        $output = '<div id="zeroping-debugbar" style="position: fixed; bottom: 0; left: 0; right: 0; background: #f1f1f1; padding: 10px; border-top: 1px solid #ccc;">';

        foreach ($this->collectors as $collector) {
            $output .= $collector->render();
        }

        $output .= '</div>';

        return $output;
    }
}
