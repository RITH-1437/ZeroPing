<?php

namespace App\Core\Validation\Rules;

class StringRule implements Rule
{
    public function validate(
        string $field,
        mixed $value,
        array $data = [],
        array $parameters = []
    ): bool {

        if ($value === null) {
            return true;
        }

        return is_string($value);
    }

    public function message(
        string $field,
        array $parameters = []
    ): string {

        return "{$field} must be a string.";
    }
}
