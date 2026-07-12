<?php

namespace App\Core\Console\Commands;

use App\Core\Console\Command;

class MakeTestCommand extends Command
{
    protected string $signature = 'make:test';

    protected string $description = 'Create a new test class';

    public function handle(string $name): void
    {
        if (empty($name) || str_starts_with($name, '-')) {
            $this->error('Usage: php zero make:test TestName [--feature]');
            return;
        }

        if (!str_ends_with($name, 'Test')) {
            $name .= 'Test';
        }

        $feature = $this->option('feature') !== null;

        $namespace = $feature ? 'Tests\\Feature' : 'Tests\\Unit';
        $directory = $feature ? 'tests/Feature' : 'tests/Unit';
        $base = $feature ? 'Feature' : 'Unit';

        $content = $this->replace(
            $this->stub('test.stub'),
            [
                'namespace' => $namespace,
                'class'     => $name,
            ]
        );

        $file = BASE_PATH . "/{$directory}/{$name}.php";

        $this->writeGenerated($file, $content, $base . ' test');
    }
}
