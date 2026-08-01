<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Core\Console\Commands\NewCommand;
use PHPUnit\Framework\TestCase;

class StarterTemplatesTest extends TestCase
{
    public function testEveryListedTemplateHasADirectory(): void
    {
        $reflection = new \ReflectionClass(NewCommand::class);
        $constant   = $reflection->getConstant('TEMPLATES');

        $this->assertIsArray($constant, 'NewCommand::TEMPLATES should be an array.');
        $this->assertNotEmpty($constant, 'NewCommand should declare at least one template.');

        $base = dirname(__DIR__, 2) . '/templates';

        foreach (array_keys($constant) as $type) {
            $dir = $base . '/' . $type;
            $this->assertDirectoryExists($dir, "Template directory missing for '{$type}'.");
        }
    }
}
