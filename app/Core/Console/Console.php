<?php

namespace App\Core\Console;

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
use App\Core\Console\Commands\MigrateRefreshCommand;
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
use App\Core\Console\Commands\SearchIndexCommand;
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
    /**
     * Parse CLI arguments and dispatch the appropriate command handler.
     *
     * @param array $argv The CLI argument vector ($_SERVER['argv']).
     */
    public function run(array $argv): void
    {
        $command = $argv[1] ?? null;

        switch ($command) {

            case 'version':
                echo "ZeroPing Framework v1.0.0\n";
                break;

            case 'about':
                $style = new ConsoleStyle();
                $style->writeln("");
                $style->writeln("<fg=cyan>███████╗███████╗██████╗  ██████╗ ██████╗ ██╗███╗   ██╗ ██████╗</>");
                $style->writeln("<fg=cyan>╚══███╔╝██╔════╝██╔══██╗██╔═══██╗██╔══██╗██║████╗  ██║██╔════╝</>");
                $style->writeln("<fg=cyan>  ███╔╝ █████╗  ██████╔╝██║   ██║██████╔╝██║██╔██╗ ██║██║  ███╗</>");
                $style->writeln("<fg=cyan> ███╔╝  ██╔══╝  ██╔══██╗██║   ██║██╔═══╝ ██║██║╚██╗██║██║   ██║</>");
                $style->writeln("<fg=cyan>███████╗███████╗██║  ██║╚██████╔╝██║     ██║██║ ╚████║╚██████╔╝</>");
                $style->writeln("<fg=cyan>╚══════╝╚══════╝╚═╝  ╚═╝ ╚═════╝ ╚═╝     ╚═╝╚═╝  ╚═══╝ ╚═════╝</>");
                $style->writeln("");
                $style->writeln("<fg=green>ZeroPing Framework</> <fg=yellow>v1.0.0</>");
                $style->writeln("<fg=gray>Lightweight PHP Framework built from scratch.</>");
                $style->writeln("");
                $style->writeln("<fg=yellow>About</>");
                $style->writeln("<fg=gray>──────────────────────────────────────────────────────────────</>");
                $style->writeln("  <fg=white>Author    </> <fg=cyan>Nairith RIN</>");
                $style->writeln("  <fg=white>Email     </> <fg=cyan>nairithrinn143@gmail.com</>");
                $style->writeln("  <fg=white>GitHub    </> <fg=cyan>https://github.com/RITH-1437</>");
                $style->writeln("  <fg=white>Repository</> <fg=cyan>https://github.com/RITH-1437/ZeroPing</>");
                $style->writeln("  <fg=white>License   </> <fg=cyan>MIT</>");
                $style->writeln("  <fg=white>PHP       </> <fg=cyan>>= 8.1</>");
                $style->writeln("");
                $style->writeln("<fg=yellow>Documentation</>");
                $style->writeln("  <fg=gray>Coming Soon</>");
                $style->writeln("");
                break;

            // ── Server ──────────────────────────────────────────────────────
            case 'serve':
                (new ServeCommand())->handle(array_slice($argv, 2));
                break;

            // ── Migrations ──────────────────────────────────────────────────
            case 'migrate':
                (new MigrateCommand())->handle();
                break;

            case 'migrate:refresh':
                (new MigrateRefreshCommand())->handle();
                break;

            case 'migrate:fresh':
                (new MigrateFreshCommand())->handle();
                break;

            case 'migrate:rollback':
                (new MigrateRollbackCommand())->handle();
                break;

            case 'migrate:status':
                (new MigrateStatusCommand())->handle();
                break;

            // ── Make ────────────────────────────────────────────────────────
            case 'make:model':
                (new MakeModelCommand())->handle($argv[2] ?? '');
                break;

            case 'make:controller':
                (new MakeControllerCommand())->handle($argv[2] ?? '');
                break;

            case 'make:service':
                (new MakeServiceCommand())->handle($argv[2] ?? '');
                break;

            case 'make:repository':
                (new MakeRepositoryCommand())->handle($argv[2] ?? '');
                break;

            case 'make:migration':
                (new MakeMigrationCommand())->handle($argv[2] ?? '');
                break;

            case 'make:mail':
                (new MakeMailCommand())->handle($argv[2] ?? '');
                break;

            // ── Routes ──────────────────────────────────────────────────────
            case 'route:list':
                (new RouteListCommand())->handle();
                break;

            case 'route:cache':
                (new RouteCacheCommand())->handle();
                break;

            case 'route:clear':
                (new RouteClearCommand())->handle();
                break;

            // ── Config ──────────────────────────────────────────────────────
            case 'config:test':
                (new ConfigTestCommand())->handle();
                break;

            case 'config:cache':
                (new ConfigCacheCommand())->handle();
                break;

            case 'config:clear':
                (new ConfigClearCommand())->handle();
                break;

            // ── Cache ───────────────────────────────────────────────────────
            case 'cache:test':
                (new CacheTestCommand())->handle();
                break;

            case 'cache:clear':
                (new CacheClearCommand())->handle();
                break;

            // ── Queue ───────────────────────────────────────────────────────
            case 'queue:test':
                (new QueueTestCommand())->handle();
                break;

            case 'queue:work':
                (new QueueWorkCommand())->handle();
                break;

            case 'queue:listen':
                (new QueueListenCommand())->handle();
                break;

            case 'queue:failed':
                (new QueueFailedCommand())->handle();
                break;

            case 'queue:retry':
                (new QueueRetryCommand())->handle($argv[2] ?? '');
                break;

            case 'queue:clear':
                (new QueueClearCommand())->handle();
                break;

            case 'queue:restart':
                (new QueueRestartCommand())->handle();
                break;

            // ── Schedule ────────────────────────────────────────────────────
            case 'schedule:run':
                (new ScheduleRunCommand())->handle();
                break;

            case 'schedule:list':
                (new ScheduleListCommand())->handle();
                break;

            case 'schedule:test':
                (new ScheduleTestCommand())->handle();
                break;

            case 'schedule:clear':
                (new ScheduleClearCommand())->handle();
                break;

            // ── Storage ─────────────────────────────────────────────────────
            case 'storage:test':
                (new StorageTestCommand())->handle();
                break;

            case 'storage:clear':
                (new StorageClearCommand())->handle();
                break;

            // ── Views ───────────────────────────────────────────────────────
            case 'view:cache':
                (new ViewCacheCommand())->handle();
                break;

            case 'view:clear':
                (new ViewClearCommand())->handle();
                break;

            // ── Optimize ────────────────────────────────────────────────────
            case 'optimize':
                (new OptimizeCommand())->handle();
                break;

            case 'optimize:clear':
                (new OptimizeClearCommand())->handle();
                break;

            // ── Keys / Security ─────────────────────────────────────────────
            case 'key:generate':
                (new KeyGenerateCommand())->handle();
                break;

            case 'security:test':
                (new SecurityTestCommand())->handle();
                break;

            // ── Search ──────────────────────────────────────────────────────
            case 'search:index':
                (new SearchIndexCommand())->handle();
                break;

            // ── Starter Templates ─────────────────────────────────────────
            case 'new':
                $command = new \App\Core\Console\Commands\NewCommand();
                $command->handle($argv[2] ?? '', array_slice($argv, 3));
                break;

            // ── Tests / Misc ─────────────────────────────────────────────────
            case 'orm:test':
                (new OrmTestCommand())->handle();
                break;

            case 'mail:test':
                (new MailTestCommand())->handle();
                break;

            case 'log:test':
                (new LogTestCommand())->handle();
                break;

            case 'validate:test':
                (new ValidateTestCommand())->handle();
                break;

            case 'test':
                (new TestCommand())->handle();
                break;

            default:

                $style = new ConsoleStyle();

                $style->writeln("<fg=cyan>███████╗███████╗██████╗  ██████╗ ██████╗ ██╗███╗   ██╗ ██████╗</>");
                $style->writeln("<fg=cyan>╚══███╔╝██╔════╝██╔══██╗██╔═══██╗██╔══██╗██║████╗  ██║██╔════╝</>");
                $style->writeln("<fg=cyan>  ███╔╝ █████╗  ██████╔╝██║   ██║██████╔╝██║██╔██╗ ██║██║  ███╗</>");
                $style->writeln("<fg=cyan> ███╔╝  ██╔══╝  ██╔══██╗██║   ██║██╔═══╝ ██║██║╚██╗██║██║   ██║</>");
                $style->writeln("<fg=cyan>███████╗███████╗██║  ██║╚██████╔╝██║     ██║██║ ╚████║╚██████╔╝</>");
                $style->writeln("<fg=cyan>╚══════╝╚══════╝╚═╝  ╚═╝ ╚═════╝ ╚═╝     ╚═╝╚═╝  ╚═══╝ ╚═════╝</>");
                $style->writeln("");

                $style->writeln("<fg=green>ZeroPing Framework</> <fg=yellow>v1.0.0</>");
                $style->writeln("<fg=gray>Lightweight PHP Framework built from scratch.</>");
                $style->writeln("");

                $style->writeln("<fg=yellow>Usage:</>");
                $style->writeln("  <fg=white>php zero &lt;command&gt; [options]</>");
                $style->writeln("");

                $style->writeln("<fg=yellow>Available Commands</>");
                $style->writeln("<fg=gray>──────────────────────────────────────────────────────────────</>");

                $style->writeln("  <fg=green>serve</>                 <fg=gray>Start development server</>");
                $style->writeln("  <fg=green>about</>                 <fg=gray>Display framework information</>");
                $style->writeln("  <fg=green>new</>                   <fg=gray>Scaffold a new project from a template</>");
                $style->writeln("");

                $style->writeln("  <fg=yellow>Migrations</>");
                $style->writeln("  <fg=green>migrate</>               <fg=gray>Run database migrations</>");
                $style->writeln("  <fg=green>migrate:fresh</>         <fg=gray>Drop all tables and re-run migrations</>");
                $style->writeln("  <fg=green>migrate:refresh</>       <fg=gray>Rollback all migrations then re-run them</>");
                $style->writeln("  <fg=green>migrate:rollback</>      <fg=gray>Rollback the last migration</>");
                $style->writeln("  <fg=green>migrate:status</>        <fg=gray>Show migration status</>");
                $style->writeln("");

                $style->writeln("  <fg=yellow>Make</>");
                $style->writeln("  <fg=green>make:model</>            <fg=gray>Create a model</>");
                $style->writeln("  <fg=green>make:controller</>       <fg=gray>Create a controller</>");
                $style->writeln("  <fg=green>make:service</>          <fg=gray>Create a service</>");
                $style->writeln("  <fg=green>make:repository</>       <fg=gray>Create a repository</>");
                $style->writeln("  <fg=green>make:migration</>        <fg=gray>Create a migration</>");
                $style->writeln("  <fg=green>make:mail</>             <fg=gray>Create a mailable</>");
                $style->writeln("");

                $style->writeln("  <fg=yellow>Routes</>");
                $style->writeln("  <fg=green>route:list</>            <fg=gray>Display all routes</>");
                $style->writeln("  <fg=green>route:cache</>           <fg=gray>Cache the routes</>");
                $style->writeln("  <fg=green>route:clear</>           <fg=gray>Clear the route cache</>");
                $style->writeln("");

                $style->writeln("  <fg=yellow>Config</>");
                $style->writeln("  <fg=green>config:cache</>          <fg=gray>Cache the config</>");
                $style->writeln("  <fg=green>config:clear</>          <fg=gray>Clear the config cache</>");
                $style->writeln("");

                $style->writeln("  <fg=yellow>Cache</>");
                $style->writeln("  <fg=green>cache:clear</>           <fg=gray>Flush the application cache</>");
                $style->writeln("");

                $style->writeln("  <fg=yellow>Queue</>");
                $style->writeln("  <fg=green>queue:work</>            <fg=gray>Process jobs on the queue</>");
                $style->writeln("  <fg=green>queue:listen</>          <fg=gray>Listen to a queue</>");
                $style->writeln("  <fg=green>queue:failed</>          <fg=gray>List failed queue jobs</>");
                $style->writeln("  <fg=green>queue:retry</>           <fg=gray>Retry a failed queue job</>");
                $style->writeln("  <fg=green>queue:clear</>           <fg=gray>Delete all jobs from the queue</>");
                $style->writeln("  <fg=green>queue:restart</>         <fg=gray>Restart queue workers</>");
                $style->writeln("");

                $style->writeln("  <fg=yellow>Schedule</>");
                $style->writeln("  <fg=green>schedule:run</>          <fg=gray>Run scheduled commands</>");
                $style->writeln("  <fg=green>schedule:list</>         <fg=gray>List scheduled commands</>");
                $style->writeln("  <fg=green>schedule:clear</>        <fg=gray>Clear scheduled commands</>");
                $style->writeln("");

                $style->writeln("  <fg=yellow>Storage & Views</>");
                $style->writeln("  <fg=green>storage:clear</>         <fg=gray>Clear storage files</>");
                $style->writeln("  <fg=green>view:cache</>            <fg=gray>Compile all Blade templates</>");
                $style->writeln("  <fg=green>view:clear</>            <fg=gray>Clear compiled view files</>");
                $style->writeln("");

                $style->writeln("  <fg=yellow>Optimize</>");
                $style->writeln("  <fg=green>optimize</>              <fg=gray>Cache config, routes and views</>");
                $style->writeln("  <fg=green>optimize:clear</>        <fg=gray>Clear all cached data</>");
                $style->writeln("");

                $style->writeln("  <fg=yellow>Security & Keys</>");
                $style->writeln("  <fg=green>key:generate</>          <fg=gray>Set the application key</>");
                $style->writeln("");
                $style->writeln("  <fg=yellow>Search</>");
                $style->writeln("  <fg=green>search:index</>          <fg=gray>Build documentation search index</>");
                $style->writeln("");

                $style->writeln("  <fg=yellow>Testing & Diagnostics</>");
                $style->writeln("  <fg=green>test</>                  <fg=gray>Run framework tests</>");
                $style->writeln("  <fg=green>orm:test</>              <fg=gray>Test ORM</>");
                $style->writeln("  <fg=green>cache:test</>            <fg=gray>Test cache system</>");
                $style->writeln("  <fg=green>mail:test</>             <fg=gray>Test mail system</>");
                $style->writeln("  <fg=green>queue:test</>            <fg=gray>Test queue system</>");
                $style->writeln("  <fg=green>schedule:test</>         <fg=gray>Test scheduler</>");
                $style->writeln("  <fg=green>security:test</>         <fg=gray>Test security layer</>");
                $style->writeln("  <fg=green>storage:test</>          <fg=gray>Test storage system</>");
                $style->writeln("  <fg=green>log:test</>              <fg=gray>Test logger</>");
                $style->writeln("  <fg=green>config:test</>           <fg=gray>Test config system</>");
                $style->writeln("  <fg=green>validate:test</>         <fg=gray>Test validator</>");
                $style->writeln("");

                $style->writeln("<fg=yellow>Documentation</>");
                $style->writeln("  <fg=gray>Coming Soon</>");
                $style->writeln("");

                $style->writeln("<fg=yellow>GitHub</>");
                $style->writeln("  <fg=cyan>https://github.com/RITH-1437/zero-ping</>");
                $style->writeln("");
        }
    }
}
