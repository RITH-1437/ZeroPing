# CLI Reference

ZeroPing ships with a powerful CLI (`php zero`) for managing your application.

## General Commands

| Command | Description |
|---|---|
| `php zero` | Display the command list |
| `php zero about` | Display framework information |
| `php zero version` | Show the framework version |
| `php zero serve` | Start the development server |

## Project Scaffolding

| Command | Description |
|---|---|
| `php zero new {type}` | Create a new project from a template |

Types: `empty`, `blog`, `api`, `mvc`

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

## Cache

| Command | Description |
|---|---|
| `php zero cache:clear` | Flush the application cache |
| `php zero view:cache` | Cache compiled view files |
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
| `php zero test` | Run framework tests |
| `php zero orm:test` | Test ORM |
| `php zero cache:test` | Test cache system |
| `php zero mail:test` | Test mail system |
| `php zero queue:test` | Test queue system |
| `php zero schedule:test` | Test scheduler |
| `php zero security:test` | Test security layer |
| `php zero storage:test` | Test storage system |
| `php zero log:test` | Test logger |
| `php zero config:test` | Test config system |
| `php zero validate:test` | Test validator |
