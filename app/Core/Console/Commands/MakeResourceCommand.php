<?php

declare(strict_types=1);

namespace App\Core\Console\Commands;

use App\Core\Console\Command;

class MakeResourceCommand extends Command
{
    protected string $signature = 'make:resource';

    protected string $description = 'Create a new API resource class';

    public function handle(string $name): void
    {
        if (empty($name)) {
            $this->error('Usage: php zero make:resource ResourceName');
            return;
        }

        if (!str_ends_with($name, 'Resource')) {
            $name .= 'Resource';
        }

        $content = $this->replace(
            $this->stub('resource.stub'),
            ['class' => $name]
        );

        $file = BASE_PATH . "/app/Http/Resources/{$name}.php";

        $this->writeGenerated($file, $content, 'Resource');
    }
}
