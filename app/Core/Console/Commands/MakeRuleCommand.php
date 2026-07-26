<?php

declare(strict_types=1);

namespace App\Core\Console\Commands;

use App\Core\Console\Command;

class MakeRuleCommand extends Command
{
    protected string $signature = 'make:rule';

    protected string $description = 'Create a new custom validation rule';

    public function handle(string $name): void
    {
        if (empty($name)) {
            $this->error('Usage: php zero make:rule RuleName');
            return;
        }

        if (!str_ends_with($name, 'Rule')) {
            $name .= 'Rule';
        }

        $content = $this->replace(
            $this->stub('rule.stub'),
            ['class' => $name]
        );

        $file = BASE_PATH . "/app/Core/Validation/Rules/{$name}.php";

        $this->writeGenerated($file, $content, 'Rule');
    }
}
