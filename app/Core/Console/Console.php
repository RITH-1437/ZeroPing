<?php

namespace App\Core\Console;

use App\Core\Console\Commands\AboutCommand;
use App\Core\Console\Commands\CacheClearCommand;
use App\Core\Console\Commands\CacheTestCommand;
use App\Core\Console\Commands\ConfigCacheCommand;
use App\Core\Console\Commands\ConfigClearCommand;
use App\Core\Console\Commands\ConfigTestCommand;
use App\Core\Console\Commands\DbSeedCommand;
use App\Core\Console\Commands\DoctorCommand;
use App\Core\Console\Commands\KeyGenerateCommand;
use App\Core\Console\Commands\LogTestCommand;
use App\Core\Console\Commands\MailTestCommand;
use App\Core\Console\Commands\MakeCommandCommand;
use App\Core\Console\Commands\MakeControllerCommand;
use App\Core\Console\Commands\MakeMailCommand;
use App\Core\Console\Commands\MakeMiddlewareCommand;
use App\Core\Console\Commands\MakeMigrationCommand;
use App\Core\Console\Commands\MakeModelCommand;
use App\Core\Console\Commands\MakePolicyCommand;
use App\Core\Console\Commands\MakeProviderCommand;
use App\Core\Console\Commands\MakeRepositoryCommand;
use App\Core\Console\Commands\MakeRequestCommand;
use App\Core\Console\Commands\MakeSeederCommand;
use App\Core\Console\Commands\MakeServiceCommand;
use App\Core\Console\Commands\MakeTestCommand;
use App\Core\Console\Commands\MigrateCommand;
use App\Core\Console\Commands\MigrateFreshCommand;
use App\Core\Console\Commands\MigrateRefreshCommand;
use App\Core\Console\Commands\MigrateResetCommand;
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

        if ($command === null || $command === '--help' || $command === '-h' || $command === '-?') {
            $this->showHelp();
            return;
        }

        $helpFlags = ['-h', '--help', '-?'];
        $rest = array_slice($argv, 2);

        if ($command !== 'help' && $command !== 'list') {
            foreach ($rest as $token) {
                if (in_array($token, $helpFlags, true)) {
                    $this->showCommandHelp($command);
                    return;
                }
            }
        }

        switch ($command) {

            case 'version':
                echo "ZeroPing Framework v" . \App\Core\Application\App::VERSION . "\n";
                break;

            case 'about':
                (new AboutCommand())->handle();
                break;

            case 'help':
                $target = $argv[2] ?? null;
                if ($target !== null) {
                    $this->showCommandHelp($target);
                } else {
                    $this->showHelp();
                }
                break;

            case 'list':
                $this->showHelp();
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

            case 'migrate:reset':
                (new MigrateResetCommand())->handle();
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

            case 'make:seeder':
                (new MakeSeederCommand())->handle($argv[2] ?? '');
                break;

            case 'make:middleware':
                (new MakeMiddlewareCommand())->handle($argv[2] ?? '');
                break;

            case 'make:request':
                (new MakeRequestCommand())->handle($argv[2] ?? '');
                break;

            case 'make:policy':
                (new MakePolicyCommand())->handle($argv[2] ?? '');
                break;

            case 'make:provider':
                (new MakeProviderCommand())->handle($argv[2] ?? '');
                break;

            case 'make:command':
                (new MakeCommandCommand())->handle($argv[2] ?? '');
                break;

            case 'make:test':
                (new MakeTestCommand())->handle($argv[2] ?? '');
                break;

            // ── Database / Seeds ──────────────────────────────────────────────
            case 'db:seed':
                (new DbSeedCommand())->handle();
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

            case 'doctor':
                (new DoctorCommand())->handle();
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
                $this->showHelp();
                break;
        }
    }

    /**
     * Single source of truth for the command listing and per-command help.
     * Group => [ command => [description, [option => description]] ].
     *
     * @return array<string, array<string, array{0: string, 1: array<string, string>}>>
     */
    private function commandInfo(): array
    {
        $force = ['--force' => 'Overwrite existing files when generating'];

        return [
            'General' => [
                'serve' => ['Run the development server', []],
                'about' => ['Show framework information', []],
                'help'  => ['Show the help screen (or command-specific help)', []],
                'new'   => ['Scaffold a new project from a starter template', $force],
            ],
            'Migrations' => [
                'migrate'          => ['Run database migrations', []],
                'migrate:fresh'    => ['Drop all tables and re-run migrations', []],
                'migrate:refresh'  => ['Rollback all migrations then re-run them', []],
                'migrate:rollback' => ['Rollback the last migration batch', []],
                'migrate:reset'    => ['Rollback all migrations', []],
                'migrate:status'   => ['Show migration status', []],
            ],
            'Make' => [
                'make:model'      => ['Create an Eloquent-style model', $force],
                'make:controller' => ['Create an HTTP controller', $force],
                'make:service'    => ['Create a service class', $force],
                'make:repository' => ['Create a repository class', $force],
                'make:migration'  => ['Create a migration file', $force],
                'make:mail'       => ['Create a mailable class and email view', $force],
                'make:seeder'     => ['Create a database seeder', $force],
                'make:middleware' => ['Create an HTTP middleware', $force],
                'make:request'    => ['Create a form request', $force],
                'make:policy'     => ['Create an authorization policy', $force],
                'make:provider'   => ['Create a service provider', $force],
                'make:command'    => ['Create a console command', $force],
                'make:test'       => ['Create a unit or feature test', ['--feature' => 'Create a feature test instead of a unit test'] + $force],
            ],
            'Database' => [
                'db:seed' => ['Seed the database with records', ['--class=' => 'Run only this seeder class']],
            ],
            'Routes' => [
                'route:list'  => ['Display all registered routes', []],
                'route:cache' => ['Cache the route definitions', []],
                'route:clear' => ['Clear the route cache', []],
            ],
            'Config' => [
                'config:cache' => ['Cache the configuration files', []],
                'config:clear' => ['Clear the configuration cache', []],
                'config:test'  => ['Run configuration diagnostics', []],
            ],
            'Cache' => [
                'cache:clear' => ['Flush the application cache', []],
                'cache:test'  => ['Run cache diagnostics', []],
            ],
            'Queue' => [
                'queue:work'    => ['Process jobs from the queue', ['--connection=' => 'Queue connection to use', '--queue=' => 'Queue name to work', '--delay=' => 'Delay before retrying (seconds)', '--sleep=' => 'Sleep between jobs (seconds)', '--tries=' => 'Max attempts before failing']],
                'queue:listen'  => ['Listen to the queue continuously', ['--connection=' => 'Queue connection to use', '--queue=' => 'Queue name to work', '--sleep=' => 'Sleep between jobs (seconds)', '--tries=' => 'Max attempts before failing']],
                'queue:failed'  => ['List failed queue jobs', []],
                'queue:retry'   => ['Retry a failed queue job by id', []],
                'queue:clear'   => ['Delete all jobs from the queue', []],
                'queue:restart' => ['Restart running queue workers', []],
                'queue:test'    => ['Run queue diagnostics', []],
            ],
            'Schedule' => [
                'schedule:run'   => ['Run due scheduled events', []],
                'schedule:list'  => ['List scheduled events', []],
                'schedule:test'  => ['Run scheduler diagnostics', []],
                'schedule:clear' => ['Clear the scheduler cache', []],
            ],
            'Storage & Views' => [
                'storage:clear' => ['Clear storage files', []],
                'storage:test'  => ['Run storage diagnostics', []],
                'view:cache'    => ['Cache compiled view files', []],
                'view:clear'    => ['Clear compiled view files', []],
            ],
            'Optimize' => [
                'optimize'       => ['Cache config, routes and views', []],
                'optimize:clear' => ['Clear all cached data', []],
            ],
            'Security & Keys' => [
                'key:generate' => ['Generate the application key', []],
                'doctor'       => ['Verify the installation and environment', []],
                'security:test' => ['Run security-layer diagnostics', []],
            ],
            'Search' => [
                'search:index' => ['Build the documentation search index', []],
            ],
            'Testing & Diagnostics' => [
                'test'           => ['Run the framework test suite', []],
                'orm:test'       => ['Run ORM diagnostics', []],
                'mail:test'      => ['Run mail diagnostics', []],
                'log:test'       => ['Run logger diagnostics', []],
                'validate:test'  => ['Run validator diagnostics', []],
            ],
        ];
    }

    /**
     * @return array{0: string, 1: array<string, string>}|null
     */
    private function findCommand(string $name): ?array
    {
        foreach ($this->commandInfo() as $commands) {
            if (array_key_exists($name, $commands)) {
                return $commands[$name];
            }
        }

        return null;
    }

    /**
     * Render help for a single command.
     */
    private function showCommandHelp(string $name): void
    {
        $style = new ConsoleStyle();

        $info = $this->findCommand($name);

        if ($info === null) {
            $style->writeln("<fg=red>Command '{$name}' not found. Run <fg=white>php zero help</> for a list.</>");
            return;
        }

        [$description, $options] = $info;

        $style->writeln('');
        $style->writeln("<options=bold;fg=cyan>{$name}</>");
        $style->writeln('<fg=gray>' . str_repeat('═', mb_strlen($name)) . '</>');
        $style->writeln('');
        $style->writeln("<fg=white>{$description}</>");
        $style->writeln('');
        $style->writeln('<fg=yellow>Usage:</>');
        $style->writeln("  <fg=white>php zero {$name} [options]</>");
        $style->writeln('');
        $style->writeln('<fg=yellow>Options:</>');
        $style->writeln("  <fg=green>--help</>   <fg=gray>Show this command's help</>");

        foreach ($options as $flag => $desc) {
            $style->writeln("  <fg=green>{$flag}</>   <fg=gray>{$desc}</>");
        }

        $style->writeln('');
    }

    /**
     * Render the command listing / help screen.
     */
    private function showHelp(): void
    {
        $style = new ConsoleStyle();

        $style->writeln("<fg=cyan>███████╗███████╗██████╗  ██████╗ ██████╗ ██╗███╗   ██╗ ██████╗</>");
        $style->writeln("<fg=cyan>╚══███╔╝██╔════╝██╔══██╗██╔═══██╗██╔══██╗██║████╗  ██║██╔════╝</>");
        $style->writeln("<fg=cyan>  ███╔╝ █████╗  ██████╔╝██║   ██║██████╔╝██║██╔██╗ ██║██║  ███╗</>");
        $style->writeln("<fg=cyan> ███╔╝  ██╔══╝  ██╔══██╗██║   ██║██╔═══╝ ██║██║╚██╗██║██║   ██║</>");
        $style->writeln("<fg=cyan>███████╗███████╗██║  ██║╚██████╔╝██║     ██║██║ ╚████║╚██████╔╝</>");
        $style->writeln("<fg=cyan>╚══════╝╚══════╝╚═╝  ╚═╝ ╚═════╝ ╚═╝     ╚═╝╚═╝  ╚═══╝ ╚═════╝</>");
        $style->writeln("");

        $style->writeln("<fg=green>ZeroPing Framework</> <fg=yellow>v" . \App\Core\Application\App::VERSION . "</>");
        $style->writeln("<fg=gray>Lightweight PHP Framework built from scratch.</>");
        $style->writeln("");

        $style->writeln("<fg=yellow>Usage:</>");
        $style->writeln("  <fg=white>php zero &lt;command&gt; [options]</>");
        $style->writeln("");

        $style->writeln("<fg=yellow>Available Commands</>");
        $style->writeln("<fg=gray>──────────────────────────────────────────────────────────────</>");

        foreach ($this->commandInfo() as $group => $commands) {
            $style->writeln('');
            $style->writeln("  <fg=yellow>{$group}</>");

            foreach ($commands as $name => [$description]) {
                $padded = str_pad($name, 22);
                $style->writeln("  <fg=green>{$padded}</> <fg=gray>{$description}</>");
            }
        }

        $style->writeln('');
        $style->writeln("<fg=yellow>Global Options</>");
        $style->writeln("  <fg=green>--help</>        <fg=gray>Show this help screen or command-specific help</>");
        $style->writeln("  <fg=green>--force</>       <fg=gray>Overwrite existing files when generating</>");
        $style->writeln("  <fg=green>--class=</>      <fg=gray>Target a specific class (db:seed)</>");
        $style->writeln("  <fg=green>--feature</>     <fg=gray>Create a feature test (make:test)</>");
        $style->writeln("  <fg=green>--connection=</> <fg=gray>Queue connection (queue:work, queue:listen)</>");
        $style->writeln("  <fg=green>--queue=</>      <fg=gray>Queue name (queue:work, queue:listen)</>");
        $style->writeln("  <fg=green>--delay=</>      <fg=gray>Delay before retry in seconds (queue:work)</>");
        $style->writeln("  <fg=green>--sleep=</>      <fg=gray>Sleep between jobs in seconds (queue)</>");
        $style->writeln("  <fg=green>--tries=</>      <fg=gray>Max attempts before failing (queue)</>");
        $style->writeln("");

        $style->writeln("<fg=yellow>GitHub</>");
        $style->writeln("  <fg=cyan>https://github.com/RITH-1437/ZeroPing</>");
        $style->writeln("");
    }
}
