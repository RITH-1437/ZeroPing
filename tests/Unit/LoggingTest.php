<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Core\Logging\FileLogger;
use App\Core\Support\Config;
use PHPUnit\Framework\TestCase;

class LoggingTest extends TestCase
{
    private string $path;

    protected function setUp(): void
    {
        parent::setUp();

        $this->path = sys_get_temp_dir() . '/zp_log_' . uniqid() . '.log';
        Config::set('logging.path', $this->path);
    }

    protected function tearDown(): void
    {
        if (is_file($this->path)) {
            unlink($this->path);
        }

        parent::tearDown();
    }

    public function test_info_writes_to_file(): void
    {
        (new FileLogger())->info('hello log');

        $this->assertFileExists($this->path);
        $this->assertStringContainsString('INFO: hello log', file_get_contents($this->path));
    }

    public function test_all_levels_write(): void
    {
        $logger = new FileLogger();

        $logger->error('boom');
        $logger->debug('trace');
        $logger->warning('careful');

        $content = file_get_contents($this->path);

        $this->assertStringContainsString('ERROR: boom', $content);
        $this->assertStringContainsString('DEBUG: trace', $content);
        $this->assertStringContainsString('WARNING: careful', $content);
    }
}
