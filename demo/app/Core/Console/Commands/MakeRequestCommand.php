<?php

namespace App\Core\Console\Commands;

use App\Core\Console\Command;

class MakeRequestCommand extends Command
{
    protected string $signature = 'make:request';

    protected string $description = 'Create a new form request class';

    public function handle(string $name): void
    {
        if (empty($name)) {
            $this->error('Usage: php zero make:request RequestName');
            return;
        }

        if (!str_ends_with($name, 'Request')) {
            $name .= 'Request';
        }

        $content = $this->replace(
            $this->stub('request.stub'),
            ['class' => $name]
        );

        $file = BASE_PATH . "/app/Http/Requests/{$name}.php";

        $this->writeGenerated($file, $content, 'Request');
    }
}
