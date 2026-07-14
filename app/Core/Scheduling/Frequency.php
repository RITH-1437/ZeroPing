<?php

namespace App\Core\Scheduling;

trait Frequency
{
    public string $expression = '* * * * *';

    /**
     * Run the event every minute.
     *
     * @return self
     */
    public function everyMinute(): self
    {
        return $this->cron('* * * * *');
    }

    /**
     * Run the event every two minutes.
     *
     * @return self
     */
    public function everyTwoMinutes(): self
    {
        return $this->cron('*/2 * * * *');
    }

    /**
     * Run the event every five minutes.
     *
     * @return self
     */
    public function everyFiveMinutes(): self
    {
        return $this->cron('*/5 * * * *');
    }

    /**
     * Run the event every ten minutes.
     *
     * @return self
     */
    public function everyTenMinutes(): self
    {
        return $this->cron('*/10 * * * *');
    }

    /**
     * Run the event every thirty minutes.
     *
     * @return self
     */
    public function everyThirtyMinutes(): self
    {
        return $this->cron('*/30 * * * *');
    }

    /**
     * Run the event every hour.
     *
     * @return self
     */
    public function hourly(): self
    {
        return $this->cron('0 * * * *');
    }

    /**
     * Run the event at a specific minute of each hour.
     *
     * @param int $minute
     * @return self
     */
    public function hourlyAt(int $minute): self
    {
        return $this->cron("{$minute} * * * *");
    }

    /**
     * Run the event every day at midnight.
     *
     * @return self
     */
    public function daily(): self
    {
        return $this->cron('0 0 * * *');
    }

    /**
     * Run the event daily at a given time.
     *
     * @param string $time
     * @return self
     */
    public function dailyAt(string $time): self
    {
        [$hour, $minute] = explode(':', $time);

        return $this->cron("{$minute} {$hour} * * *");
    }

    /**
     * Run the event twice daily.
     *
     * @param int $hour1
     * @param int $hour2
     * @return self
     */
    public function twiceDaily(int $hour1 = 1, int $hour2 = 13): self
    {
        return $this->cron("0 {$hour1},{$hour2} * * *");
    }

    /**
     * Run the event every week.
     *
     * @return self
     */
    public function weekly(): self
    {
        return $this->cron('0 0 * * 0');
    }

    /**
     * Run the event every month.
     *
     * @return self
     */
    public function monthly(): self
    {
        return $this->cron('0 0 1 * *');
    }

    /**
     * Run the event every year.
     *
     * @return self
     */
    public function yearly(): self
    {
        return $this->cron('0 0 1 1 *');
    }

    /**
     * Set a custom cron expression.
     *
     * @param string $expression
     * @return self
     */
    public function cron(string $expression): self
    {
        $this->expression = $expression;

        return $this;
    }
}
