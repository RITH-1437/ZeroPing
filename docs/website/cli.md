# CLI Reference

ZeroPing ships with a powerful CLI (`php zero`) for managing your application.
Run `php zero` or `php zero help` to list every available command.

## General Commands

| Command | Description |
|---|---|
| `php zero` | Display the command list |
| `php zero about` | Display framework, PHP, and environment information |
| `php zero version` | Show the framework version |
| `php zero doctor` | Verify the installation (PHP, extensions, env, key, database) |
| `php zero serve [port]` | Start the development server (defaults to `1437`) |

## Project Scaffolding

| Command | Description |
|---|---|
| `php zero new {type}` | Create a new project from a template |

Types: `empty`, `blog`, `api`, `mvc`, `dashboard`

```bash
php zero new blog --name="My Blog" --dir=./my-blog
```

## Search

| Command | Description |
|---|---|
| `php zero search:index` | Build the documentation search index |

## Migrations

| Command | Description |
|---|---|
| `php zero migrate` | Run all pending migrations |
| `php zero migrate:fresh` | Drop all tables and re-run migrations |
| `php zero migrate:refresh` | Rollback all migrations then re-run them |
| `php zero migrate:rollback` | Rollback the last migration |
| `php zero migrate:reset` | Rollback all migrations |
| `php zero migrate:status` | Show migration status |

## Make (Code Generation)

| Command | Description |
|---|---|
| `php zero make:model {name}` | Create a new model |
| `php zero make:controller {name}` | Create a new controller |
| `php zero make:service {name}` | Create a new service class |
| `php zero make:repository {name}` | Create a new repository |
| `php zero make:migration {name}` | Create a new migration |
| `php zero make:mail {name}` | Create a new mailable class |
| `php zero make:seeder {name}` | Create a new database seeder |
| `php zero make:middleware {name}` | Create a new HTTP middleware |
| `php zero make:request {name}` | Create a new form request |
| `php zero make:policy {name}` | Create a new authorization policy |
| `php zero make:provider {name}` | Create a new service provider |
| `php zero make:command {name}` | Create a new console command |
| `php zero make:test {name}` | Create a unit test (`--feature` for a feature test) |

## Routes

| Command | Description |
|---|---|
| `php zero route:list` | Display all registered routes |
| `php zero route:cache` | Cache routes for faster resolution |
| `php zero route:clear` | Clear the route cache |

## Configuration

| Command | Description |
|---|---|
| `php zero config:cache` | Cache configuration files |
| `php zero config:clear` | Clear the configuration cache |

## Cache & Views

| Command | Description |
|---|---|
| `php zero cache:clear` | Flush the application cache |
| `php zero view:cache` | Cache compiled PHP view files |
| `php zero view:clear` | Clear compiled view files |

## Optimization

| Command | Description |
|---|---|
| `php zero optimize` | Cache config, routes, views, and search index |
| `php zero optimize:clear` | Clear all cached data |

## Queue

| Command | Description |
|---|---|
| `php zero queue:work` | Process jobs on the queue |
| `php zero queue:listen` | Listen to a queue |
| `php zero queue:failed` | List failed queue jobs |
| `php zero queue:retry {id}` | Retry a failed job |
| `php zero queue:clear` | Delete all jobs from the queue |
| `php zero queue:restart` | Restart queue workers |

## Schedule

| Command | Description |
|---|---|
| `php zero schedule:run` | Run scheduled commands |
| `php zero schedule:list` | List scheduled commands |
| `php zero schedule:clear` | Clear scheduled commands |

## Security

| Command | Description |
|---|---|
| `php zero key:generate` | Generate a new application key |
| `php zero security:test` | Run security diagnostics |

## Testing

| Command | Description |
|---|---|
| `php zero test` | Run the framework test suite |
| `php zero orm:test` | Test the ORM |
| `php zero cache:test` | Test the cache system |
| `php zero mail:test` | Test the mail system |
| `php zero queue:test` | Test the queue system |
| `php zero schedule:test` | Test the scheduler |
| `php zero security:test` | Test the security layer |
| `php zero storage:test` | Test the storage system |
| `php zero log:test` | Test the logger |
| `php zero config:test` | Test the config system |
| `php zero validate:test` | Test the validator |
