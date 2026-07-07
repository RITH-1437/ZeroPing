<?php

namespace App\Core\Console\Commands;

use App\Core\Console\Command;

class MakeModelCommand extends Command
{
    /**
     * The console command description.
     *
     * @var string
     */
    protected string $description = 'Create a new Eloquent model class';

    public function handle(string $name): void
    {
        if (empty($name)) {

            echo "Usage: php zero make:model ModelName\n";

            return;
        }

        $table = strtolower($name) . 's';

        $content = $this->replace(

            $this->stub('model.stub'),

            [

                'class' => $name,

                'table' => $table

            ]

        );

        $file = BASE_PATH . "/app/Models/{$name}.php";

        if (file_exists($file)) {

            echo "❌ Model {$name} already exists.\n";

            return;
        }

        $this->write($file, $content);

        echo "✅ Model created: app/Models/{$name}.php\n";
    }
}
