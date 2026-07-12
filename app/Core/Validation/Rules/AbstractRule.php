<?php

namespace App\Core\Validation\Rules;

abstract class AbstractRule implements Rule
{
    protected function isEmpty(mixed $value): bool
    {
        return $value === null
            || $value === ''
            || (is_array($value) && empty($value));
    }

    protected function length(mixed $value): int
    {
        return mb_strlen((string) $value);
    }

    protected function parameter(
        array $parameters,
        int $index,
        mixed $default = null
    ): mixed {
        return $parameters[$index] ?? $default;
    }
}
