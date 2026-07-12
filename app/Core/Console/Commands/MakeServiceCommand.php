<?php

namespace App\Core\Console\Commands;

use App\Core\Console\Command;

class MakeServiceCommand extends Command
{
    /**
     * The console command description.
     *
     * @var string
     */
    protected string $description = 'Create a new service class';

    public function handle(string $name): void
    {
        if (empty($name)) {

            echo "Usage: php zero make:service ServiceName\n";

            return;
        }

        if (!str_ends_with($name, 'Service')) {

            $name .= 'Service';
        }

        $content = $this->replace(

            $this->stub('service.stub'),

            [

                'class' => $name

            ]

        );

        $file = BASE_PATH . "/app/Services/{$name}.php";

        $this->writeGenerated($file, $content, 'Service');
    }
}
