<?php

namespace App\Core\Console;

abstract class Command
{
    protected array $options = [];

    public function __construct()
    {
        $this->parseOptions();
    }

    protected function stub(string $name): string
    {
        $path = BASE_PATH . "/app/Core/Console/Stubs/{$name}";

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

    protected function info(string $message): void
    {
        echo "\033[32m{$message}\033[0m\n";
    }

    protected function error(string $message): void
    {
        echo "\033[31m{$message}\033[0m\n";
    }

    protected function option(string $key)
    {
        return $this->options[$key] ?? null;
    }

    protected function parseOptions(): void
    {
        $argv = $_SERVER['argv'];
        array_shift($argv);
        array_shift($argv);

        foreach ($argv as $arg) {
            if (strpos($arg, '--') === 0) {
                $parts = explode('=', substr($arg, 2));
                $this->options[$parts[0]] = $parts[1] ?? true;
            }
        }
    }

    protected function call(string $command, array $arguments = []): void
    {
        $argv = array_merge(['zero', $command], $arguments);
        (new \App\Core\Console())->run($argv);
    }
}
