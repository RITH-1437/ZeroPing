<?php

declare(strict_types=1);

namespace App\Core\Console\Generators;

class MakeListenerCommand extends Generator
{
    protected string $description = 'Create a new event listener';

    public function handle(string $name): void
    {
        if (empty($name)) {
            $this->warn('Usage: php zero make:listener ListenerName [--event=EventName]');

            return;
        }

        $name = $this->ensureSuffix($name, 'Listener');

        $event = $this->option('event');
        $eventClass = $event !== null ? $this->ensureSuffix((string) $event, 'Event') : 'Event';
        $eventImport = $event !== null
            ? "use App\\Events\\{$eventClass};"
            : 'use App\\Core\\Events\\Event;';

        $content = $this->replace($this->stub('listener.stub'), [
            'class'        => $name,
            'event_class' => $eventClass,
            'event_import' => $eventImport,
        ]);

        $file = BASE_PATH . "/app/Listeners/{$name}.php";

        $this->writeGenerated($file, $content, 'Listener');
    }
}
