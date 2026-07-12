<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Core\Console\Commands\NewCommand;
use PHPUnit\Framework\TestCase;

class StarterTemplatesTest extends TestCase
{
    public function testEveryListedTemplateHasADirectory(): void
    {
        $command = new NewCommand();

        $reflection = new \ReflectionClass($command);
        $property = $reflection->getProperty('templates');

        $templates = $property->getValue($command);

        $this->assertNotEmpty($templates, 'NewCommand should declare at least one template.');

        $base = dirname(__DIR__, 2) . '/app/Core/Console/Templates';

        foreach (array_keys($templates) as $type) {
            $dir = $base . '/' . $type;
            $this->assertDirectoryExists($dir, "Template directory missing for '{$type}'.");
        }
    }
}
