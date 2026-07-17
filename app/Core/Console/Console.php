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
use App\Core\Console\Commands\InstallCommand;
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
use App\Core\Console\Commands\PackageListCommand;
use App\Core\Console\Commands\PackageEnableCommand;
use App\Core\Console\Commands\PackageDisableCommand;
use App\Core\Console\Commands\PackageInstallCommand;
use App\Core\Console\Commands\PackageRemoveCommand;
use App\Core\Console\Commands\PackageUpdateCommand;
use App\Core\Console\Commands\PackageCreateCommand;
use App\Core\Console\Commands\StarterInstallCommand;
use App\Core\Console\Commands\VendorPublishCommand;
use App\Core\Console\Generators\MakeJobCommand;
use App\Core\Console\Generators\MakeEventCommand;
use App\Core\Console\Generators\MakeListenerCommand;
use App\Core\Console\Generators\MakeNotificationCommand;
use App\Core\Console\Generators\MakeFactoryCommand;
use App\Core\Console\Generators\MakeAuthCommand;
use App\Core\Console\Generators\MakeEnumCommand;
use App\Core\Console\Commands\MakeServiceCommand;
use App\Core\Console\Commands\MakeTestCommand;
use App\Core\Console\Commands\MigrateCommand;
use App\Core\Console\Commands\MigrateFreshCommand;
use App\Core\Console\Commands\MigrateRefreshCommand;
use App\Core\Console\Commands\MigrateResetCommand;
use App\Core\Console\Commands\MigrateRollbackCommand;
use App\Core\Console\Commands\MigrateStatusCommand;
use App\Core\Console\Commands\MonitorCommand;
use App\Core\Console\Commands\OptimizeClearCommand;
use App\Core\Console\Commands\OptimizeCommand;
use App\Core\Console\Commands\OrmTestCommand;
use App\Core\Console\Commands\PublishCommand;
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
use App\Core\Console\Commands\SearchIndexCommand;
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

            // ── Project ───────────────────────────────────────────────────
            case 'serve':
                (new ServeCommand())->handle(array_slice($argv, 2));
                break;

            case 'install':
                (new InstallCommand())->handle();
                break;

            // ── Migrations ───────────────────────────────────────────────
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

            // ── Make ────────────────────────────────────────────────────
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

            case 'make:job':
                (new MakeJobCommand())->handle($argv[2] ?? '');
                break;

            case 'make:event':
                (new MakeEventCommand())->handle($argv[2] ?? '');
                break;

            case 'make:listener':
                (new MakeListenerCommand())->handle($argv[2] ?? '');
                break;

            case 'make:notification':
                (new MakeNotificationCommand())->handle($argv[2] ?? '');
                break;

            case 'make:factory':
                (new MakeFactoryCommand())->handle($argv[2] ?? '');
                break;

            case 'make:auth':
                (new MakeAuthCommand())->handle($argv[2] ?? '');
                break;

            case 'make:enum':
                (new MakeEnumCommand())->handle($argv[2] ?? '');
                break;

            case 'package:list':
                (new PackageListCommand())->handle();
                break;

            case 'package:enable':
                (new PackageEnableCommand())->handle($argv[2] ?? '');
                break;

            case 'package:disable':
                (new PackageDisableCommand())->handle($argv[2] ?? '');
                break;

            case 'package:install':
                (new PackageInstallCommand())->handle($argv[2] ?? '');
                break;

            case 'package:remove':
                (new PackageRemoveCommand())->handle($argv[2] ?? '');
                break;

            case 'package:update':
                (new PackageUpdateCommand())->handle($argv[2] ?? '');
                break;

            case 'package:create':
                (new PackageCreateCommand())->handle($argv[2] ?? '');
                break;

            case 'starter:install':
                (new StarterInstallCommand())->handle($argv[2] ?? '');
                break;

            case 'vendor:publish':
                (new VendorPublishCommand())->handle();
                break;

            // ── Database / Seeds ──────────────────────────────────────
            case 'db:seed':
                (new DbSeedCommand())->handle();
                break;

            // ── Routes ─────────────────────────────────────────────────
            case 'route:list':
                (new RouteListCommand())->handle();
                break;

            case 'route:cache':
                (new RouteCacheCommand())->handle();
                break;

            case 'route:clear':
                (new RouteClearCommand())->handle();
                break;

            // ── Config ───────────────────────────────────────────────
            case 'config:test':
                (new ConfigTestCommand())->handle();
                break;

            case 'config:cache':
                (new ConfigCacheCommand())->handle();
                break;

            case 'config:clear':
                (new ConfigClearCommand())->handle();
                break;

            // ── Cache ───────────────────────────────────────────────
            case 'cache:test':
                (new CacheTestCommand())->handle();
                break;

            case 'cache:clear':
                (new CacheClearCommand())->handle();
                break;

            // ── Queue ───────────────────────────────────────────────
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

            // ── Schedule ────────────────────────────────────────────
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

            // ── Storage ─────────────────────────────────────────────
            case 'storage:test':
                (new StorageTestCommand())->handle();
                break;

            case 'storage:clear':
                (new StorageClearCommand())->handle();
                break;

            // ── Views ───────────────────────────────────────────────
            case 'view:cache':
                (new ViewCacheCommand())->handle();
                break;

            case 'view:clear':
                (new ViewClearCommand())->handle();
                break;

            // ── Optimize ────────────────────────────────────────────
            case 'optimize':
                (new OptimizeCommand())->handle();
                break;

            case 'optimize:clear':
                (new OptimizeClearCommand())->handle();
                break;

            // ── Keys / Security ─────────────────────────────────────
            case 'key:generate':
                (new KeyGenerateCommand())->handle();
                break;

            case 'doctor':
                (new DoctorCommand())->handle();
                break;

            case 'monitor':
                (new MonitorCommand())->handle();
                break;

            case 'install':
                (new InstallCommand())->handle();
                break;

            case 'security:test':
                (new SecurityTestCommand())->handle();
                break;

            // ── Search ─────────────────────────────────────────────
            case 'search:index':
                (new SearchIndexCommand())->handle();
                break;

            // ── Publishing ────────────────────────────────────────
            case 'publish':
                (new PublishCommand())->handle();
                break;

            // ── Starter Templates ─────────────────────────────────
            case 'new':
                $command = new \App\Core\Console\Commands\NewCommand();
                $command->handle($argv[2] ?? '', array_slice($argv, 3));
                break;

            // ── Tests / Misc ─────────────────────────────────────
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
                $this->runPackageCommand($command, $argv);
                break;
        }
    }

    /**
     * Dispatch a command registered by an installed package, falling back to the
     * help screen when the command is unknown.
     */
    private function runPackageCommand(string $name, array $argv): void
    {
        if (!class_exists(\Zeroping\Support\Console\CommandRegistry::class)) {
            $this->showHelp();
            return;
        }

        $class = \Zeroping\Support\Console\CommandRegistry::find($name);

        if ($class === null) {
            $this->showHelp();
            return;
        }

        (new $class())->handle();
    }

    /**
     * Single source of truth for the command listing and per-command help.
     *
     * group => command => [
     *   'description' => string,
     *   'options'     => [flag => description],
     *   'arguments'   => [['name' =>, 'description' =>]],
     *   'examples'    => [string],
     *   'notes'       => string,
     * ]
     *
     * @return array<string, array<string, array{description: string, options: array<string,string>, arguments: array<int,array{name:string,description:string}>, examples: string[], notes: string}>>
     */
    private function commandInfo(): array
    {
        $force = ['--force' => 'Overwrite existing files when generating'];

        return [
            'Project' => [
                'new' => [
                    'description' => 'Scaffold a new project from a starter template',
                    'options' => $force,
                    'arguments' => [['name' => 'type', 'description' => 'empty | mvc | blog | api']],
                    'examples' => ['php zero new blog', 'php zero new api'],
                    'notes' => 'Starter templates live in /templates (empty, mvc, blog, api).',
                ],
                'install' => [
                    'description' => 'Run the interactive installation wizard',
                    'options' => [],
                    'arguments' => [],
                    'examples' => ['php zero install'],
                    'notes' => 'Guides you through .env, database, APP_KEY and migrations.',
                ],
                'serve' => [
                    'description' => 'Run the development server',
                    'options' => [],
                    'arguments' => [['name' => 'port', 'description' => 'Port to listen on (default 1437)']],
                    'examples' => ['php zero serve', 'php zero serve 8000'],
                    'notes' => 'Press Ctrl+C to stop.',
                ],
            ],
            'Database & Migrations' => [
                'migrate' => [
                    'description' => 'Run database migrations',
                    'options' => [],
                    'arguments' => [],
                    'examples' => ['php zero migrate'],
                    'notes' => '',
                ],
                'migrate:fresh' => [
                    'description' => 'Drop all tables and re-run migrations',
                    'options' => [],
                    'arguments' => [],
                    'examples' => ['php zero migrate:fresh'],
                    'notes' => 'Destroys all data — use with care.',
                ],
                'migrate:refresh' => [
                    'description' => 'Rollback all migrations then re-run them',
                    'options' => [],
                    'arguments' => [],
                    'examples' => ['php zero migrate:refresh'],
                    'notes' => '',
                ],
                'migrate:rollback' => [
                    'description' => 'Rollback the last migration batch',
                    'options' => [],
                    'arguments' => [],
                    'examples' => ['php zero migrate:rollback'],
                    'notes' => '',
                ],
                'migrate:reset' => [
                    'description' => 'Rollback all migrations',
                    'options' => [],
                    'arguments' => [],
                    'examples' => ['php zero migrate:reset'],
                    'notes' => '',
                ],
                'migrate:status' => [
                    'description' => 'Show migration status',
                    'options' => [],
                    'arguments' => [],
                    'examples' => ['php zero migrate:status'],
                    'notes' => '',
                ],
                'db:seed' => [
                    'description' => 'Seed the database with records',
                    'options' => ['--class=' => 'Run only this seeder class'],
                    'arguments' => [],
                    'examples' => ['php zero db:seed', 'php zero db:seed --class=DatabaseSeeder'],
                    'notes' => '',
                ],
            ],
            'Generators' => [
                'make:model' => [
                    'description' => 'Create an Eloquent-style model',
                    'options' => [
                        '--all' => 'Generate migration, factory, seeder and controller too',
                        '--migration' => 'Also create a create_{table}_table migration',
                        '--factory' => 'Also create a model factory',
                        '--seeder' => 'Also create a database seeder',
                        '--controller' => 'Also create a controller',
                        '--resource' => 'Create a resourceful controller (with --controller/--all)',
                    ] + $force,
                    'arguments' => [['name' => 'name', 'description' => 'Model class name']],
                    'examples' => ['php zero make:model Post', 'php zero make:model Post --all'],
                    'notes' => '',
                ],
                'make:controller' => [
                    'description' => 'Create an HTTP controller',
                    'options' => ['--resource' => 'Generate index/show/store/update/destroy methods'] + $force,
                    'arguments' => [['name' => 'name', 'description' => 'Controller class name']],
                    'examples' => ['php zero make:controller PostController', 'php zero make:controller PostController --resource'],
                    'notes' => '',
                ],
                'make:service' => [
                    'description' => 'Create a service class',
                    'options' => $force,
                    'arguments' => [['name' => 'name', 'description' => 'Service class name']],
                    'examples' => ['php zero make:service PaymentService'],
                    'notes' => '',
                ],
                'make:repository' => [
                    'description' => 'Create a repository class',
                    'options' => $force,
                    'arguments' => [['name' => 'name', 'description' => 'Repository class name']],
                    'examples' => ['php zero make:repository UserRepository'],
                    'notes' => '',
                ],
                'make:migration' => [
                    'description' => 'Create a migration file',
                    'options' => $force,
                    'arguments' => [['name' => 'name', 'description' => 'Migration name, e.g. create_posts_table']],
                    'examples' => ['php zero make:migration create_posts_table'],
                    'notes' => '',
                ],
                'make:mail' => [
                    'description' => 'Create a mailable class and email view',
                    'options' => $force,
                    'arguments' => [['name' => 'name', 'description' => 'Mailable class name']],
                    'examples' => ['php zero make:mail WelcomeMail'],
                    'notes' => '',
                ],
                'make:seeder' => [
                    'description' => 'Create a database seeder',
                    'options' => $force,
                    'arguments' => [['name' => 'name', 'description' => 'Seeder class name']],
                    'examples' => ['php zero make:seeder DatabaseSeeder'],
                    'notes' => '',
                ],
                'make:middleware' => [
                    'description' => 'Create an HTTP middleware',
                    'options' => $force,
                    'arguments' => [['name' => 'name', 'description' => 'Middleware class name']],
                    'examples' => ['php zero make:middleware AuthMiddleware'],
                    'notes' => '',
                ],
                'make:request' => [
                    'description' => 'Create a form request',
                    'options' => $force,
                    'arguments' => [['name' => 'name', 'description' => 'Request class name']],
                    'examples' => ['php zero make:request StorePostRequest'],
                    'notes' => '',
                ],
                'make:policy' => [
                    'description' => 'Create an authorization policy',
                    'options' => $force,
                    'arguments' => [['name' => 'name', 'description' => 'Policy class name']],
                    'examples' => ['php zero make:policy PostPolicy'],
                    'notes' => '',
                ],
                'make:provider' => [
                    'description' => 'Create a service provider',
                    'options' => $force,
                    'arguments' => [['name' => 'name', 'description' => 'Provider class name']],
                    'examples' => ['php zero make:provider AppServiceProvider'],
                    'notes' => '',
                ],
                'make:command' => [
                    'description' => 'Create a console command',
                    'options' => $force,
                    'arguments' => [['name' => 'name', 'description' => 'Command class name']],
                    'examples' => ['php zero make:command SendEmailsCommand'],
                    'notes' => '',
                ],
                'make:test' => [
                    'description' => 'Create a unit or feature test',
                    'options' => ['--feature' => 'Create a feature test instead of a unit test'] + $force,
                    'arguments' => [['name' => 'name', 'description' => 'Test class name']],
                    'examples' => ['php zero make:test UserTest', 'php zero make:test ApiTest --feature'],
                    'notes' => '',
                ],
                'make:job' => [
                    'description' => 'Create a new queue job class',
                    'options' => $force,
                    'arguments' => [['name' => 'name', 'description' => 'Job class name']],
                    'examples' => ['php zero make:job SendWelcomeEmail'],
                    'notes' => 'Extends App\Core\Queue\Job and implements handle().',
                ],
                'make:event' => [
                    'description' => 'Create a new event class',
                    'options' => $force,
                    'arguments' => [['name' => 'name', 'description' => 'Event class name']],
                    'examples' => ['php zero make:event UserRegistered'],
                    'notes' => 'Extends App\Core\Events\Event.',
                ],
                'make:listener' => [
                    'description' => 'Create a new event listener',
                    'options' => ['--event=' => 'The event this listener handles'] + $force,
                    'arguments' => [['name' => 'name', 'description' => 'Listener class name']],
                    'examples' => ['php zero make:listener LogUserRegistered --event=UserRegistered'],
                    'notes' => 'Implements App\Core\Events\Listener.',
                ],
                'make:notification' => [
                    'description' => 'Create a new notification class',
                    'options' => $force,
                    'arguments' => [['name' => 'name', 'description' => 'Notification class name']],
                    'examples' => ['php zero make:notification InvoicePaid'],
                    'notes' => 'Scaffolds via()/toMail()/toArray() methods.',
                ],
                'make:factory' => [
                    'description' => 'Create a model factory',
                    'options' => ['--model=' => 'The model this factory builds'] + $force,
                    'arguments' => [['name' => 'name', 'description' => 'Factory class name']],
                    'examples' => ['php zero make:factory PostFactory --model=Post'],
                    'notes' => '',
                ],
                'make:auth' => [
                    'description' => 'Scaffold authentication (controller, views, routes)',
                    'options' => ['--name=' => 'Controller class name (default AuthController)'] + $force,
                    'arguments' => [],
                    'examples' => ['php zero make:auth', 'php zero make:auth --name=AuthController'],
                    'notes' => 'Generates AuthController, auth views, and appends routes to config/routes.php.',
                ],
                'make:enum' => [
                    'description' => 'Create a new backed enum',
                    'options' => $force,
                    'arguments' => [['name' => 'name', 'description' => 'Enum class name']],
                    'examples' => ['php zero make:enum Status'],
                    'notes' => 'Generates a string-backed enum in app/Enums.',
                ],
            ],
            'Packages' => [
                'package:list' => [
                    'description' => 'List installed ZeroPing packages and their state',
                    'options' => [],
                    'arguments' => [],
                    'examples' => ['php zero package:list'],
                    'notes' => 'Auto-discovered from packages/* and vendor (extra.zeroping.providers).',
                ],
                'package:enable' => [
                    'description' => 'Enable a ZeroPing package',
                    'options' => [],
                    'arguments' => [['name' => 'name', 'description' => 'Package name (e.g. zeroping/queue)']],
                    'examples' => ['php zero package:enable zeroping/queue'],
                    'notes' => 'Edits config/packages.php.',
                ],
                'package:disable' => [
                    'description' => 'Disable a ZeroPing package',
                    'options' => [],
                    'arguments' => [['name' => 'name', 'description' => 'Package name']],
                    'examples' => ['php zero package:disable zeroping/queue'],
                    'notes' => 'Edits config/packages.php.',
                ],
                'package:install' => [
                    'description' => 'Install and enable a ZeroPing package',
                    'options' => [],
                    'arguments' => [['name' => 'name', 'description' => 'Package name']],
                    'examples' => ['php zero package:install zeroping/queue'],
                    'notes' => 'Enables a discovered package, else attempts composer require.',
                ],
                'package:remove' => [
                    'description' => 'Remove a ZeroPing package from config',
                    'options' => ['--force' => 'Also delete the package directory'],
                    'arguments' => [['name' => 'name', 'description' => 'Package name']],
                    'examples' => ['php zero package:remove zeroping/queue', 'php zero package:remove zeroping/queue --force'],
                    'notes' => '',
                ],
                'package:update' => [
                    'description' => 'Update a ZeroPing package',
                    'options' => [],
                    'arguments' => [['name' => 'name', 'description' => 'Package name']],
                    'examples' => ['php zero package:update zeroping/queue'],
                    'notes' => 'Best-effort composer update.',
                ],
                'package:create' => [
                    'description' => 'Scaffold a new ZeroPing package',
                    'options' => [],
                    'arguments' => [['name' => 'name', 'description' => 'Package (StudlyCase) name']],
                    'examples' => ['php zero package:create Blog'],
                    'notes' => 'Creates packages/zeroping/<name>/ with provider, routes, config, migrations, views, assets, tests.',
                ],
                'starter:install' => [
                    'description' => 'Install a starter kit (bundle of packages)',
                    'options' => [],
                    'arguments' => [['name' => 'kit', 'description' => 'arena | ecommerce | api']],
                    'examples' => ['php zero starter:install arena'],
                    'notes' => 'Enables every package in the kit at once.',
                ],
                'vendor:publish' => [
                    'description' => 'Publish package assets (config, views, migrations, ...)',
                    'options' => ['--group=' => 'Publish only this group (default: all)'],
                    'arguments' => [],
                    'examples' => ['php zero vendor:publish', 'php zero vendor:publish --group=queue-config', 'php zero vendor:publish --force'],
                    'notes' => 'Copies package files into the host app when missing.',
                ],
            ],
            'Routes' => [
                'route:list' => [
                    'description' => 'Display all registered routes',
                    'options' => [],
                    'arguments' => [],
                    'examples' => ['php zero route:list'],
                    'notes' => '',
                ],
                'route:cache' => [
                    'description' => 'Cache the route definitions',
                    'options' => [],
                    'arguments' => [],
                    'examples' => ['php zero route:cache'],
                    'notes' => '',
                ],
                'route:clear' => [
                    'description' => 'Clear the route cache',
                    'options' => [],
                    'arguments' => [],
                    'examples' => ['php zero route:clear'],
                    'notes' => '',
                ],
            ],
            'Config & Cache' => [
                'config:cache' => [
                    'description' => 'Cache the configuration files',
                    'options' => [],
                    'arguments' => [],
                    'examples' => ['php zero config:cache'],
                    'notes' => '',
                ],
                'config:clear' => [
                    'description' => 'Clear the configuration cache',
                    'options' => [],
                    'arguments' => [],
                    'examples' => ['php zero config:clear'],
                    'notes' => '',
                ],
                'config:test' => [
                    'description' => 'Run configuration diagnostics',
                    'options' => [],
                    'arguments' => [],
                    'examples' => ['php zero config:test'],
                    'notes' => '',
                ],
                'cache:clear' => [
                    'description' => 'Flush the application cache',
                    'options' => [],
                    'arguments' => [],
                    'examples' => ['php zero cache:clear'],
                    'notes' => '',
                ],
                'cache:test' => [
                    'description' => 'Run cache diagnostics',
                    'options' => [],
                    'arguments' => [],
                    'examples' => ['php zero cache:test'],
                    'notes' => '',
                ],
                'view:cache' => [
                    'description' => 'Cache compiled view files',
                    'options' => [],
                    'arguments' => [],
                    'examples' => ['php zero view:cache'],
                    'notes' => '',
                ],
                'view:clear' => [
                    'description' => 'Clear compiled view files',
                    'options' => [],
                    'arguments' => [],
                    'examples' => ['php zero view:clear'],
                    'notes' => '',
                ],
                'optimize' => [
                    'description' => 'Cache config, routes and views',
                    'options' => [],
                    'arguments' => [],
                    'examples' => ['php zero optimize'],
                    'notes' => '',
                ],
                'optimize:clear' => [
                    'description' => 'Clear all cached data',
                    'options' => [],
                    'arguments' => [],
                    'examples' => ['php zero optimize:clear'],
                    'notes' => '',
                ],
            ],
            'Queue & Schedule' => [
                'queue:work' => [
                    'description' => 'Process jobs from the queue',
                    'options' => [
                        '--connection=' => 'Queue connection to use',
                        '--queue=' => 'Queue name to work',
                        '--delay=' => 'Delay before retrying (seconds)',
                        '--sleep=' => 'Sleep between jobs (seconds)',
                        '--tries=' => 'Max attempts before failing',
                    ],
                    'arguments' => [],
                    'examples' => ['php zero queue:work', 'php zero queue:work --queue=emails'],
                    'notes' => '',
                ],
                'queue:listen' => [
                    'description' => 'Listen to the queue continuously',
                    'options' => [
                        '--connection=' => 'Queue connection to use',
                        '--queue=' => 'Queue name to work',
                        '--sleep=' => 'Sleep between jobs (seconds)',
                        '--tries=' => 'Max attempts before failing',
                    ],
                    'arguments' => [],
                    'examples' => ['php zero queue:listen'],
                    'notes' => '',
                ],
                'queue:failed' => [
                    'description' => 'List failed queue jobs',
                    'options' => [],
                    'arguments' => [],
                    'examples' => ['php zero queue:failed'],
                    'notes' => '',
                ],
                'queue:retry' => [
                    'description' => 'Retry a failed queue job by id',
                    'options' => [],
                    'arguments' => [['name' => 'id', 'description' => 'Failed job id']],
                    'examples' => ['php zero queue:retry 5'],
                    'notes' => '',
                ],
                'queue:clear' => [
                    'description' => 'Delete all jobs from the queue',
                    'options' => [],
                    'arguments' => [],
                    'examples' => ['php zero queue:clear'],
                    'notes' => '',
                ],
                'queue:restart' => [
                    'description' => 'Restart running queue workers',
                    'options' => [],
                    'arguments' => [],
                    'examples' => ['php zero queue:restart'],
                    'notes' => '',
                ],
                'queue:test' => [
                    'description' => 'Run queue diagnostics',
                    'options' => [],
                    'arguments' => [],
                    'examples' => ['php zero queue:test'],
                    'notes' => '',
                ],
                'schedule:run' => [
                    'description' => 'Run due scheduled events',
                    'options' => [],
                    'arguments' => [],
                    'examples' => ['php zero schedule:run'],
                    'notes' => '',
                ],
                'schedule:list' => [
                    'description' => 'List scheduled events',
                    'options' => [],
                    'arguments' => [],
                    'examples' => ['php zero schedule:list'],
                    'notes' => '',
                ],
                'schedule:test' => [
                    'description' => 'Run scheduler diagnostics',
                    'options' => [],
                    'arguments' => [],
                    'examples' => ['php zero schedule:test'],
                    'notes' => '',
                ],
                'schedule:clear' => [
                    'description' => 'Clear the scheduler cache',
                    'options' => [],
                    'arguments' => [],
                    'examples' => ['php zero schedule:clear'],
                    'notes' => '',
                ],
            ],
            'Storage & Search' => [
                'storage:clear' => [
                    'description' => 'Clear storage files',
                    'options' => [],
                    'arguments' => [],
                    'examples' => ['php zero storage:clear'],
                    'notes' => '',
                ],
                'storage:test' => [
                    'description' => 'Run storage diagnostics',
                    'options' => [],
                    'arguments' => [],
                    'examples' => ['php zero storage:test'],
                    'notes' => '',
                ],
                'search:index' => [
                    'description' => 'Build the documentation search index',
                    'options' => [],
                    'arguments' => [],
                    'examples' => ['php zero search:index'],
                    'notes' => '',
                ],
            ],
            'Security & Keys' => [
                'key:generate' => [
                    'description' => 'Generate the application key',
                    'options' => [],
                    'arguments' => [],
                    'examples' => ['php zero key:generate'],
                    'notes' => 'Writes APP_KEY into .env.',
                ],
                'doctor' => [
                    'description' => 'Verify the installation and environment',
                    'options' => [],
                    'arguments' => [],
                    'examples' => ['php zero doctor'],
                    'notes' => 'Recommended after any environment change.',
                ],
                'monitor' => [
                    'description' => 'Show application health and service status',
                    'options' => [],
                    'arguments' => [],
                    'examples' => ['php zero monitor'],
                    'notes' => 'Reports framework version, PHP and core service status.',
                ],
                'security:test' => [
                    'description' => 'Run security-layer diagnostics',
                    'options' => [],
                    'arguments' => [],
                    'examples' => ['php zero security:test'],
                    'notes' => '',
                ],
            ],
            'Testing & Diagnostics' => [
                'test' => [
                    'description' => 'Run the framework test suite',
                    'options' => [],
                    'arguments' => [],
                    'examples' => ['php zero test'],
                    'notes' => '',
                ],
                'orm:test' => [
                    'description' => 'Run ORM diagnostics',
                    'options' => [],
                    'arguments' => [],
                    'examples' => ['php zero orm:test'],
                    'notes' => '',
                ],
                'mail:test' => [
                    'description' => 'Run mail diagnostics',
                    'options' => [],
                    'arguments' => [],
                    'examples' => ['php zero mail:test'],
                    'notes' => '',
                ],
                'log:test' => [
                    'description' => 'Run logger diagnostics',
                    'options' => [],
                    'arguments' => [],
                    'examples' => ['php zero log:test'],
                    'notes' => '',
                ],
                'validate:test' => [
                    'description' => 'Run validator diagnostics',
                    'options' => [],
                    'arguments' => [],
                    'examples' => ['php zero validate:test'],
                    'notes' => '',
                ],
            ],
            'Utilities' => [
                'about' => [
                    'description' => 'Show framework, PHP, environment and link information',
                    'options' => [],
                    'arguments' => [],
                    'examples' => ['php zero about'],
                    'notes' => '',
                ],
                'version' => [
                    'description' => 'Print the ZeroPing version',
                    'options' => [],
                    'arguments' => [],
                    'examples' => ['php zero version'],
                    'notes' => '',
                ],
                'help' => [
                    'description' => 'Show this help screen or command-specific help',
                    'options' => [],
                    'arguments' => [['name' => 'command', 'description' => 'Optional command to show help for']],
                    'examples' => ['php zero help', 'php zero help make:model'],
                    'notes' => 'All commands also accept --help.',
                ],
                'publish' => [
                    'description' => 'Publish framework config, views, lang and public assets',
                    'options' => ['--group=' => 'config | views | lang | public | all (default)'],
                    'arguments' => [],
                    'examples' => ['php zero publish', 'php zero publish --group=lang'],
                    'notes' => 'Copies framework defaults only when missing.',
                ],
            ],
        ];
    }

    /**
     * @return array{description: string, options: array<string,string>, arguments: array<int,array{name:string,description:string}>, examples: string[], notes: string}|null
     */
    private function findCommand(string $name): ?array
    {
        foreach ($this->commandInfo() as $commands) {
            if (array_key_exists($name, $commands)) {
                return $commands[$name];
            }
        }

        if (class_exists(\Zeroping\Support\Console\CommandRegistry::class)) {
            $class = \Zeroping\Support\Console\CommandRegistry::find($name);

            if ($class !== null) {
                $instance = new $class();

                return [
                    'description' => method_exists($instance, 'getDescription')
                        ? $instance->getDescription()
                        : '',
                    'options'     => [],
                    'arguments'   => [],
                    'examples'    => [],
                    'notes'       => 'Provided by an installed package.',
                ];
            }
        }

        return null;
    }

    /**
     * Render help for a single command (supports `php zero <cmd> --help`).
     */
    private function showCommandHelp(string $name): void
    {
        $style = new ConsoleStyle();

        $info = $this->findCommand($name);

        if ($info === null) {
            $style->writeln("<fg=red>Command '{$name}' not found. Run <fg=white>php zero help</> for a list.</>");
            return;
        }

        $description = $info['description'] ?? '';
        $options     = $info['options'] ?? [];
        $arguments   = $info['arguments'] ?? [];
        $examples    = $info['examples'] ?? [];
        $notes       = $info['notes'] ?? '';

        $style->writeln('');
        $style->writeln("<options=bold;fg=cyan>{$name}</>");
        $style->writeln('<fg=gray>' . str_repeat('═', mb_strlen($name)) . '</>');
        $style->writeln('');
        $style->writeln("<fg=white>{$description}</>");

        $style->writeln('');
        $style->writeln('<fg=yellow>Usage:</>');
        $usage = 'php zero ' . $name;
        foreach ($arguments as $arg) {
            $usage .= ' <fg=green>[' . ($arg['name'] ?? '') . ']</>';
        }
        $usage .= ' <fg=gray>[options]</>';
        $style->writeln('  ' . $usage);

        if ($arguments !== []) {
            $style->writeln('');
            $style->writeln('<fg=yellow>Arguments:</>');
            foreach ($arguments as $arg) {
                $padded = str_pad('<fg=green>' . ($arg['name'] ?? '') . '</>', 22);
                $style->writeln('  ' . $padded . ' <fg=gray>' . ($arg['description'] ?? '') . '</>');
            }
        }

        $style->writeln('');
        $style->writeln('<fg=yellow>Options:</>');
        $style->writeln('  <fg=green>--help</>   <fg=gray>Show this command\'s help</>');
        if ($options === []) {
            $style->writeln('  <fg=gray>No additional options.</>');
        }
        foreach ($options as $flag => $desc) {
            $padded = str_pad('<fg=green>' . $flag . '</>', 22);
            $style->writeln('  ' . $padded . ' <fg=gray>' . $desc . '</>');
        }

        if ($examples !== []) {
            $style->writeln('');
            $style->writeln('<fg=yellow>Examples:</>');
            foreach ($examples as $example) {
                $style->writeln('  <fg=cyan>' . $example . '</>');
            }
        }

        if ($notes !== '') {
            $style->writeln('');
            $style->writeln('<fg=yellow>Notes:</>');
            $style->writeln('  <fg=gray>' . $notes . '</>');
        }

        $style->writeln('');
    }

    /**
     * Render the command listing / help screen.
     */
    private function showHelp(): void
    {
        $style = new ConsoleStyle();

        $style->writeln(Banner::header(\App\Core\Application\App::VERSION));
        $style->writeln('');

        $style->writeln('<fg=yellow>Usage:</>');
        $style->writeln('  <fg=white>php zero &lt;command&gt; [options]</>');
        $style->writeln('');

        $style->writeln('<fg=yellow>Available Commands</>');
        $style->writeln('<fg=gray>' . str_repeat('─', 60) . '</>');

        foreach ($this->commandInfo() as $group => $commands) {
            $style->writeln('');
            $style->writeln('  <options=bold;fg=yellow>' . $group . '</>');

            foreach ($commands as $name => $info) {
                $description = is_array($info) ? ($info['description'] ?? '') : $info;
                $padded = str_pad($name, 22);
                $style->writeln('    <fg=green>' . $padded . '</> <fg=gray>' . $description . '</>');
            }
        }

        if (class_exists(\Zeroping\Support\Console\CommandRegistry::class)) {
            $packageCommands = \Zeroping\Support\Console\CommandRegistry::all();

            if ($packageCommands !== []) {
                $style->writeln('');
                $style->writeln('  <options=bold;fg=yellow>Package Commands</>');

                foreach ($packageCommands as $name => $class) {
                    $instance   = new $class();
                    $description = method_exists($instance, 'getDescription')
                        ? $instance->getDescription()
                        : '';
                    $padded = str_pad($name, 22);
                    $style->writeln('    <fg=green>' . $padded . '</> <fg=gray>' . $description . '</>');
                }
            }
        }

        $style->writeln('');
        $style->writeln('<fg=yellow>Global Options</>');
        $style->writeln('  <fg=green>--help</>         <fg=gray>Show this help screen or command-specific help</>');
        $style->writeln('  <fg=green>--force</>        <fg=gray>Overwrite existing files when generating</>');
        $style->writeln('  <fg=green>--class=</>       <fg=gray>Target a specific class (db:seed)</>');
        $style->writeln('  <fg=green>--feature</>      <fg=gray>Create a feature test (make:test)</>');
        $style->writeln('  <fg=green>--connection=</>  <fg=gray>Queue connection (queue:work, queue:listen)</>');
        $style->writeln('  <fg=green>--queue=</>       <fg=gray>Queue name (queue:work, queue:listen)</>');
        $style->writeln('  <fg=green>--delay=</>       <fg=gray>Delay before retry in seconds (queue:work)</>');
        $style->writeln('  <fg=green>--sleep=</>       <fg=gray>Sleep between jobs in seconds (queue)</>');
        $style->writeln('  <fg=green>--tries=</>       <fg=gray>Max attempts before failing (queue)</>');
        $style->writeln('  <fg=green>--group=</>       <fg=gray>Publish group (publish)</>');
        $style->writeln('');
        $style->writeln('<fg=gray>Run</> <fg=cyan>php zero &lt;command&gt; --help</> <fg=gray>for details on any command.</>');
        $style->writeln('');
    }
}
