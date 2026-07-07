<?php

namespace App\Core\Testing\Console;

trait ConsoleAssertions
{
    public function artisan(string $command, array $parameters = []): int
    {
        // This is a simplified implementation. A real implementation would
        // run the command and capture the output.
        return 0;
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
