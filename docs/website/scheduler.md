# Task Scheduling

The scheduler is centered on `App\Core\Scheduling\ScheduleManager` (a container singleton). You define events on it: `call()` for closures, `command()` for shell commands, and `job()` for queued jobs. Each event is then given a frequency.

## Defining events

```php
use App\Core\Scheduling\ScheduleManager;

$manager = app(ScheduleManager::class);

// Closure every minute
$manager->call(function () {
    // ...
})->everyMinute();

// Shell command daily
$manager->command('cache:clear')->daily();

// Queued job hourly
$manager->job(new \App\Jobs\CleanupJob())->hourly()->withoutOverlapping();
```

## Frequencies

Chain one of these onto an event: `everyMinute()`, `everyTwoMinutes()`, `everyFiveMinutes()`, `everyTenMinutes()`, `everyThirtyMinutes()`, `hourly()`, `hourlyAt($minute)`, `daily()`, `dailyAt('13:30')`, `twiceDaily($h1, $h2)`, `weekly()`, `monthly()`, `yearly()`, `cron('0 0 * * 0')`.

## Constraints & conditions

Events also accept: `withoutOverlapping()`, `runInBackground()`, `environments(['production'])`, `when(fn)`, `skip(fn)`, `between($start, $end)`, `unlessBetween(...)`, `weekdays()`, `weekends()`, and `timezone($tz)`.

## Running the scheduler

ZeroPing does not run events on its own. Invoke the runner from your system cron (typically every minute):

```bash
* * * * * php /path/to/zero schedule:run >> /dev/null 2>&1
```

Other commands: `php zero schedule:list` (show defined events), `php zero schedule:test` (self-test), `php zero schedule:clear` (clear schedule cache).

## Best Practices

Define all schedules in a single service provider (resolve `ScheduleManager` from the container and register events there) so `schedule:run` finds them. Keep event callbacks short and idempotent.

## Common Mistakes

Expecting events to fire automatically, or calling `at('13:30')`. There is no `at()` — use `dailyAt('13:30')`. And because the runner must be triggered (e.g. by system cron calling `schedule:run`), nothing runs until you wire that up.

## Notes

Event *due-time* calculation and the `withoutOverlapping()` mutex are not yet implemented: `isDue()` currently returns `true`, so every event runs each time `schedule:run` is invoked. Gate expensive work with `when()`, or run `schedule:run` at the cadence you need, until that lands.

## Tips

Until exact-time firing lands, run `schedule:run` at the smallest granularity you care about (e.g. every minute) and protect heavy tasks with `when()`.
