<?php

namespace App\Core\Console\Commands;

use App\Core\Console\Command;

class MakeRepositoryCommand extends Command
{
    /**
     * The console command description.
     *
     * @var string
     */
    protected string $description = 'Create a new repository class';

    public function handle(string $name): void
    {
        if (empty($name)) {

            echo "Usage: php zero make:repository RepositoryName\n";

            return;
        }

        if (!str_ends_with($name, 'Repository')) {

            $name .= 'Repository';
        }

        $model = str_replace('Repository', '', $name);

        $content = $this->replace(

            $this->stub('repository.stub'),

            [

                'class' => $name,

                'model' => $model

            ]

        );

        $file = BASE_PATH . "/app/Repositories/{$name}.php";

        if (file_exists($file)) {

            echo "❌ Repository {$name} already exists.\n";

            return;
        }

        $this->write($file, $content);

        echo "✅ Repository created: app/Repositories/{$name}.php\n";
    }
}
