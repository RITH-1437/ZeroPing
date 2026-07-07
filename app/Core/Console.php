<?php

namespace App\Core;

use App\Core\Console\Commands\ConfigTestCommand;
use App\Core\Console\Commands\LogTestCommand;
use App\Core\Console\Commands\MakeControllerCommand;
use App\Core\Console\Commands\MakeMigrationCommand;
use App\Core\Console\Commands\MakeModelCommand;
use App\Core\Console\Commands\MakeRepositoryCommand;
use App\Core\Console\Commands\MakeServiceCommand;
use App\Core\Console\Commands\MigrateCommand;
use App\Core\Console\Commands\MigrateFreshCommand;
use App\Core\Console\Commands\MigrateRollbackCommand;
use App\Core\Console\Commands\MigrateStatusCommand;
use App\Core\Console\Commands\OrmTestCommand;
use App\Core\Console\Commands\RouteListCommand;
use App\Core\Console\Commands\ServeCommand;
use App\Core\Console\Commands\ValidateTestCommand;

class Console
{
    public function run(array $argv): void
    {
        $command = $argv[1] ?? null;

        switch ($command) {

            case 'serve':
                (new ServeCommand())->handle();
                break;

            case 'migrate':
                (new MigrateCommand())->handle();
                break;

            case 'migrate:status':
                (new MigrateStatusCommand())->handle();
                break;

            case 'migrate:rollback':
                (new MigrateRollbackCommand())->handle();
                break;

            case 'migrate:fresh':
                (new MigrateFreshCommand())->handle();
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

            case 'orm:test':
                (new OrmTestCommand())->handle();
                break;

            case 'config:test':
                (new ConfigTestCommand())->handle();
                break;

            case 'validate:test':
                (new ValidateTestCommand())->handle();
                break;

            case 'log:test':
                (new LogTestCommand())->handle();
                break;

            default:

                echo "ZeroPing Framework\n\n";
                echo "Available commands:\n";
                echo "  serve\n";
                echo "  migrate\n";
                echo "  migrate:status\n";
                echo "  migrate:rollback\n";
                echo "  migrate:fresh\n";
                echo "  make:model\n";
                echo "  make:controller\n";
                echo "  make:service\n";
                echo "  make:repository\n";
                echo "  make:migration\n";
                echo "  route:list\n";
                echo "  orm:test\n";
                echo "  config:test\n";
                echo "  validate:test\n";
                echo "  log:test\n";
        }
    }
}
