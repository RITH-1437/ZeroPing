<?php

declare(strict_types=1);

namespace App\Core\Scheduling;

/**
 * Trait providing fluent cron expression building methods.
 *
 * Used by scheduled task classes to define execution frequency
 * without manually writing cron expressions. Methods return `$this`
 * for fluent chaining.
 *
 * @example
 * ```php
 * $schedule->command('cache:clear')
 *     ->dailyAt('03:00')
 *     ->weekdays();
 * ```
 *
 * @property string $expression The cron expression string.
 */
trait Frequency
{
    /**
     * The cron expression representing the task's frequency.
     *
     * @var string
     */
    public string $expression = '* * * * *';

    /**
     * Set a custom cron expression.
     *
     * @param string $expression A valid 5-field cron expression.
     * @return static
     */
    public function cron(string $expression): static
    {
        $this->expression = $expression;

        return $this;
    }

    /**
     * Run the task every minute.
     *
     * @return static
     */
    public function everyMinute(): static
    {
        return $this->cron('* * * * *');
    }

    /**
     * Run the task every two minutes.
     *
     * @return static
     */
    public function everyTwoMinutes(): static
    {
        return $this->cron('*/2 * * * *');
    }

    /**
     * Run the task every five minutes.
     *
     * @return static
     */
    public function everyFiveMinutes(): static
    {
        return $this->cron('*/5 * * * *');
    }

    /**
     * Run the task every ten minutes.
     *
     * @return static
     */
    public function everyTenMinutes(): static
    {
        return $this->cron('*/10 * * * *');
    }

    /**
     * Run the task every fifteen minutes.
     *
     * @return static
     */
    public function everyFifteenMinutes(): static
    {
        return $this->cron('*/15 * * * *');
    }

    /**
     * Run the task every thirty minutes.
     *
     * @return static
     */
    public function everyThirtyMinutes(): static
    {
        return $this->cron('*/30 * * * *');
    }

    /**
     * Run the task every hour at minute 0.
     *
     * @return static
     */
    public function hourly(): static
    {
        return $this->cron('0 * * * *');
    }

    /**
     * Run the task every hour at a specific minute.
     *
     * @param int $minute The minute of the hour (0-59).
     * @return static
     */
    public function hourlyAt(int $minute): static
    {
        $minute = max(0, min(59, $minute));

        return $this->cron("{$minute} * * * *");
    }

    /**
     * Run the task every day at midnight (00:00).
     *
     * @return static
     */
    public function daily(): static
    {
        return $this->cron('0 0 * * *');
    }

    /**
     * Run the task daily at a specific time.
     *
     * @param string $time The time in "HH:MM" format (24-hour).
     * @return static
     *
     * @throws \InvalidArgumentException If the time format is invalid.
     */
    public function dailyAt(string $time): static
    {
        $parts = explode(':', $time);

        if (count($parts) !== 2) {
            throw new \InvalidArgumentException(
                sprintf('Invalid time format "%s". Expected "HH:MM".', $time)
            );
        }

        [$hour, $minute] = $parts;
        $hour = (int) $hour;
        $minute = (int) $minute;

        return $this->cron("{$minute} {$hour} * * *");
    }

    /**
     * Run the task twice daily at the given hours.
     *
     * @param int $hour1 The first hour (0-23). Default: 1.
     * @param int $hour2 The second hour (0-23). Default: 13.
     * @return static
     */
    public function twiceDaily(int $hour1 = 1, int $hour2 = 13): static
    {
        $hour1 = max(0, min(23, $hour1));
        $hour2 = max(0, min(23, $hour2));

        return $this->cron("0 {$hour1},{$hour2} * * *");
    }

    /**
     * Run the task every week on Sunday at midnight.
     *
     * @return static
     */
    public function weekly(): static
    {
        return $this->cron('0 0 * * 0');
    }

    /**
     * Run the task weekly on a specific day and time.
     *
     * @param int $dayOfWeek The day of week (0=Sunday, 6=Saturday).
     * @param string $time The time in "HH:MM" format.
     * @return static
     */
    public function weeklyOn(int $dayOfWeek, string $time = '00:00'): static
    {
        $dayOfWeek = max(0, min(6, $dayOfWeek));
        $parts = explode(':', $time);
        $hour = (int) ($parts[0] ?? 0);
        $minute = (int) ($parts[1] ?? 0);

        return $this->cron("{$minute} {$hour} * * {$dayOfWeek}");
    }

    /**
     * Run the task on the first day of every month at midnight.
     *
     * @return static
     */
    public function monthly(): static
    {
        return $this->cron('0 0 1 * *');
    }

    /**
     * Run the task monthly on a specific day and time.
     *
     * @param int $dayOfMonth The day of the month (1-31).
     * @param string $time The time in "HH:MM" format.
     * @return static
     */
    public function monthlyOn(int $dayOfMonth = 1, string $time = '00:00'): static
    {
        $dayOfMonth = max(1, min(31, $dayOfMonth));
        $parts = explode(':', $time);
        $hour = (int) ($parts[0] ?? 0);
        $minute = (int) ($parts[1] ?? 0);

        return $this->cron("{$minute} {$hour} {$dayOfMonth} * *");
    }

    /**
     * Run the task on January 1st at midnight each year.
     *
     * @return static
     */
    public function yearly(): static
    {
        return $this->cron('0 0 1 1 *');
    }

    /**
     * Restrict the task to only run on weekdays (Monday–Friday).
     *
     * @return static
     */
    public function weekdays(): static
    {
        // Append weekday restriction to existing expression
        $parts = explode(' ', $this->expression);
        $parts[4] = '1-5';
        $this->expression = implode(' ', $parts);

        return $this;
    }

    /**
     * Restrict the task to only run on weekends (Saturday–Sunday).
     *
     * @return static
     */
    public function weekends(): static
    {
        $parts = explode(' ', $this->expression);
        $parts[4] = '0,6';
        $this->expression = implode(' ', $parts);

        return $this;
    }

    /**
     * Restrict the task to specific days of the week.
     *
     * @param int|array<int, int> ...$days Days of the week (0=Sunday, 6=Saturday).
     * @return static
     */
    public function days(int|array ...$days): static
    {
        $allDays = [];
        foreach ($days as $day) {
            if (is_array($day)) {
                $allDays = array_merge($allDays, $day);
            } else {
                $allDays[] = $day;
            }
        }

        $parts = explode(' ', $this->expression);
        $parts[4] = implode(',', array_unique($allDays));
        $this->expression = implode(' ', $parts);

        return $this;
    }
}
