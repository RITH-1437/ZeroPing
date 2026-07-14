<?php

declare(strict_types=1);

namespace App\Core\Console\Generators;

class MakeEnumCommand extends Generator
{
    protected string $description = 'Create a new backed enum';

    public function handle(string $name): void
    {
        if (empty($name)) {
            $this->warn('Usage: php zero make:enum EnumName');

            return;
        }

        $content = $this->replace($this->stub('enum.stub'), [
            'class' => $name,
        ]);

        $file = BASE_PATH . "/app/Enums/{$name}.php";

        $this->writeGenerated($file, $content, 'Enum');
    }
}
