<?php
declare(strict_types=1);

namespace App\Core\Validation\Rules;

interface Rule
{
    public function validate(
        string $field,
        mixed $value,
        array $data = [],
        array $parameters = []
    ): bool;

    public function message(
        string $field,
        array $parameters = []
    ): string;
}
