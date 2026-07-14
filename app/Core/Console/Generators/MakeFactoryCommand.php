<?php

declare(strict_types=1);

namespace App\Core\Console\Generators;

class MakeFactoryCommand extends Generator
{
    protected string $description = 'Create a model factory';

    public function handle(string $name): void
    {
        if (empty($name)) {
            $this->warn('Usage: php zero make:factory FactoryName [--model=ModelName]');

            return;
        }

        if (!str_ends_with($name, 'Factory')) {
            $name .= 'Factory';
        }

        $model = $this->option('model');
        $modelClass = $model !== null ? (string) $model : null;

        $modelImport = $modelClass !== null ? "use App\\Models\\{$modelClass};" : '';
        $modelProp = $modelClass !== null
            ? "    protected string \$model = {$modelClass}::class;"
            : "    // protected string \$model = YourModel::class;";

        $content = $this->replace($this->stub('factory.stub'), [
            'class'        => $name,
            'model_import' => $modelImport,
            'model_prop'   => $modelProp,
        ]);

        $file = BASE_PATH . "/app/Factories/{$name}.php";

        $this->writeGenerated($file, $content, 'Factory');
    }
}
