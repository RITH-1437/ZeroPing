<?php

declare(strict_types=1);

namespace App\Core\Console\Generators;

class MakeJobCommand extends Generator
{
    protected string $description = 'Create a new queue job class';

    public function handle(string $name): void
    {
        if (empty($name)) {
            $this->warn('Usage: php zero make:job JobName');

            return;
        }

        $name = $this->ensureSuffix($name, 'Job');

        $content = $this->replace($this->stub('job.stub'), [
            'class' => $name,
        ]);

        $file = BASE_PATH . "/app/Jobs/{$name}.php";

        $this->writeGenerated($file, $content, 'Job');
    }
}
