<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Core\Debug\ExceptionHandler;
use App\Core\Support\Config;
use PHPUnit\Framework\TestCase;

class ExceptionHandlerTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SERVER['REQUEST_URI'] = '/';
        $_SERVER['HTTP_HOST'] = 'localhost';
    }

    public function test_debug_renders_pretty_exception(): void
    {
        Config::set('app.debug', true);

        ob_start();
        (new ExceptionHandler())->handle(new \RuntimeException('boom'));
        $output = (string) ob_get_clean();

        $this->assertStringContainsString('RuntimeException', $output);
        $this->assertStringContainsString('boom', $output);
    }

    public function test_production_renders_generic_page(): void
    {
        Config::set('app.debug', false);

        ob_start();
        (new ExceptionHandler())->handle(new \RuntimeException('boom'));
        $output = (string) ob_get_clean();

        $this->assertStringContainsString('Something Went Wrong', $output);
        $this->assertStringNotContainsString('boom', $output);
    }
}
