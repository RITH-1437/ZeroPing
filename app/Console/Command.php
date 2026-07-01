<?php

namespace App\Console;

abstract class Command
{
    protected function stub(string $name): string
    {
        $path = BASE_PATH . "/app/Console/Stubs/{$name}";

        if (!file_exists($path)) {
            throw new \RuntimeException("Stub {$name} not found.");
        }

        return file_get_contents($path);
    }

    protected function replace(
        string $stub,
        array $replace
    ): string {

        foreach ($replace as $search => $value) {

            $stub = str_replace(
                '{{ '.$search.' }}',
                $value,
                $stub
            );
        }

        return $stub;
    }

    protected function write(
        string $file,
        string $content
    ): void {

        $directory = dirname($file);

        if (!is_dir($directory)) {

            mkdir($directory, 0777, true);
        }

        file_put_contents(
            $file,
            $content
        );
    }
}