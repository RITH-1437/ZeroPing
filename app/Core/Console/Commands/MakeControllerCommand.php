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
            echo "Usage: php zero make:controller ControllerName\n";

            return;
        }

        if (!str_ends_with($name, 'Controller')) {
            $name .= 'Controller';
        }

        $content = $this->replace(
            $this->stub('controller.stub'),
            [

                'class' => $name

            ]
        );

        $file = BASE_PATH . "/app/Controllers/{$name}.php";

        $this->writeGenerated($file, $content, 'Controller');
    }
}
