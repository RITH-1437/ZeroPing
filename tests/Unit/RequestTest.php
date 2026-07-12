<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Core\Http\Request;

class RequestTest extends \Tests\TestCase
{
    public function testMethodReturnsRequestMethod(): void
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';

        $this->assertSame('POST', Request::method());
    }

    public function testMethodReturnsGetAsDefault(): void
    {
        unset($_SERVER['REQUEST_METHOD']);

        $this->assertSame('GET', Request::method());
    }

    public function testMethodRespectsMethodOverride(): void
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST['_method'] = 'DELETE';

        $this->assertSame('DELETE', Request::method());
    }

    public function testUrlConstructsFullUrl(): void
    {
        $_SERVER['HTTP_HOST'] = 'example.com';
        $_SERVER['REQUEST_URI'] = '/users?page=1';
        unset($_SERVER['HTTPS']);

        $this->assertSame('http://example.com/users?page=1', Request::url());
    }

    public function testUrlReturnsHttpsWhenSecure(): void
    {
        $_SERVER['HTTPS'] = 'on';
        $_SERVER['HTTP_HOST'] = 'example.com';
        $_SERVER['REQUEST_URI'] = '/secure';

        $this->assertSame('https://example.com/secure', Request::url());
    }

    public function testPathReturnsUriPath(): void
    {
        $_SERVER['REQUEST_URI'] = '/users/42?tab=settings';

        $this->assertSame('/users/42', Request::path());
    }

    public function testPathReturnsSlashForRoot(): void
    {
        $_SERVER['REQUEST_URI'] = '/';

        $this->assertSame('/', Request::path());
    }

    public function testIpReturnsXForwardedFor(): void
    {
        $_SERVER['HTTP_X_FORWARDED_FOR'] = '1.2.3.4';

        $this->assertSame('1.2.3.4', Request::ip());
    }

    public function testIpReturnsClientIp(): void
    {
        unset($_SERVER['HTTP_X_FORWARDED_FOR']);
        $_SERVER['HTTP_CLIENT_IP'] = '5.6.7.8';

        $this->assertSame('5.6.7.8', Request::ip());
    }

    public function testIpReturnsRemoteAddr(): void
    {
        unset($_SERVER['HTTP_X_FORWARDED_FOR'], $_SERVER['HTTP_CLIENT_IP']);
        $_SERVER['REMOTE_ADDR'] = '9.10.11.12';

        $this->assertSame('9.10.11.12', Request::ip());
    }

    public function testIpReturnsLocalhostAsFallback(): void
    {
        unset($_SERVER['HTTP_X_FORWARDED_FOR'], $_SERVER['HTTP_CLIENT_IP'], $_SERVER['REMOTE_ADDR']);

        $this->assertSame('127.0.0.1', Request::ip());
    }

    public function testIsMatchesExactPath(): void
    {
        $_SERVER['REQUEST_URI'] = '/users';

        $this->assertTrue(Request::is('users'));
    }

    public function testIsMatchesWildcardPattern(): void
    {
        $_SERVER['REQUEST_URI'] = '/users/42';

        $this->assertTrue(Request::is('users/*'));
    }

    public function testIsReturnsFalseForNonMatchingPattern(): void
    {
        $_SERVER['REQUEST_URI'] = '/posts';

        $this->assertFalse(Request::is('users/*'));
    }

    public function testInputReturnsPostValue(): void
    {
        $_POST['name'] = 'John';

        $this->assertSame('John', Request::input('name'));
    }

    public function testInputReturnsGetValue(): void
    {
        unset($_POST['page']);
        $_GET['page'] = '5';

        $this->assertSame('5', Request::input('page'));
    }

    public function testInputReturnsDefaultForMissingKey(): void
    {
        $this->assertSame('fallback', Request::input('missing', 'fallback'));
    }

    public function testInputReturnsNullForMissingKey(): void
    {
        $this->assertNull(Request::input('missing'));
    }

    public function testAllMergesGetAndPost(): void
    {
        $_GET = ['page' => '1'];
        $_POST = ['name' => 'Test'];

        $all = Request::all();

        $this->assertSame('1', $all['page']);
        $this->assertSame('Test', $all['name']);
    }

    public function testHasReturnsTrueForPostKey(): void
    {
        $_POST['email'] = 'test@example.com';

        $this->assertTrue(Request::has('email'));
    }

    public function testHasReturnsTrueForGetKey(): void
    {
        $_GET['q'] = 'search';

        $this->assertTrue(Request::has('q'));
    }

    public function testHasReturnsFalseForMissingKey(): void
    {
        $this->assertFalse(Request::has('nonexistent'));
    }

    public function testOnlyReturnsSelectedKeys(): void
    {
        $_GET = ['page' => '1'];
        $_POST = ['name' => 'John', 'email' => 'j@test.com'];

        $result = Request::only(['name', 'page']);

        $this->assertArrayHasKey('name', $result);
        $this->assertArrayHasKey('page', $result);
        $this->assertCount(2, $result);
        $this->assertSame('John', $result['name']);
        $this->assertSame('1', $result['page']);
    }

    public function testExceptExcludesKeys(): void
    {
        $_GET = ['page' => '1'];
        $_POST = ['name' => 'John', 'email' => 'j@test.com'];

        $result = Request::except(['email']);

        $this->assertArrayHasKey('name', $result);
        $this->assertArrayHasKey('page', $result);
        $this->assertArrayNotHasKey('email', $result);
    }

    public function testIsJsonReturnsTrueForJsonContentType(): void
    {
        $_SERVER['CONTENT_TYPE'] = 'application/json';

        $this->assertTrue(Request::isJson());
    }

    public function testIsJsonReturnsFalseForFormContentType(): void
    {
        $_SERVER['CONTENT_TYPE'] = 'application/x-www-form-urlencoded';

        $this->assertFalse(Request::isJson());
    }

    public function testHeaderReturnsServerValue(): void
    {
        $_SERVER['HTTP_X_CUSTOM'] = 'test-value';

        $this->assertSame('test-value', Request::header('X-Custom'));
    }

    public function testHeaderReturnsDefaultForMissingHeader(): void
    {
        $this->assertSame('default', Request::header('Missing', 'default'));
    }

    public function testHeaderReturnsNullForMissingHeader(): void
    {
        $this->assertNull(Request::header('Missing'));
    }

    public function testCaptureCreatesRequestFromGlobals(): void
    {
        $_GET = ['a' => 1];
        $_POST = ['b' => 2];
        $_SERVER['REQUEST_METHOD'] = 'GET';

        $request = Request::capture();

        $this->assertInstanceOf(Request::class, $request);
    }

    public function testInstanceGetReturnsPostValue(): void
    {
        $request = new Request([], ['name' => 'John'], ['REQUEST_METHOD' => 'POST']);

        $this->assertSame('John', $request->get('name'));
    }

    public function testInstanceGetReturnsQueryValue(): void
    {
        $request = new Request(['page' => '2'], [], ['REQUEST_METHOD' => 'GET']);

        $this->assertSame('2', $request->get('page'));
    }

    public function testInstanceGetReturnsDefault(): void
    {
        $request = new Request([], [], ['REQUEST_METHOD' => 'GET']);

        $this->assertSame('none', $request->get('missing', 'none'));
    }

    public function testInstanceGetMethodReturnsRequestMethod(): void
    {
        $request = new Request([], [], ['REQUEST_METHOD' => 'POST']);

        $this->assertSame('POST', $request->getMethod());
    }

    public function testInstanceGetHeaderReturnsParsedHeader(): void
    {
        $request = new Request([], [], [
            'HTTP_AUTHORIZATION' => 'Bearer token123',
            'REQUEST_METHOD' => 'GET',
        ]);

        $this->assertSame('Bearer token123', $request->getHeader('authorization'));
    }

    public function testInstanceGetHeaderReturnsDefault(): void
    {
        $request = new Request([], [], ['REQUEST_METHOD' => 'GET']);

        $this->assertSame('fallback', $request->getHeader('x-missing', 'fallback'));
    }
}
