<?php

namespace Zeroping\Support\Console;

use App\Core\Console\Command as BaseCommand;

/**
 * Base console command for packages.
 *
 * Extends the framework's App\Core\Console\Command and adds the metadata the
 * package CommandRegistry needs: a name() and structured argument access.
 *
 * The framework's Console is expected to consult CommandRegistry before its
 * hard-coded switch (see ARCHITECTURE.md, §6.1). Until then, a package command
 * can also be invoked via a thin host command that calls CommandRegistry::run().
 */
abstract class Command extends BaseCommand
{
    /**
     * The command name, e.g. "queue:work".
     */
    abstract public function name(): string;

    /**
     * Execute the command. Arguments are available via argument().
     */
    abstract public function handle(): void;

    /**
     * Get a positional argument by index (0-based, excluding the command name).
     */
    protected function argument(int $index): ?string
    {
        $args = array_slice($_SERVER['argv'] ?? [], 2);
        return $args[$index] ?? null;
    }

    /**
     * Get all positional arguments (excluding the command name).
     *
     * @return string[]
     */
    protected function arguments(): array
    {
        return array_slice($_SERVER['argv'] ?? [], 2);
    }
}
