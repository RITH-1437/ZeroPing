<?php

namespace App\Core\Validation\Rules;

class InRule extends AbstractRule
{
    public function validate(string $field, mixed $value, array $data = [], array $parameters = []): bool
    {
        if ($this->isEmpty($value)) {
            return true;
        }

        return in_array((string) $value, $parameters, true);
    }

    public function message(string $field, array $parameters = []): string
    {
        $allowed = implode(', ', $parameters);
        return "{$field} must be one of: {$allowed}.";
    }
}
