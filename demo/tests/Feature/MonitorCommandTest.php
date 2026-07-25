<?php

declare(strict_types=1);

namespace Tests\Feature;

use PHPUnit\Framework\TestCase;

class MonitorCommandTest extends TestCase
{
    public function testMonitorReportsStatus(): void
    {
        $repo = dirname(__DIR__, 2);
        $cmd  = sprintf(
            'php -d opcache.enable_cli=0 %s monitor 2>&1',
            escapeshellarg($repo . '/zero')
        );

        exec($cmd, $output, $code);

        $text = implode("\n", $output);

        // Version banner + completion marker from MonitorCommand.
        $this->assertStringContainsString(\App\Core\Application\App::VERSION, $text);
        $this->assertStringContainsString('Monitor complete', $text);
    }
}
