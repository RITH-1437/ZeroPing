<?php

namespace App\Core\Debug;

interface Collector
{
    public function getName(): string;

    public function render(): string;
}
