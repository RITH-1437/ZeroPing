<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Core\Session\Flash;
use App\Core\Session\Session;
use PHPUnit\Framework\TestCase;

class SessionTest extends TestCase
{
    protected function setUp(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_save_path(sys_get_temp_dir());
        }

        Session::start();
        Session::destroy();
        Session::start();
    }

    protected function tearDown(): void
    {
        Session::destroy();
    }

    public function testSetGetHasRemove(): void
    {
        $this->assertFalse(Session::has('user'));

        Session::set('user', ['id' => 1]);

        $this->assertTrue(Session::has('user'));
        $this->assertSame(['id' => 1], Session::get('user'));
        $this->assertSame('def', Session::get('missing', 'def'));

        Session::remove('user');

        $this->assertFalse(Session::has('user'));
    }

    public function testFlashSetGetClears(): void
    {
        $this->assertFalse(Flash::has());

        Flash::set('success', 'Saved!');

        $this->assertTrue(Flash::has());
        $this->assertSame(
            ['type' => 'success', 'message' => 'Saved!'],
            Flash::get()
        );

        // Reading the flash consumes it.
        $this->assertFalse(Flash::has());

        Flash::error('Oops');
        $this->assertSame('Oops', Flash::get()['message']);
    }
}
