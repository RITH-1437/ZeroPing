<?php

declare(strict_types=1);

namespace Tests\Feature;

use PHPUnit\Framework\TestCase;

class QueueCommandTest extends TestCase
{
    public function testQueueTestCommandDispatchesAndCompletes(): void
    {
        $repo = dirname(__DIR__, 2);
        $cmd  = sprintf(
            'php -d opcache.enable_cli=0 %s queue:test 2>&1',
            escapeshellarg($repo . '/zero')
        );

        exec($cmd, $output, $code);

        $text = implode("\n", $output);

        $this->assertSame(0, $code);
        $this->assertStringContainsString('Queue test completed successfully', $text);
        $this->assertStringContainsString(
            'Test job handled successfully',
            (string) file_get_contents(dirname(__DIR__, 2) . '/storage/logs/zeroping.log')
        );
    }
}
