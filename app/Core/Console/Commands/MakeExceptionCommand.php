<?php

declare(strict_types=1);

namespace App\Core\Console\Commands;

use App\Core\Console\Command;

class MakeExceptionCommand extends Command
{
    protected string $signature = 'make:exception';

    protected string $description = 'Create a new custom exception class';

    public function handle(string $name): void
    {
        if (empty($name)) {
            $this->error('Usage: php zero make:exception ExceptionName');
            return;
        }

        if (!str_ends_with($name, 'Exception')) {
            $name .= 'Exception';
        }

        $content = $this->replace(
            $this->stub('exception.stub'),
            ['class' => $name]
        );

        $file = BASE_PATH . "/app/Exceptions/{$name}.php";

        $this->writeGenerated($file, $content, 'Exception');
    }
}
