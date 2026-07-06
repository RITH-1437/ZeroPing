<?php

namespace App\Core\Validation\Rules;

class IntegerRule implements Rule
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

        return filter_var($value, FILTER_VALIDATE_INT) !== false;
    }

    public function message(
        string $field,
        array $parameters = []
    ): string {

        return "{$field} must be an integer.";
    }
}