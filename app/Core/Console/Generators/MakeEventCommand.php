<?php

declare(strict_types=1);

namespace App\Core\Console\Generators;

class MakeEventCommand extends Generator
{
    protected string $description = 'Create a new event class';

    public function handle(string $name): void
    {
        if (empty($name)) {
            $this->warn('Usage: php zero make:event EventName');

            return;
        }

        $name = $this->ensureSuffix($name, 'Event');

        $content = $this->replace($this->stub('event.stub'), [
            'class' => $name,
        ]);

        $file = BASE_PATH . "/app/Events/{$name}.php";

        $this->writeGenerated($file, $content, 'Event');
    }
}
