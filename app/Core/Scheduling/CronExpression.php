<?php

namespace App\Core\Scheduling;

class CronExpression
{
    protected array $fields;

    public function __construct(
        protected string $expression
    ) {
        $this->fields = $this->parse($expression);
    }

    public function isDue(\DateTimeInterface $date = null): bool
    {
        $date = $date ?? new \DateTime();

        return $this->matches($this->fields[0], (int) $date->format('i'))
            && $this->matches($this->fields[1], (int) $date->format('G'))
            && $this->matches($this->fields[2], (int) $date->format('j'))
            && $this->matches($this->fields[3], (int) $date->format('n'))
            && $this->matches($this->fields[4], (int) $date->format('w'));
    }

    protected function matches(array $field, int $value): bool
    {
        foreach ($field as $item) {
            if (is_array($item)) {
                if ($value >= $item[0] && $value <= $item[1]) {
                    return true;
                }
            } elseif ($value === $item) {
                return true;
            }
        }
        return false;
    }

    protected function parse(string $expression): array
    {
        $parts = preg_split('/\s+/', trim($expression));

        if (count($parts) !== 5) {
            throw new \InvalidArgumentException("Invalid cron expression: {$expression}");
        }

        return [
            $this->parseField($parts[0], 0),
            $this->parseField($parts[1], 1),
            $this->parseField($parts[2], 2),
            $this->parseField($parts[3], 3),
            $this->parseField($parts[4], 4),
        ];
    }

    protected function parseField(string $field, int $position): array
    {
        $result = [];

        foreach (explode(',', $field) as $part) {
            if (str_contains($part, '/')) {
                [$range, $step] = explode('/', $part, 2);
                $step = (int) $step;

                if ($range === '*') {
                    [$min, $max] = $this->rangeForPosition($position);
                } elseif (str_contains($range, '-')) {
                    [$min, $max] = $this->parseRange($range);
                } else {
                    $min = $max = (int) $range;
                }

                for ($i = $min; $i <= $max; $i += $step) {
                    $result[] = $i;
                }
            } elseif ($part === '*') {
                $result[] = $this->rangeForPosition($position);
            } elseif (str_contains($part, '-')) {
                $result[] = $this->parseRange($part);
            } else {
                $result[] = (int) $part;
            }
        }

        return $result;
    }

    protected function parseRange(string $range): array
    {
        $parts = explode('-', $range);
        return [(int) $parts[0], (int) $parts[1]];
    }

    protected function rangeForPosition(int $position): array
    {
        return match ($position) {
            0 => [0, 59],
            1 => [0, 23],
            2 => [1, 31],
            3 => [1, 12],
            4 => [0, 6],
            default => [0, 0],
        };
    }
}
