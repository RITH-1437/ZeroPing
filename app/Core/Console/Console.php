<?php

namespace App\Core\Console;

use App\Core\Console\Commands\MakeControllerCommand;
use App\Core\Console\Commands\MakeMigrationCommand;
use App\Core\Console\Commands\MakeModelCommand;
use App\Core\Console\Commands\MakeRepositoryCommand;
use App\Core\Console\Commands\MakeServiceCommand;
use App\Core\Console\Commands\MigrateCommand;
use App\Core\Console\Commands\RouteListCommand;
use App\Core\Console\Commands\ConfigTestCommand;
use App\Core\Console\Commands\LogTestCommand;
use App\Core\Console\Commands\ValidateTestCommand;

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

            case 'route:list':
                (new RouteListCommand())->handle();
                break;

            case 'log:test':
                (new LogTestCommand())->handle();
                break;

            case 'config:test':
                (new ConfigTestCommand())->handle();
                break;

            case 'validate:test':
                (new ValidateTestCommand())->handle();
                break;

            default:

                echo "ZeroPing Framework\n\n";
                echo "Available commands:\n";
                echo "-  migrate\n";
                echo "-  make:model\n";
                echo "-  make:controller\n";
                echo "-  make:service\n";
                echo "-  make:repository\n";
                echo "-  make:migration\n";
                echo "-  route:list\n";
                echo "-  log:test\n";
                echo "-  config:test\n";
                echo "-  validate:test\n";
        }
    }
}