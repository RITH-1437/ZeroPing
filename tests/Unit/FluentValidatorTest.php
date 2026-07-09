<?php

namespace Tests\Unit;

use App\Core\Testing\TestCase;
use App\Core\Validation\FluentValidator;

class FluentValidatorTest extends TestCase
{
    public function test_passes_with_valid_data()
    {
        $v = FluentValidator::make([
            'name' => 'John',
            'email' => 'john@example.com',
        ])
            ->field('name')->required()->string()->min(2)->end()
            ->field('email')->required()->email()->end();

        $this->assertTrue($v->passes());
        $this->assertFalse($v->fails());
    }

    public function test_fails_with_invalid_data()
    {
        $v = FluentValidator::make([
            'name' => '',
            'email' => 'not-an-email',
        ])
            ->field('name')->required()->end()
            ->field('email')->email()->end();

        $this->assertTrue($v->fails());
        $this->assertFalse($v->passes());
    }

    public function test_returns_errors_on_failure()
    {
        $v = FluentValidator::make([
            'name' => '',
        ])
            ->field('name')->required()->end();

        $v->validate();
        $errors = $v->errors();

        $this->assertNotEmpty($errors);
        $this->assertArrayHasKey('name', $errors);
    }

    public function test_fluent_in_rule()
    {
        $v = FluentValidator::make(['role' => 'admin'])
            ->field('role')->in(['admin', 'user'])->end();

        $this->assertTrue($v->passes());
    }

    public function test_fluent_not_in_rule()
    {
        $v = FluentValidator::make(['role' => 'banned'])
            ->field('role')->notIn(['banned', 'deleted'])->end();

        $this->assertTrue($v->fails());
    }

    public function test_fluent_regex_rule()
    {
        $v = FluentValidator::make(['code' => 'ABC123'])
            ->field('code')->regex('/^[A-Z0-9]+$/')->end();

        $this->assertTrue($v->passes());
    }

    public function test_fluent_between_rule()
    {
        $v = FluentValidator::make(['age' => 25])
            ->field('age')->between(18, 99)->end();

        $this->assertTrue($v->passes());
    }
}
