<?php

namespace App\Core\Validation\Rules;

use App\Core\Application\App;
use App\Core\Validation\DatabasePresenceVerifier;

class ExistsRule extends AbstractRule
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

        return $verifier->exists(
            $table,
            $column,
            $value
        );
    }

    public function message(
        string $field,
        array $parameters = []
    ): string {

        return "The selected {$field} is invalid.";
    }
}
