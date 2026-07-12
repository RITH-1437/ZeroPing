# Queues

A queued job is a class extending `App\Core\Queue\Job` with a `handle(): void` method. Jobs are pushed through the `dispatch()` helper or `App\Core\Queue\Dispatcher`, stored by a driver, and processed by a worker. Drivers: `sync` (default — runs inline), `database`, `array`, and `null`.

## Defining a job

```php
namespace App\Jobs;

use App\Core\Queue\Job;

class SendWelcomeEmail extends Job
{
    public string $queue = 'default';
    public int $tries = 3;
    public int $timeout = 60;

    public function __construct(public string $email) {}

    public function handle(): void
    {
        // deliver the email...
    }

    public function failed(\Throwable $e): void
    {
        // optional: log the failure
    }
}
```

## Dispatching

```php
use App\Core\Queue\Dispatcher;

dispatch(new SendWelcomeEmail('ada@example.com'));      // default connection
Dispatcher::dispatch(new SendWelcomeEmail('a@b.com'));
Dispatcher::dispatchLater(60, new SendWelcomeEmail('a@b.com')); // delay 60s
Dispatcher::dispatchSync(new SendWelcomeEmail('a@b.com'));     // run now
```

## Running the worker

For real background processing, switch the default connection to `database` in `config/queue.php` (which uses the `jobs` table — run `php zero migrate` to create it), then start a worker:

```bash
php zero queue:work --connection=database --queue=default --tries=3 --sleep=3
```

## Worker commands

| Command | Purpose |
|---|---|
| `php zero queue:work` | Run the daemon worker (options: `--connection`, `--queue`, `--delay`, `--sleep`, `--tries`). |
| `php zero queue:listen` | Listen continuously (same as work, no delay). |
| `php zero queue:failed` | List failed jobs (id, connection, queue, failed_at). |
| `php zero queue:retry <id>` | Retry a failed job by id. |
| `php zero queue:clear` | Delete all rows from the `jobs` table. |
| `php zero queue:restart` | Broadcast a restart signal to running workers. |

## Best Practices

Set `$tries` and a sensible `$timeout` on every job, and keep job constructors serializable (pass ids/primitives, not live resources). Use the `database` driver for anything that must survive a restart.

## Common Mistakes

Calling `$job->dispatch()`, `pushOn()`, or `withDelay()`. None exist — push via the `dispatch()` helper or `Dispatcher`. Delayed dispatch is `Dispatcher::dispatchLater($delay, $job)`. There is also no `queue:flush` command — use `queue:clear`.

## Notes

The default connection is `sync`, which executes `handle()` immediately and inline — nothing is queued. Failed jobs are recorded in the `failed_jobs` table (also created by `php zero migrate`).

## Tips

Run `php zero queue:work` under a process manager (Supervisor, systemd) in production so workers auto-restart.
