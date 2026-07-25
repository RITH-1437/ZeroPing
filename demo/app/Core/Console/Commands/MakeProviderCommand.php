<?php

namespace App\Core\Console\Commands;

use App\Core\Console\Command;

class MakeProviderCommand extends Command
{
    protected string $signature = 'make:provider';

    protected string $description = 'Create a new service provider class';

    public function handle(string $name): void
    {
        if (empty($name)) {
            $this->error('Usage: php zero make:provider ProviderName');
            return;
        }

        if (!str_ends_with($name, 'ServiceProvider')) {
            $name .= 'ServiceProvider';
        }

        $content = $this->replace(
            $this->stub('provider.stub'),
            ['class' => $name]
        );

        $file = BASE_PATH . "/app/Providers/{$name}.php";

        $this->writeGenerated($file, $content, 'Provider');
    }
}
