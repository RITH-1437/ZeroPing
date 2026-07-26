<?php

namespace App\Core\Console\Commands;

use App\Core\Console\Command;

class MakeMigrationCommand extends Command
{
    protected string $signature = 'make:migration';

    protected string $description = 'Create a new migration file';

    public function handle(string $name): void
    {
        if (empty($name)) {
            $this->warn("Usage: php zero make:migration MigrationName");

            return;
        }

        $directory = BASE_PATH . '/database/migrations';

        if (!is_dir($directory)) {
            mkdir($directory, 0777, true);
        }

        $timestamp = date('Y_m_d_His');

        $filename = "{$timestamp}_{$this->snake($name)}.php";

        $file = $directory . '/' . $filename;

        $table = $this->guessTable($name);

        $content = $this->replace(
            $this->stub('migration.stub'),
            ['table' => $table]
        );

        $this->writeGenerated($file, $content, 'Migration');
    }

    private function snake(string $value): string
    {
        return strtolower(
            preg_replace('/(?<!^)[A-Z]/', '_$0', $value)
        );
    }

    private function guessTable(string $name): string
    {
        if (preg_match('/^create_(.+?)_table$/i', $name, $matches)) {
            return $matches[1];
        }

        if (preg_match('/Create(.+)Table/i', $name, $matches)) {
            return strtolower($matches[1]);
        }

        return strtolower($name);
    }
}
