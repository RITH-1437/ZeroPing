<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Core\Container\Container;
use App\Core\Debug\DebugBar;
use App\Providers\DebugBarServiceProvider;
use PHPUnit\Framework\TestCase;

class DebugBarTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SERVER['REQUEST_URI'] = '/';
        $_SERVER['HTTP_HOST'] = 'localhost';
    }

    public function test_render_includes_all_collectors(): void
    {
        $html = (new DebugBar())->render();

        $this->assertStringContainsString('zeroping-debugbar', $html);
        $this->assertStringContainsString('Time:', $html);
        $this->assertStringContainsString('SQL Queries:', $html);
        $this->assertStringContainsString('Memory:', $html);
        $this->assertStringContainsString('Route:', $html);
        $this->assertStringContainsString('Config files:', $html);
    }

    public function test_add_custom_collector(): void
    {
        $bar = new DebugBar();

        $bar->addCollector(new class implements \App\Core\Debug\Collector {
            public function getName(): string
            {
                return 'custom';
            }

            public function render(): string
            {
                return '<span>Custom: ok</span>';
            }
        });

        $this->assertStringContainsString('Custom: ok', $bar->render());
    }

    public function test_provider_binds_debugbar_singleton(): void
    {
        $container = new Container();

        (new DebugBarServiceProvider($container))->register();

        $this->assertInstanceOf(DebugBar::class, $container->make(DebugBar::class));
        $this->assertSame(
            $container->make(DebugBar::class),
            $container->make(DebugBar::class)
        );
    }
}
