<?php

namespace App\Core;

use App\Core\Console\Commands\CacheClearCommand;
use App\Core\Console\Commands\CacheTestCommand;
use App\Core\Console\Commands\ConfigCacheCommand;
use App\Core\Console\Commands\ConfigClearCommand;
use App\Core\Console\Commands\ConfigTestCommand;
use App\Core\Console\Commands\KeyGenerateCommand;
use App\Core\Console\Commands\LogTestCommand;
use App\Core\Console\Commands\MailTestCommand;
use App\Core\Console\Commands\MakeControllerCommand;
use App\Core\Console\Commands\MakeMailCommand;
use App\Core\Console\Commands\MakeMigrationCommand;
use App\Core\Console\Commands\MakeModelCommand;
use App\Core\Console\Commands\MakeRepositoryCommand;
use App\Core\Console\Commands\MakeServiceCommand;
use App\Core\Console\Commands\MigrateCommand;
use App\Core\Console\Commands\MigrateFreshCommand;
use App\Core\Console\Commands\MigrateRollbackCommand;
use App\Core\Console\Commands\MigrateStatusCommand;
use App\Core\Console\Commands\OptimizeClearCommand;
use App\Core\Console\Commands\OptimizeCommand;
use App\Core\Console\Commands\OrmTestCommand;
use App\Core\Console\Commands\QueueClearCommand;
use App\Core\Console\Commands\QueueFailedCommand;
use App\Core\Console\Commands\QueueListenCommand;
use App\Core\Console\Commands\QueueRestartCommand;
use App\Core\Console\Commands\QueueRetryCommand;
use App\Core\Console\Commands\QueueTestCommand;
use App\Core\Console\Commands\QueueWorkCommand;
use App\Core\Console\Commands\RouteCacheCommand;
use App\Core\Console\Commands\RouteClearCommand;
use App\Core\Console\Commands\RouteListCommand;
use App\Core\Console\Commands\ScheduleClearCommand;
use App\Core\Console\Commands\ScheduleListCommand;
use App\Core\Console\Commands\ScheduleRunCommand;
use App\Core\Console\Commands\ScheduleTestCommand;
use App\Core\Console\Commands\SecurityTestCommand;
use App\Core\Console\Commands\ServeCommand;
use App\Core\Console\Commands\StorageClearCommand;
use App\Core\Console\Commands\StorageTestCommand;
use App\Core\Console\Commands\TestCommand;
use App\Core\Console\Commands\ValidateTestCommand;
use App\Core\Console\Commands\ViewCacheCommand;
use App\Core\Console\Commands\ViewClearCommand;

class Console
{
    protected array $commands = [
        'serve' => ServeCommand::class,
        'migrate' => MigrateCommand::class,
        'migrate:status' => MigrateStatusCommand::class,
        'migrate:rollback' => MigrateRollbackCommand::class,
        'migrate:fresh' => MigrateFreshCommand::class,
        'make:model' => MakeModelCommand::class,
        'make:controller' => MakeControllerCommand::class,
        'make:service' => MakeServiceCommand::class,
        'make:repository' => MakeRepositoryCommand::class,
        'make:migration' => MakeMigrationCommand::class,
        'make:mail' => MakeMailCommand::class,
        'route:list' => RouteListCommand::class,
        'orm:test' => OrmTestCommand::class,
        'config:test' => ConfigTestCommand::class,
        'validate:test' => ValidateTestCommand::class,
        'log:test' => LogTestCommand::class,
        'cache:test' => CacheTestCommand::class,
        'cache:clear' => CacheClearCommand::class,
        'storage:test' => StorageTestCommand::class,
        'storage:clear' => StorageClearCommand::class,
        'mail:test' => MailTestCommand::class,
        'queue:work' => QueueWorkCommand::class,
        'queue:listen' => QueueListenCommand::class,
        'queue:restart' => QueueRestartCommand::class,
        'queue:failed' => QueueFailedCommand::class,
        'queue:retry' => QueueRetryCommand::class,
        'queue:clear' => QueueClearCommand::class,
        'queue:test' => QueueTestCommand::class,
        'schedule:run' => ScheduleRunCommand::class,
        'schedule:list' => ScheduleListCommand::class,
        'schedule:test' => ScheduleTestCommand::class,
        'schedule:clear' => ScheduleClearCommand::class,
        'security:test' => SecurityTestCommand::class,
        'key:generate' => KeyGenerateCommand::class,
        'test' => TestCommand::class,
        'optimize' => OptimizeCommand::class,
        'optimize:clear' => OptimizeClearCommand::class,
        'config:cache' => ConfigCacheCommand::class,
        'config:clear' => ConfigClearCommand::class,
        'route:cache' => RouteCacheCommand::class,
        'route:clear' => RouteClearCommand::class,
        'view:cache' => ViewCacheCommand::class,
        'view:clear' => ViewClearCommand::class,
    ];

    public function run(array $argv): void
    {
        $command = $argv[1] ?? null;

        if (!$command) {
            $this->showHelp();
            return;
        }

        if (!isset($this->commands[$command])) {
            echo "Command not found: {$command}\n";
            return;
        }

        $class = $this->commands[$command];
        (new $class())->handle($argv[2] ?? '');
    }

    protected function showHelp(): void
    {
        echo "\033[32m";
        echo "   ____                           __    ____\n";
        echo "  / __ \____  _  ____  _  ____  / /_  / __ \____ ____  ____\n";
        echo " / / / / __ \| |/_/ / / / __ \/ __ \/ /_/ / __ `/ _ \/ __ \\\n";
        echo "/ /_/ / /_/ />  </ /_/ / / / / / / / ____/ /_/ /  __/ / / /\n";
        echo "\____/ .___/_/|_|\__,_/_/ /_/_/ /_/_/    \__, /\___/_/ /_/\n";
        echo "    /_/                               /____/\n";
        echo "\033[0m";
        echo "\n";
        echo "ZeroPing Framework \033[33m(v1.0.0)\033[0m\n\n";
        echo "\033[33mUsage:\033[0m\n";
        echo "  command [options] [arguments]\n\n";
        echo "\033[33mAvailable commands:\033[0m\n";

        foreach ($this->commands as $name => $class) {
            $description = (new $class())->description ?? '';
            printf("  \033[32m%-20s\033[0m %s\n", $name, $description);
        }
    }
}
