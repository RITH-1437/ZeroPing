<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Core\Application\App;
use App\Core\Localization\Translator;
use PHPUnit\Framework\TestCase;

class LocalizationTest extends TestCase
{
    private string $tmp;

    protected function setUp(): void
    {
        $this->tmp = sys_get_temp_dir() . '/zp_lang_' . uniqid();
        mkdir($this->tmp . '/en', 0777, true);
        mkdir($this->tmp . '/es', 0777, true);

        file_put_contents($this->tmp . '/en/messages.php', "<?php return ['welcome' => 'Welcome, :name', 'nested' => ['a' => 'A']];");
        file_put_contents($this->tmp . '/es/messages.php', "<?php return ['welcome' => 'Hola, :name'];");
        file_put_contents($this->tmp . '/en/auth.php', "<?php return ['failed' => 'Bad credentials'];");
    }

    private function translator(): Translator
    {
        return new Translator($this->tmp, 'en', 'en');
    }

    public function testTranslatesWithPlaceholder(): void
    {
        $this->assertSame('Welcome, Ada', $this->translator()->get('messages.welcome', ['name' => 'Ada']));
    }

    public function testNestedKeys(): void
    {
        $this->assertSame('A', $this->translator()->get('messages.nested.a'));
    }

    public function testFallbackToConfiguredLocale(): void
    {
        $t = new Translator($this->tmp, 'es', 'en');

        // 'auth.failed' exists only in 'en' (the fallback).
        $this->assertSame('Bad credentials', $t->get('auth.failed'));
    }

    public function testMissingKeyReturnsKey(): void
    {
        $this->assertSame('missing.key', $this->translator()->get('missing.key'));
    }

    public function testHas(): void
    {
        $t = $this->translator();
        $this->assertTrue($t->has('messages.welcome'));
        $this->assertFalse($t->has('messages.nope'));
    }

    public function testSetLocaleSwitchesLanguage(): void
    {
        $t = $this->translator();
        $t->setLocale('es');

        $this->assertSame('Hola, Ada', $t->get('messages.welcome', ['name' => 'Ada']));
    }

    public function testTransHelperUsesBoundTranslator(): void
    {
        App::container()->singleton(
            Translator::class,
            fn () => new Translator($this->tmp, 'en', 'en')
        );

        $this->assertSame('Welcome, Ada', trans('messages.welcome', ['name' => 'Ada']));
        $this->assertSame('Welcome, Ada', __('messages.welcome', ['name' => 'Ada']));
    }
}
