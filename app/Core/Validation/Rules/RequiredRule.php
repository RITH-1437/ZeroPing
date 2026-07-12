<?php

namespace App\Core\Validation\Rules;

class RequiredRule extends AbstractRule
{
    public function validate(
        string $field,
        mixed $value,
        array $data = [],
        array $parameters = []
    ): bool {

        return !$this->isEmpty($value);
    }

    public function message(
        string $field,
        array $parameters = []
    ): string {

        return "{$field} is required.";
    }
}
