<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Core\View\Controller;
use App\Core\Security\CSRF;
use App\Core\Http\Request;
use App\Core\Session\Flash;

class ControllerTest extends \Tests\TestCase
{
    private Controller $controller;

    protected function setUp(): void
    {
        parent::setUp();
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION = [];
        $this->controller = new class extends Controller {};
    }

    private function callProtected(string $method, array $args = [])
    {
        $ref = new \ReflectionMethod(Controller::class, $method);
        return $ref->invokeArgs($this->controller, $args);
    }

    public function testValidateCsrfReturnsTrueForValidToken(): void
    {
        $token = CSRF::generate();
        Request::capture();
        $_POST['_token'] = $token;

        $result = $this->callProtected('validateCsrf');

        $this->assertTrue($result);
    }

    public function testValidateCsrfReturnsFalseForMissingToken(): void
    {
        $_POST['_token'] = '';

        $result = $this->callProtected('validateCsrf');

        $this->assertFalse($result);
    }

    public function testValidateCsrfReturnsFalseForInvalidToken(): void
    {
        $_POST['_token'] = 'invalid-token';

        $result = $this->callProtected('validateCsrf');

        $this->assertFalse($result);
    }

    public function testValidateCsrfSetsFlashErrorOnInvalid(): void
    {
        $_POST['_token'] = 'invalid-token';

        $result = $this->callProtected('validateCsrf');

        $this->assertFalse($result);
        $this->assertTrue(Flash::has());
        $flash = Flash::get();
        $this->assertSame('error', $flash['type']);
    }

    public function testViewCallsViewRender(): void
    {
        $base = sys_get_temp_dir() . '/zero_view_test';
        if (!is_dir($base . '/views/layouts')) {
            mkdir($base . '/views/layouts', 0777, true);
        }
        file_put_contents($base . '/views/test.php', '<?php echo "rendered"; ?>');
        file_put_contents($base . '/views/layouts/app.php', '<?php echo "{{ slot }}"; ?>');

        \App\Core\View\View::setBasePath(sys_get_temp_dir() . '/zero_view_test');
        \App\Core\View\View::enableCache(false);

        ob_start();
        $this->callProtected('view', ['test', [], 'app']);
        $output = ob_get_clean();

        $this->assertStringContainsString('rendered', $output);

        \App\Core\View\View::setBasePath(null);
    }
}
