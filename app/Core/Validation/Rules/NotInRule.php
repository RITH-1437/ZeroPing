<?php

namespace App\Core\Validation\Rules;

class NotInRule extends AbstractRule
{
    public function validate(string $field, mixed $value, array $data = [], array $parameters = []): bool
    {
        if ($this->isEmpty($value)) {
            return true;
        }

        return !in_array((string) $value, $parameters, true);
    }

    public function message(string $field, array $parameters = []): string
    {
        $disallowed = implode(', ', $parameters);
        return "{$field} must not be one of: {$disallowed}.";
    }
}
