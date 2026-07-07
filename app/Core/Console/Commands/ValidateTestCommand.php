<?php

namespace App\Core\Console\Commands;

use App\Core\Console\Command;
use App\Core\Support\Validator;

class ValidateTestCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected string $signature = 'validate:test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected string $description = 'Test the validator';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        $this->info('Testing validator...');

        $validator = new Validator();

        $data = [
            'name' => 'John Doe',
            'email' => 'john.doe@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'age' => 25,
            'website' => 'https://example.com',
        ];

        $rules = [
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
            'age' => 'required|numeric|min:18',
            'website' => 'nullable|url',
        ];

        $validator->validate($data, $rules);

        $this->assert($validator->passes(), 'validator passes');

        $this->info('Validator test completed successfully!');
    }

    protected function assert(bool $condition, string $test): void
    {
        if ($condition) {
            $this->info("✔ {$test}");
        } else {
            $this->error("✗ {$test}");
        }
    }
}
