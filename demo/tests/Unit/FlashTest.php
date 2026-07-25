<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Core\Session\Flash;

class FlashTest extends \Tests\TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION = [];
    }

    public function testSetAndGetFlashMessage(): void
    {
        Flash::set('success', 'It worked!');

        $flash = Flash::get();

        $this->assertSame(['type' => 'success', 'message' => 'It worked!'], $flash);
    }

    public function testGetClearsFlashData(): void
    {
        Flash::set('info', 'Notice');

        Flash::get();

        $this->assertNull(Flash::get());
    }

    public function testHasReturnsTrueWhenFlashExists(): void
    {
        Flash::set('error', 'Failed');

        $this->assertTrue(Flash::has());
    }

    public function testHasReturnsFalseWhenNoFlash(): void
    {
        $this->assertFalse(Flash::has());
    }

    public function testSuccessSetsSuccessType(): void
    {
        Flash::success('Saved');

        $flash = Flash::get();

        $this->assertSame('success', $flash['type']);
        $this->assertSame('Saved', $flash['message']);
    }

    public function testErrorSetsErrorType(): void
    {
        Flash::error('Something broke');

        $flash = Flash::get();

        $this->assertSame('error', $flash['type']);
        $this->assertSame('Something broke', $flash['message']);
    }

    public function testWarningSetsWarningType(): void
    {
        Flash::warning('Careful');

        $flash = Flash::get();

        $this->assertSame('warning', $flash['type']);
        $this->assertSame('Careful', $flash['message']);
    }

    public function testInfoSetsInfoType(): void
    {
        Flash::info('FYI');

        $flash = Flash::get();

        $this->assertSame('info', $flash['type']);
        $this->assertSame('FYI', $flash['message']);
    }

    public function testFlashOverwritesPreviousFlash(): void
    {
        Flash::success('First');
        Flash::error('Second');

        $flash = Flash::get();

        $this->assertSame('error', $flash['type']);
        $this->assertSame('Second', $flash['message']);
    }
}
