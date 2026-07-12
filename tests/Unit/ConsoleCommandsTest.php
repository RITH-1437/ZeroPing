<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Core\Console\Command;
use PHPUnit\Framework\TestCase;

class ConsoleCommandsTest extends TestCase
{
    public function testEveryCommandClassIsInstantiable(): void
    {
        $dir = dirname(__DIR__, 2) . '/app/Core/Console/Commands';

        $files = glob($dir . '/*.php');
        $this->assertNotEmpty($files, 'Expected command classes to exist.');

        foreach ($files as $file) {
            $class = 'App\\Core\\Console\\Commands\\' . basename($file, '.php');

            $this->assertTrue(
                class_exists($class),
                "Command class {$class} should exist."
            );

            $instance = new $class();

            $reflection = new \ReflectionClass($instance);

            if ($reflection->isSubclassOf(Command::class) && $reflection->hasProperty('signature')) {
                $value = $reflection->getProperty('signature')->getValue($instance);
                $this->assertIsString($value, "Signature for {$class} must be a string.");
                $this->assertNotEmpty($value, "Signature for {$class} must not be empty.");
            }
        }
    }
}
