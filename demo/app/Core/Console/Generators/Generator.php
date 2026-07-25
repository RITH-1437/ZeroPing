<?php

declare(strict_types=1);

namespace App\Core\Console\Generators;

use App\Core\Console\Command;

/**
 * Shared base for the make:* generators.
 *
 * Wraps the file-generation primitives provided by {@see Command}
 * (stub / replace / writeGenerated / option) so individual
 * generators stay tiny and consistent.
 */
abstract class Generator extends Command
{
    /**
     * Render a stub and write it with the standard overwrite guard.
     */
    protected function generate(string $stubName, array $replace, string $file, string $label): bool
    {
        $content = $this->replace($this->stub($stubName), $replace);

        return $this->writeGenerated($file, $content, $label);
    }

    /**
     * Derive a plural table name from a model class.
     */
    protected function tableName(string $model): string
    {
        $base = preg_replace('/Model$/', '', $model);

        return strtolower($base) . 's';
    }

    /**
     * Ensure a class name ends with the expected suffix.
     */
    protected function ensureSuffix(string $name, string $suffix): string
    {
        return str_ends_with($name, $suffix) ? $name : $name . $suffix;
    }
}
