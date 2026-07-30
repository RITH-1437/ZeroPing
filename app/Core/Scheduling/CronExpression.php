<?php

declare(strict_types=1);

namespace App\Core\Scheduling;

/**
 * Parses and evaluates standard 5-field cron expressions.
 *
 * Supports the following cron syntax:
 * - `*` — any value
 * - `1,2,3` — comma-separated list
 * - `1-5` — range
 * - `*\/5` — step values
 * - `1-10/2` — range with step
 *
 * Field positions:
 * 1. Minute (0-59)
 * 2. Hour (0-23)
 * 3. Day of month (1-31)
 * 4. Month (1-12)
 * 5. Day of week (0-6, Sunday=0)
 *
 * Edge cases handled:
 * - Validates field ranges against position constraints
 * - Handles step values that don't evenly divide the range
 * - Supports timezone-aware date comparisons
 * - Day-of-week wraps (7 is treated as 0/Sunday)
 *
 * @example
 * ```php
 * $cron = new CronExpression('0 9 * * 1-5'); // 9 AM weekdays
 * $cron->isDue(); // true if it's currently 9:xx on a weekday
 *
 * $cron = new CronExpression('*\/15 * * * *'); // Every 15 minutes
 * $cron->isDue(new \DateTime('2024-01-01 10:30:00')); // true
 * ```
 */
class CronExpression
{
    /**
     * The raw cron expression string.
     *
     * @var string
     */
    protected string $expression;

    /**
     * The parsed field values (arrays of integers or ranges).
     *
     * @var array<int, array<int, int>>
     */
    protected array $fields;

    /**
     * Valid ranges for each cron field position.
     *
     * @var array<int, array{0: int, 1: int}>
     */
    protected const FIELD_RANGES = [
        0 => [0, 59],   // Minute
        1 => [0, 23],   // Hour
        2 => [1, 31],   // Day of month
        3 => [1, 12],   // Month
        4 => [0, 6],    // Day of week (0=Sunday)
    ];

    /**
     * Create a new cron expression instance.
     *
     * @param string $expression A valid 5-field cron expression.
     *
     * @throws \InvalidArgumentException If the expression is not a valid 5-field cron expression.
     */
    public function __construct(string $expression)
    {
        $this->expression = $expression;
        $this->fields = $this->parse($expression);
    }

    /**
     * Determine if the cron expression is due at the given date/time.
     *
     * Checks all five fields (minute, hour, day of month, month, day of week)
     * against the given (or current) date/time. All fields must match for
     * the expression to be considered due.
     *
     * @param \DateTimeInterface|null $date The date to check against. Defaults to now.
     * @return bool True if the expression is due at the given time.
     */
    public function isDue(?\DateTimeInterface $date = null): bool
    {
        $date = $date ?? new \DateTimeImmutable();

        $minute = (int) $date->format('i');
        $hour = (int) $date->format('G');
        $dayOfMonth = (int) $date->format('j');
        $month = (int) $date->format('n');
        $dayOfWeek = (int) $date->format('w');

        return $this->fieldMatches($this->fields[0], $minute)
            && $this->fieldMatches($this->fields[1], $hour)
            && $this->fieldMatches($this->fields[2], $dayOfMonth)
            && $this->fieldMatches($this->fields[3], $month)
            && $this->fieldMatches($this->fields[4], $dayOfWeek);
    }

    /**
     * Get the raw cron expression string.
     *
     * @return string
     */
    public function getExpression(): string
    {
        return $this->expression;
    }

    /**
     * Check if a value matches a parsed cron field.
     *
     * @param array<int, int> $allowedValues The set of allowed integer values for the field.
     * @param int $value The value to check.
     * @return bool True if the value is in the allowed set.
     */
    protected function fieldMatches(array $allowedValues, int $value): bool
    {
        return in_array($value, $allowedValues, true);
    }

    /**
     * Parse a full 5-field cron expression into an array of allowed values per field.
     *
     * @param string $expression The cron expression to parse.
     * @return array<int, array<int, int>> An array of 5 elements, each containing allowed values.
     *
     * @throws \InvalidArgumentException If the expression does not have exactly 5 fields.
     */
    protected function parse(string $expression): array
    {
        $parts = preg_split('/\s+/', trim($expression));

        if ($parts === false || count($parts) !== 5) {
            throw new \InvalidArgumentException(
                sprintf('Invalid cron expression: "%s". Expected exactly 5 space-separated fields.', $expression)
            );
        }

        return [
            $this->parseField($parts[0], 0),
            $this->parseField($parts[1], 1),
            $this->parseField($parts[2], 2),
            $this->parseField($parts[3], 3),
            $this->parseField($parts[4], 4),
        ];
    }

