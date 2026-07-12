<?php

namespace App\Core\Console\Commands;

use App\Core\Console\Command;

class MakeMigrationCommand extends Command
{
    /**
     * The console command description.
     *
     * @var string
     */
    protected string $description = 'Create a new migration file';

    public function handle(string $name): void
    {
        if (empty($name)) {
            $this->warn("Usage: php zero make:migration MigrationName");

            return;
        }

        $directory = BASE_PATH . '/database/migrations';

        if (!is_dir($directory)) {
            mkdir($directory, 0777, true);
        }

        $timestamp = date('Y_m_d_His');

        $filename = "{$timestamp}_{$this->snake($name)}.php";

        $file = $directory . '/' . $filename;

        $table = $this->guessTable($name);

        $content = <<<PHP
<?php

use App\Core\Database\Migration;
use App\Core\Database\Schema;
use App\Core\Database\Blueprint;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('{$table}', function (Blueprint \$table) {

            \$table->id();

            \$table->timestamps();

        });
    }

    public function down(): void
    {
        Schema::drop('{$table}');
    }
};

PHP;

        $this->writeGenerated($file, $content, 'Migration');
    }

    private function snake(string $value): string
    {
        return strtolower(
            preg_replace('/(?<!^)[A-Z]/', '_$0', $value)
        );
    }

    private function guessTable(string $name): string
    {
        if (preg_match('/Create(.+)Table/i', $name, $matches)) {
            return strtolower($matches[1]);
        }

        return strtolower($name);
    }
}
