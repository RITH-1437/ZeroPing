<?php

namespace App\Core\Console\Commands;

use App\Core\Console\Command;
use App\Core\Scheduling\Schedule;

class ScheduleTestCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected string $signature = 'schedule:test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected string $description = 'Test the scheduler';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        $this->info('Testing scheduler...');

        $schedule = new Schedule();

        $schedule->command('cache:clear')->daily();
        $schedule->command('queue:work')->everyMinute();

        $this->assert(count($schedule->events()) === 2, 'schedule has 2 events');

        $this->info('Scheduler test completed successfully!');
    }

    protected function assert(bool $condition, string $test): void
    {
        if ($condition) {
            $this->info("✔ {$test}");
        } else {
            $this->error("✗ {$test}");
        }
    }
}
