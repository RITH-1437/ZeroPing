<?php

namespace Tests\Unit;

use App\Core\Testing\TestCase;
use App\Core\Validation\Rules\ArrayRule;
use App\Core\Validation\Rules\FileRule;
use App\Core\Validation\Rules\ImageRule;
use App\Core\Validation\Rules\InRule;
use App\Core\Validation\Rules\NotInRule;
use App\Core\Validation\Rules\RegexRule;
use App\Core\Validation\Rules\SizeRule;

class ValidationRulesTest extends TestCase
{
    public function test_array_rule_passes_for_array()
    {
        $rule = new ArrayRule();
        $this->assertTrue($rule->validate('items', [1, 2, 3]));
    }

    public function test_array_rule_fails_for_string()
    {
        $rule = new ArrayRule();
        $this->assertFalse($rule->validate('items', 'not-array'));
    }

    public function test_array_rule_passes_for_empty()
    {
        $rule = new ArrayRule();
        $this->assertTrue($rule->validate('items', null));
    }

    public function test_file_rule_fails_for_string()
    {
        $rule = new FileRule();
        $this->assertFalse($rule->validate('file', 'string'));
    }

    public function test_file_rule_fails_for_missing_keys()
    {
        $rule = new FileRule();
        $this->assertFalse($rule->validate('file', ['name' => 'x.txt']));
    }

    public function test_file_rule_passes_for_empty()
    {
        $rule = new FileRule();
        $this->assertTrue($rule->validate('file', null));
    }

    public function test_in_rule_passes()
    {
        $rule = new InRule();
        $this->assertTrue($rule->validate('role', 'admin', [], ['admin', 'user']));
    }

    public function test_in_rule_fails()
    {
        $rule = new InRule();
        $this->assertFalse($rule->validate('role', 'superadmin', [], ['admin', 'user']));
    }

    public function test_in_rule_passes_for_empty()
    {
        $rule = new InRule();
        $this->assertTrue($rule->validate('role', null));
    }

    public function test_not_in_rule_passes()
    {
        $rule = new NotInRule();
        $this->assertTrue($rule->validate('role', 'editor', [], ['admin', 'user']));
    }

    public function test_not_in_rule_fails()
    {
        $rule = new NotInRule();
        $this->assertFalse($rule->validate('role', 'admin', [], ['admin', 'user']));
    }

    public function test_regex_rule_passes()
    {
        $rule = new RegexRule();
        $this->assertTrue($rule->validate('field', 'abc123', [], ['/^[a-z0-9]+$/']));
    }

    public function test_regex_rule_fails()
    {
        $rule = new RegexRule();
        $this->assertFalse($rule->validate('field', 'ABC!!!', [], ['/^[a-z0-9]+$/']));
    }

    public function test_regex_rule_passes_for_empty()
    {
        $rule = new RegexRule();
        $this->assertTrue($rule->validate('field', null));
    }

    public function test_size_rule_passes_for_string()
    {
        $rule = new SizeRule();
        $this->assertTrue($rule->validate('field', 'short', [], ['100']));
    }

    public function test_size_rule_passes_for_empty()
    {
        $rule = new SizeRule();
        $this->assertTrue($rule->validate('field', null));
    }
}
