<?php

namespace App\Core\Console\Commands;

use App\Core\Console\Command;

class MakeCommandCommand extends Command
{
    protected string $signature = 'make:command';

    protected string $description = 'Create a new console command';

    public function handle(string $name): void
    {
        if (empty($name) || str_starts_with($name, '-')) {
            $this->error('Usage: php zero make:command CommandName');
            return;
        }

        if (!str_ends_with($name, 'Command')) {
            $name .= 'Command';
        }

        $signature = strtolower(preg_replace('/(?<!^)[A-Z]/', '-$0', str_replace('Command', '', $name)));

        $content = $this->replace(
            $this->stub('command.stub'),
            [
                'class'       => $name,
                'signature'   => $signature,
                'description' => 'The ' . $signature . ' command',
            ]
        );

        $file = BASE_PATH . "/app/Core/Console/Commands/{$name}.php";

        $this->writeGenerated($file, $content, 'Command');
    }
}
