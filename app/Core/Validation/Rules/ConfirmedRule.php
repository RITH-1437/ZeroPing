<?php

namespace App\Core\Validation\Rules;

class ConfirmedRule extends AbstractRule
{
    public function validate(
        string $field,
        mixed $value,
        array $data = [],
        array $parameters = []
    ): bool {

        return ($data[$field . '_confirmation'] ?? null) === $value;
    }

    public function message(
        string $field,
        array $parameters = []
    ): string {

        return "{$field} confirmation does not match.";
    }
}
