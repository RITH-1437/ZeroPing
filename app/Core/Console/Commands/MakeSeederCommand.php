<?php

namespace App\Core\Console\Commands;

use App\Core\Console\Command;

class MakeSeederCommand extends Command
{
    protected string $signature = 'make:seeder';
    protected string $description = 'Create a new seeder class';

    public function handle(string $name): void
    {
        if (empty($name)) {
            echo "Usage: php zero make:seeder SeederName\n";
            return;
        }

        if (!str_ends_with($name, 'Seeder')) {
            $name .= 'Seeder';
        }

        $content = $this->replace(
            $this->stub('seeder.stub'),
            ['class' => $name]
        );

        $dir = BASE_PATH . '/database/seeders';
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }

        $file = $dir . "/{$name}.php";

        $this->writeGenerated($file, $content, 'Seeder');
    }
}
