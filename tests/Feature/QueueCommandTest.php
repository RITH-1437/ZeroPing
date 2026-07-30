<?php

declare(strict_types=1);

namespace Tests\Feature;

use PHPUnit\Framework\TestCase;

class QueueCommandTest extends TestCase
{
    /**
     * Return the path FileLogger will write to, based on config/logging.php.
     * Falls back to storage/logs/app.log — the framework default — so this
     * never breaks on a clean CI runner that has no zeroping.log yet.
     */
    private function logFile(): string
    {
        $configured = function_exists('config') ? config('logging.path') : null;
        return (is_string($configured) && $configured !== '')
            ? $configured
            : dirname(__DIR__, 2) . '/storage/logs/app.log';
    }

    public function testQueueTestCommandDispatchesAndCompletes(): void
    {
        $repo = dirname(__DIR__, 2);
        $cmd  = sprintf(
            'php -d opcache.enable_cli=0 %s queue:test 2>&1',
            escapeshellarg($repo . '/zero')
        );

        // Ensure the log directory exists before the sub-process runs so
        // FileLogger can open the file on the first write.
        $logFile = $this->logFile();
        $logDir  = dirname($logFile);
        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }

        exec($cmd, $output, $code);

        $text = implode("\n", $output);

        $this->assertSame(0, $code);
        $this->assertStringContainsString('Queue test completed successfully', $text);

        $logContent = file_get_contents($logFile);
        $this->assertIsString($logContent, "Log file not readable: {$logFile}");
        $this->assertStringContainsString('Test job handled successfully', $logContent);
    }
}
