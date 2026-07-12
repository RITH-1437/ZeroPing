<?php

namespace App\Core\Console\Commands;

use App\Core\Console\Command;

class MakeMiddlewareCommand extends Command
{
    protected string $signature = 'make:middleware';

    protected string $description = 'Create a new middleware class';

    public function handle(string $name): void
    {
        if (empty($name)) {
            $this->error('Usage: php zero make:middleware MiddlewareName');
            return;
        }

        if (!str_ends_with($name, 'Middleware')) {
            $name .= 'Middleware';
        }

        $content = $this->replace(
            $this->stub('middleware.stub'),
            ['class' => $name]
        );

        $file = BASE_PATH . "/app/Http/Middleware/{$name}.php";

        $this->writeGenerated($file, $content, 'Middleware');
    }
}
