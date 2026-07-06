<?php

namespace App\Core\Validation\Rules;

use App\Core\Application\App;
use App\Core\Validation\DatabasePresenceVerifier;

class UniqueRule extends AbstractRule
{
    public function validate(
        string $field,
        mixed $value,
        array $data = [],
        array $parameters = []
    ): bool {

        if ($this->isEmpty($value)) {
            return true;
        }

        $table = $this->parameter($parameters, 0);

        $column = $this->parameter(
            $parameters,
            1,
            $field
        );

        if (!$table) {
            return false;
        }

        $verifier = App::container()->make(
            DatabasePresenceVerifier::class
        );

        return $verifier->unique(
            $table,
            $column,
            $value
        );
    }

    public function message(
        string $field,
        array $parameters = []
    ): string {

        return "{$field} has already been taken.";
    }
}