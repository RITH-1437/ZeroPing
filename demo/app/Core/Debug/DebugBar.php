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
        $output = '<div id="zeroping-debugbar" class="zeroping-debugbar">';

        foreach ($this->collectors as $collector) {
            $output .= $collector->render();
        }

        $output .= '</div>';

        return $output;
    }
}