    /**
     * Parse a single cron field into an array of allowed integer values.
     *
     * Handles wildcards (*), comma-separated lists (1,2,3), ranges (1-5),
     * step values (e.g. every 5th), and combined range with step (1-10/2).
     *
     * @param string $field The field string to parse.
     * @param int $position The field position (0-4) for range validation.
     * @return array<int, int> The sorted array of allowed values.
     *
     * @throws \InvalidArgumentException If the field syntax is invalid.
     */
    protected function parseField(string $field, int $position): array
    {
        [$min, $max] = self::FIELD_RANGES[$position];
        $result = [];

        // Handle comma-separated parts
        foreach (explode(',', $field) as $part) {
            $part = trim($part);

            if ($part === '') {
                throw new \InvalidArgumentException(
                    sprintf('Empty field segment in cron expression at position %d.', $position)
                );
            }

            if (str_contains($part, '/')) {
                // Step value: */5, 1-10/2, 0/5
                $result = array_merge($result, $this->parseStep($part, $position));
            } elseif ($part === '*') {
                // Wildcard: all values in range
                $result = array_merge($result, range($min, $max));
            } elseif (str_contains($part, '-')) {
                // Range: 1-5
                $result = array_merge($result, $this->parseRange($part, $position));
            } else {
                // Single value
                $value = $this->normalizeValue($part, $position);
                $this->validateValue($value, $position);
                $result[] = $value;
            }
        }

        // Remove duplicates, sort, and re-index
        $result = array_values(array_unique($result));
        sort($result);

        return $result;
    }

    /**
     * Parse a step expression (e.g., wildcard/5, 1-10/2, 0/15).
     *
     * Handles expressions like: wildcard with step, range with step,
     * and single value with step.
     *
     * @param string $part The step expression to parse.
     * @param int $position The field position for range validation.
     * @return array<int, int> Array of matching values.
     *
     * @throws \InvalidArgumentException If the step value is invalid.
     */
    protected function parseStep(string $part, int $position): array
    {
        [$rangePart, $stepPart] = explode('/', $part, 2);
        $step = (int) $stepPart;

        if ($step <= 0) {
            throw new \InvalidArgumentException(
                sprintf('Step value must be positive, got "%s" at position %d.', $stepPart, $position)
            );
        }

        [$min, $max] = self::FIELD_RANGES[$position];

        if ($rangePart === '*') {
            // */step — full range with step
            $rangeStart = $min;
            $rangeEnd = $max;
        } elseif (str_contains($rangePart, '-')) {
            // range/step — e.g., 1-30/5
            [$rangeStart, $rangeEnd] = $this->parseRangeBounds($rangePart, $position);
        } else {
            // single/step — e.g., 5/15 means starting at 5, stepping by 15
            $rangeStart = $this->normalizeValue($rangePart, $position);
            $rangeEnd = $max;
        }

        $result = [];
        for ($i = $rangeStart; $i <= $rangeEnd; $i += $step) {
            $result[] = $i;
        }

        return $result;
    }

    /**
     * Parse a range expression (e.g., "1-5") into an array of integers.
     *
     * @param string $part The range expression to parse.
     * @param int $position The field position for validation.
     * @return array<int, int> Array of values in the range.
     *
     * @throws \InvalidArgumentException If the range is invalid.
     */
    protected function parseRange(string $part, int $position): array
    {
        [$start, $end] = $this->parseRangeBounds($part, $position);

        // Handle wrap-around for day of week (e.g., 5-1 = Fri-Mon)
        if ($start > $end && $position === 4) {
            [$min, $max] = self::FIELD_RANGES[$position];
            return array_merge(range($start, $max), range($min, $end));
        }

        if ($start > $end) {
            throw new \InvalidArgumentException(
                sprintf(
                    'Invalid range "%s" at position %d: start (%d) is greater than end (%d).',
                    $part,
                    $position,
                    $start,
                    $end
                )
            );
        }

        return range($start, $end);
    }

    /**
     * Extract and validate the start and end bounds of a range expression.
     *
     * @param string $part The range string (e.g., "1-5").
     * @param int $position The field position for validation.
     * @return array{0: int, 1: int} The [start, end] bounds.
     *
     * @throws \InvalidArgumentException If the range syntax is invalid.
     */
    protected function parseRangeBounds(string $part, int $position): array
    {
        $segments = explode('-', $part, 2);

        if (count($segments) !== 2) {
            throw new \InvalidArgumentException(
                sprintf('Invalid range syntax "%s" at position %d.', $part, $position)
            );
        }

        $start = $this->normalizeValue($segments[0], $position);
        $end = $this->normalizeValue($segments[1], $position);

        $this->validateValue($start, $position);
        $this->validateValue($end, $position);

        return [$start, $end];
    }

    /**
     * Normalize a value, handling day-of-week 7 → 0 conversion.
     *
     * @param string $value The raw value string.
     * @param int $position The field position.
     * @return int The normalized integer value.
     */
    protected function normalizeValue(string $value, int $position): int
    {
        $intVal = (int) $value;

        // Day of week: 7 is treated as 0 (Sunday)
        if ($position === 4 && $intVal === 7) {
            return 0;
        }

        return $intVal;
    }

    /**
     * Validate that a value falls within the acceptable range for its position.
     *
     * @param int $value The value to validate.
     * @param int $position The field position.
     * @return void
     *
     * @throws \InvalidArgumentException If the value is out of range.
     */
    protected function validateValue(int $value, int $position): void
    {
        [$min, $max] = self::FIELD_RANGES[$position];

        if ($value < $min || $value > $max) {
            $fieldNames = ['minute', 'hour', 'day of month', 'month', 'day of week'];
            throw new \InvalidArgumentException(
                sprintf(
                    'Value %d is out of range for %s field (valid: %d-%d).',
                    $value,
                    $fieldNames[$position],
                    $min,
                    $max
                )
            );
        }
    }
}
