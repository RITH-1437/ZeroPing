<?php

declare(strict_types=1);

namespace App\Core\Console;

/**
 * The main console application kernel.
 *
 * Parses CLI arguments and dispatches commands via the {@see CommandRegistry}.
 * This replaces the previous monolithic switch statement with a registry-based
 * dispatch mechanism while maintaining full backward compatibility.
 *
 * @package App\Core\Console
 */
class Console
{
    /**
     * The command registry instance.
     */
    private CommandRegistry $registry;

    /**
     * Create a new Console instance.
     */
    public function __construct(?CommandRegistry $registry = null)
    {
        $this->registry = $registry ?? new CommandRegistry();

        // Register commands that don't extend Command (special cases)
        $this->registry->register('new', Commands\NewCommand::class);
    }

    /**
     * Parse CLI arguments and dispatch the appropriate command handler.
     *
     * @param array<int, string> $argv The CLI argument vector ($_SERVER['argv']).
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

        // Handle built-in meta commands
        switch ($command) {
            case 'version':
                echo "ZeroPing Framework v" . \App\Core\Application\App::VERSION . "\n";
                return;

            case 'help':
                $target = $argv[2] ?? null;
                if ($target !== null) {
                    $this->showCommandHelp($target);
                } else {
                    $this->showHelp();
                }
                return;

            case 'list':
                $this->showHelp();
                return;
        }

        // Dispatch via registry
        $this->dispatch($command, $argv);
    }

    /**
     * Dispatch a command by signature, resolving it from the registry.
     *
     * @param string $command The command signature (e.g. "make:model").
     * @param array<int, string> $argv The full argument vector.
     */
    private function dispatch(string $command, array $argv): void
    {
        $class = $this->registry->resolve($command);

        if ($class === null) {
            // Try package-level registry
            $class = $this->registry->resolveFromPackages($command);
        }

        if ($class === null) {
            $style = new ConsoleStyle();
            $style->writeln("<fg=red>Command '{$command}' not found. Run <fg=white>php zero help</> for a list.</>");
            return;
        }

        $instance = new $class();
        $args = array_slice($argv, 2);

        // Handle instances that extend Command
        if ($instance instanceof Command) {
            $this->invokeCommand($instance, $args);
            return;
        }

        // Handle standalone command classes (e.g. NewCommand) via reflection
        $this->invokeStandaloneCommand($instance, $args);
    }

    /**
     * Invoke a standalone command object (not extending Command base class).
     *
     * @param object $instance The command instance.
     * @param array<int, string> $args The remaining CLI arguments.
     */
    private function invokeStandaloneCommand(object $instance, array $args): void
    {
        if (!method_exists($instance, 'handle')) {
            return;
        }

        $reflection = new \ReflectionMethod($instance, 'handle');
        $params = $reflection->getParameters();

        if (count($params) === 0) {
            $instance->handle();
            return;
        }

        $firstParam = $params[0];
        $type = $firstParam->getType();

        if ($type instanceof \ReflectionNamedType && $type->getName() === 'array') {
            $instance->handle($args);
            return;
        }

        if ($type instanceof \ReflectionNamedType && $type->getName() === 'string') {
            $firstArg = $args[0] ?? '';

            if (count($params) >= 2) {
                $secondParam = $params[1];
                $secondType = $secondParam->getType();
                if ($secondType instanceof \ReflectionNamedType && $secondType->getName() === 'array') {
                    $instance->handle($firstArg, array_slice($args, 1));
                    return;
                }
            }

            $instance->handle($firstArg);
            return;
        }

        $instance->handle();
    }

    /**
     * Invoke a command's handle() method with the appropriate arguments.
     *
     * Inspects the handle() signature to determine what arguments to pass,
     * maintaining backward compatibility with existing command classes that
     * expect different parameter shapes.
     *
     * @param Command $instance The command instance to invoke.
     * @param array<int, string> $args The remaining CLI arguments (after the command name).
     */
    private function invokeCommand(Command $instance, array $args): void
    {
        $reflection = new \ReflectionMethod($instance, 'handle');
        $params = $reflection->getParameters();

        if (count($params) === 0) {
            $instance->handle();
            return;
        }

        $firstParam = $params[0];
        $type = $firstParam->getType();

        // handle(array $args) — pass the full args array
        if ($type instanceof \ReflectionNamedType && $type->getName() === 'array') {
            $instance->handle($args);
            return;
        }

        // handle(string $name) — pass first argument
        if ($type instanceof \ReflectionNamedType && $type->getName() === 'string') {
            $firstArg = $args[0] ?? '';

            // Check if there are two params: handle(string $name, array $rest)
            if (count($params) >= 2) {
                $secondParam = $params[1];
                $secondType = $secondParam->getType();
                if ($secondType instanceof \ReflectionNamedType && $secondType->getName() === 'array') {
                    $instance->handle($firstArg, array_slice($args, 1));
                    return;
                }
            }

            $instance->handle($firstArg);
            return;
        }

        // Fallback: call with no args
        $instance->handle();
    }

    /**
     * Get the command registry instance.
     */
    public function getRegistry(): CommandRegistry
    {
        return $this->registry;
    }


    /**
     * Single source of truth for the command listing and per-command help.
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
                    'examples' => ['php zero serve', 'php zero serve 1437'],
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
                'make:resource' => [
                    'description' => 'Create a new API resource class',
                    'options' => $force,
                    'arguments' => [['name' => 'name', 'description' => 'Resource class name']],
                    'examples' => ['php zero make:resource UserResource'],
                    'notes' => 'Generates a JSON resource in app/Http/Resources.',
                ],
                'make:rule' => [
                    'description' => 'Create a new custom validation rule',
                    'options' => $force,
                    'arguments' => [['name' => 'name', 'description' => 'Rule class name']],
                    'examples' => ['php zero make:rule UppercaseRule'],
                    'notes' => 'Generates a validation rule in app/Core/Validation/Rules.',
                ],
                'make:scope' => [
                    'description' => 'Create a new global query scope',
                    'options' => $force,
                    'arguments' => [['name' => 'name', 'description' => 'Scope class name']],
                    'examples' => ['php zero make:scope ActiveScope'],
                    'notes' => 'Generates a global scope in app/Core/ORM/Scopes.',
                ],
                'make:exception' => [
                    'description' => 'Create a new custom exception class',
                    'options' => $force,
                    'arguments' => [['name' => 'name', 'description' => 'Exception class name']],
                    'examples' => ['php zero make:exception PaymentException'],
                    'notes' => 'Generates a custom exception in app/Exceptions.',
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
     * Find command metadata by name from the commandInfo registry.
     *
     * @return array{description: string, options: array<string,string>, arguments: array<int,array{name:string,description:string}>, examples: string[], notes: string}|null
     */
    private function findCommand(string $name): ?array
    {
        foreach ($this->commandInfo() as $commands) {
            if (array_key_exists($name, $commands)) {
                return $commands[$name];
            }
        }

        // Check package-level registry for help metadata
        $class = $this->registry->resolveFromPackages($name);

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

        $packageCommands = $this->registry->allFromPackages();

        if ($packageCommands !== []) {
            $style->writeln('');
            $style->writeln('  <options=bold;fg=yellow>Package Commands</>');

            foreach ($packageCommands as $name => $class) {
                $instance = new $class();
                $description = method_exists($instance, 'getDescription')
                    ? $instance->getDescription()
                    : '';
                $padded = str_pad($name, 22);
                $style->writeln('    <fg=green>' . $padded . '</> <fg=gray>' . $description . '</>');
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
