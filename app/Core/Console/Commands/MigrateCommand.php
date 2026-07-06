<?php

namespace App\Core\Console\Commands;

use App\Core\Database\MigrationRunner;
use PDOException;
use Throwable;

class MigrateCommand
{
    public function handle(): void
    {
        echo "========================================\n";
        echo " ZeroPing Framework Migration\n";
        echo "========================================\n\n";

        try {

            $runner = new MigrationRunner();

            $runner->run();

            echo "\n🎉 Migration completed successfully.\n";

        } catch (PDOException $e) {

            echo "\n❌ Database Error\n";
            echo $e->getMessage() . PHP_EOL;

        } catch (Throwable $e) {

            echo "\n❌ Error\n";
            echo $e->getMessage() . PHP_EOL;
        }
    }
}