<?php

namespace App\Core\Testing;

class TestRunner
{
    public function run(TestSuite $suite): void
    {
        foreach ($suite->tests() as $test) {
            $this->runTest($test);
        }
    }

    protected function runTest(TestCase $test): void
    {
        $reflection = new \ReflectionClass($test);
        $methods = $reflection->getMethods(\ReflectionMethod::IS_PUBLIC);

        foreach ($methods as $method) {
            if (str_starts_with($method->getName(), 'test')) {
                $test->{$method->getName()}();
            }
        }
    }
}
