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
            echo "Usage: php zero make:model ModelName [--all|--migration|--factory|--seeder|--controller|--resource]\n";

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

        $this->writeGenerated($file, $content, 'Model');

        $all = (bool) $this->option('all');

        $withMigration = $all || $this->option('migration') !== null;
        $withFactory = $all || $this->option('factory') !== null;
        $withSeeder = $all || $this->option('seeder') !== null;
        $withController = $all || $this->option('controller') !== null;
        $withResource = $withController && ($all || $this->option('resource') !== null);

        if ($withMigration) {
            $this->call('make:migration', ['create_' . $table . '_table']);
        }

        if ($withFactory) {
            $this->call('make:factory', [$name . 'Factory', '--model=' . $name]);
        }

        if ($withSeeder) {
            $this->call('make:seeder', [$name . 'Seeder']);
        }

        if ($withController) {
            $controllerArgs = [$name . 'Controller'];

            if ($withResource) {
                $controllerArgs[] = '--resource';
            }

            $this->call('make:controller', $controllerArgs);
        }
    }
}
