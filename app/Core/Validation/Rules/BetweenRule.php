<?php

namespace App\Core\Validation\Rules;

class BetweenRule extends AbstractRule
{
    public function validate(
        string $field,
        mixed $value,
        array $data = [],
        array $parameters = []
    ): bool {

        $min = (int) $this->parameter($parameters, 0, 0);
        $max = (int) $this->parameter($parameters, 1, PHP_INT_MAX);

        if (is_numeric($value)) {
            return $value >= $min && $value <= $max;
        }

        if (is_array($value)) {
            $count = count($value);
            return $count >= $min && $count <= $max;
        }

        $length = $this->length($value);

        return $length >= $min && $length <= $max;
    }

    public function message(
        string $field,
        array $parameters = []
    ): string {

        return "{$field} must be between {$parameters[0]} and {$parameters[1]}.";
    }
}