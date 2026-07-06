<?php

namespace App\Core\Validation\Rules;

class SameRule extends AbstractRule
{
    public function validate(
        string $field,
        mixed $value,
        array $data = [],
        array $parameters = []
    ): bool {

        $other = $this->parameter($parameters, 0);

        if ($other === null) {
            return false;
        }

        return ($data[$other] ?? null) === $value;
    }

    public function message(
        string $field,
        array $parameters = []
    ): string {

        $other = $this->parameter($parameters, 0);

        return "{$field} must match {$other}.";
    }
}