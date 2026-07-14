<?php

namespace App\Core\Testing\Console;

use App\Core\Console\Console;

trait ConsoleAssertions
{
    public function artisan(string $command, array $parameters = []): int
    {
        $argv = ['zero', $command];

        foreach ($parameters as $key => $value) {
            if (is_string($key)) {
                $argv[] = $key;
            }
            $argv[] = (string) $value;
        }

        ob_start();

        try {
            (new Console())->run($argv);
            $exitCode = 0;
        } catch (\Throwable $e) {
            echo $e->getMessage() . PHP_EOL;
            $exitCode = 1;
        }

        $this->output = ob_get_clean();
        $this->exitCode = $exitCode;

        return $exitCode;
    }

    public function assertExitCode(int $expected): void
    {
        $this->assertEquals($expected, $this->exitCode);
    }

    public function assertOutputContains(string $string): void
    {
        $this->assertStringContainsString($string, $this->output);
    }
}
