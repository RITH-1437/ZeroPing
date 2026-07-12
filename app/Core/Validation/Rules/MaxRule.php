<?php

namespace App\Core\Validation\Rules;

use App\Core\Validation\Rules\AbstractRule;

class MaxRule extends AbstractRule
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

        $max = (int) $this->parameter(
            $parameters,
            0,
            PHP_INT_MAX
        );

        if (is_numeric($value)) {
            return (float) $value <= $max;
        }

        return $this->length($value) <= $max;
    }

    public function message(
        string $field,
        array $parameters = []
    ): string {

        $max = $parameters[0] ?? 0;

        return "{$field} must not exceed {$max}.";
    }
}
