<?php

declare(strict_types=1);

namespace App\Core\Console\Commands;

use App\Core\Console\Command;

class MakeScopeCommand extends Command
{
    protected string $signature = 'make:scope';

    protected string $description = 'Create a new global query scope';

    public function handle(string $name): void
    {
        if (empty($name)) {
            $this->error('Usage: php zero make:scope ScopeName');
            return;
        }

        if (!str_ends_with($name, 'Scope')) {
            $name .= 'Scope';
        }

        $content = $this->replace(
            $this->stub('scope.stub'),
            ['class' => $name]
        );

        $file = BASE_PATH . "/app/Core/ORM/Scopes/{$name}.php";

        $this->writeGenerated($file, $content, 'Scope');
    }
}
