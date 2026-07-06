<?php

namespace App\Core\Validation\Rules;

class NullableRule implements Rule
{
    public function validate(
        string $field,
        mixed $value,
        array $data = [],
        array $parameters = []
    ): bool {

        return true;
    }

    public function message(
        string $field,
        array $parameters = []
    ): string {

        return '';
    }
}