<?php

namespace App\Core\Console\Commands;

use App\Core\Console\Command;

class MakeControllerCommand extends Command
{
    /**
     * The console command description.
     *
     * @var string
     */
    protected string $description = 'Create a new controller class';

    public function handle(string $name): void
    {
        if (empty($name)) {
            echo "Usage: php zero make:controller ControllerName [--resource]\n";

            return;
        }

        if (!str_ends_with($name, 'Controller')) {
            $name .= 'Controller';
        }

        $stub = $this->option('resource') ? 'controller.resource.stub' : 'controller.stub';

        $content = $this->replace(
            $this->stub($stub),
            [

                'class' => $name

            ]
        );

        $file = BASE_PATH . "/app/Controllers/{$name}.php";

        $this->writeGenerated($file, $content, 'Controller');
    }
}
