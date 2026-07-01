<?php

namespace App\Console;

use App\Console\Commands\MakeControllerCommand;
use App\Console\Commands\MakeModelCommand;
use App\Console\Commands\MigrateCommand;
use App\Console\Commands\MakeServiceCommand;
use App\Console\Commands\MakeRepositoryCommand;
use App\Console\Commands\MakeMigrationCommand;
class Console
{
    public function run(array $argv): void
    {
        $command = $argv[1] ?? null;

        switch ($command) {

            case 'migrate':
                (new MigrateCommand())->handle();
                break;

            case 'make:model':
                (new MakeModelCommand())
                    ->handle($argv[2] ?? '');
                break;

            case 'make:controller':
                (new MakeControllerCommand())
                    ->handle($argv[2] ?? '');
                break;

            case 'make:service':
                (new MakeServiceCommand())
                    ->handle($argv[2] ?? '');
                break;

            case 'make:repository':
                (new MakeRepositoryCommand())
                    ->handle($argv[2] ?? '');
                break;

            case 'make:migration':
                (new MakeMigrationCommand())
                    ->handle($argv[2] ?? '');
                break;

            default:

                echo "ZeroPing Framework\n\n";
                echo "Available commands:\n";
                echo "  migrate\n";
                echo "  make:model\n";
                echo "  make:controller\n";
                echo "  make:service\n";
                echo "  make:repository\n";
                echo "  make:migration\n";
        }
    }
}
