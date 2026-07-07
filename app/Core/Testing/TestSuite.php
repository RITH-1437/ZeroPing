<?php

namespace App\Core\Testing;

class TestSuite
{
    protected array $tests = [];

    public function add(TestCase $test): void
    {
        $this->tests[] = $test;
    }

    public function tests(): array
    {
        return $this->tests;
    }
}
