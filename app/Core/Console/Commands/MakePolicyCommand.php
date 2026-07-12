<?php

namespace App\Core\Console\Commands;

use App\Core\Console\Command;

class MakePolicyCommand extends Command
{
    protected string $signature = 'make:policy';

    protected string $description = 'Create a new policy class';

    public function handle(string $name): void
    {
        if (empty($name)) {
            $this->error('Usage: php zero make:policy PolicyName');
            return;
        }

        if (!str_ends_with($name, 'Policy')) {
            $name .= 'Policy';
        }

        $content = $this->replace(
            $this->stub('policy.stub'),
            ['class' => $name]
        );

        $file = BASE_PATH . "/app/Policies/{$name}.php";

        $this->writeGenerated($file, $content, 'Policy');
    }
}
