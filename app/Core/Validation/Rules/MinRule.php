<?php

namespace App\Core\Validation\Rules;

class MinRule extends AbstractRule
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

        $min = (int) $this->parameter($parameters, 0, 0);

        return $this->length($value) >= $min;
    }

    public function message(
        string $field,
        array $parameters = []
    ): string {

        $min = $parameters[0] ?? 0;

        return "{$field} must be at least {$min} characters.";
    }
}