<?php

namespace App\Core\Validation\Rules;

class ArrayRule extends AbstractRule
{
    public function validate(string $field, mixed $value, array $data = [], array $parameters = []): bool
    {
        if ($this->isEmpty($value)) {
            return true;
        }

        return is_array($value);
    }

    public function message(string $field, array $parameters = []): string
    {
        return "{$field} must be an array.";
    }
}
