<?php

namespace App\Core\Console\Commands;

use App\Core\Console\Command;
use App\Core\Security\CSRF;
use App\Core\Security\Encryption;
use App\Core\Security\Hash;
use App\Core\Security\RateLimiter;
use App\Core\Security\Signature;

class SecurityTestCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected string $signature = 'security:test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected string $description = 'Test the security components';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        $this->info('Testing security components...');

        $this->testCsrf();
        $this->testHashing();
        $this->testEncryption();
        $this->testRateLimiter();
        $this->testSignedUrls();

        $this->info('Security components test completed successfully!');
    }

    protected function testCsrf(): void
    {
        $token = CSRF::generate();
        $this->assert(CSRF::check($token), 'CSRF');
    }

    protected function testHashing(): void
    {
        $hash = Hash::make('password');
        $this->assert(Hash::check('password', $hash), 'Hashing');
    }

    protected function testEncryption(): void
    {
        $encrypted = Encryption::encrypt('hello world');
        $this->assert(Encryption::decrypt($encrypted) === 'hello world', 'Encryption');
    }

    protected function testRateLimiter(): void
    {
        $this->assert(RateLimiter::attempt('test', 5), 'Rate Limiter');
    }

    protected function testSignedUrls(): void
    {
        $url = 'http://localhost/test';
        $signedUrl = Signature::sign($url);
        $this->assert(Signature::validate($signedUrl), 'Signed URLs');
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
