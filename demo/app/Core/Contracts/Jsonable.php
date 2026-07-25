<?php

namespace App\Core\Contracts;

interface Jsonable
{
    public function toJson(int $options = 0): string;
}
