<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Core\Config\Env;

class EnvTest extends \Tests\TestCase
{
    protected function tearDown(): void
    {
        unset($_ENV['TEST_KEY'], $_ENV['ANOTHER_KEY'], $_ENV['QUOTED_VALUE']);
        parent::tearDown();
    }

    public function testLoadThrowsWhenFileMissing(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('.env file not found.');

        Env::load('/nonexistent/path/.env');
    }

    public function testLoadParsesSimpleKeyValue(): void
    {
        $path = sys_get_temp_dir() . '/zero_env_test_' . uniqid() . '.env';
        file_put_contents($path, "TEST_KEY=hello\nANOTHER_KEY=world\n");

        Env::load($path);

        $this->assertSame('hello', $_ENV['TEST_KEY']);
        $this->assertSame('world', $_ENV['ANOTHER_KEY']);

        unlink($path);
    }

    public function testLoadSkipsComments(): void
    {
        $path = sys_get_temp_dir() . '/zero_env_test_' . uniqid() . '.env';
        file_put_contents($path, "# This is a comment\nTEST_KEY=value\n");

        Env::load($path);

        $this->assertSame('value', $_ENV['TEST_KEY']);
        $this->assertArrayNotHasKey('# This is a comment', $_ENV);

        unlink($path);
    }

    public function testLoadTrimsWhitespaceAndQuotes(): void
    {
        $path = sys_get_temp_dir() . '/zero_env_test_' . uniqid() . '.env';
        file_put_contents($path, "QUOTED_VALUE=\"quoted\"\nTEST_KEY=  spaced  \n");

        Env::load($path);

        $this->assertSame('"quoted"', $_ENV['QUOTED_VALUE']);
        $this->assertSame('spaced', $_ENV['TEST_KEY']);

        unlink($path);
    }

    public function testLoadSkipsEmptyLines(): void
    {
        $path = sys_get_temp_dir() . '/zero_env_test_' . uniqid() . '.env';
        file_put_contents($path, "TEST_KEY=value\n\n\nANOTHER_KEY=world\n");

        Env::load($path);

        $this->assertSame('value', $_ENV['TEST_KEY']);
        $this->assertSame('world', $_ENV['ANOTHER_KEY']);

        unlink($path);
    }
}
