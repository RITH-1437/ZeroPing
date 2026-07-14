<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Core\Http\ResponseFactory;
use App\Core\View\View;
use PHPUnit\Framework\TestCase;

class ResponseFactoryTest extends TestCase
{
    private string $tmp;

    protected function setUp(): void
    {
        $this->tmp = sys_get_temp_dir() . '/zp_views_' . uniqid();
        mkdir($this->tmp . '/views/layouts', 0777, true);

        file_put_contents($this->tmp . '/views/hello.php', 'Hi <?= $name ?>');
        file_put_contents($this->tmp . '/views/layouts/guest.php', 'LAYOUT{{ slot }}END');

        View::setBasePath($this->tmp);
    }

    protected function tearDown(): void
    {
        View::setBasePath(null);
    }

    public function testMakeReturnsResponseWithContentAndHeaders(): void
    {
        $response = (new ResponseFactory())->make('hello', 201, ['X-Foo' => 'bar']);

        $this->assertSame('hello', $response->content());
        $this->assertSame(201, $response->getStatusCode());
        $this->assertSame('bar', $response->getHeaders()['X-Foo']);
    }

    public function testJsonEncodesDataAndSetsContentType(): void
    {
        $response = (new ResponseFactory())->json(['a' => 1], 422);

        $this->assertSame(422, $response->getStatusCode());
        $this->assertJson((string) $response->content());
        $this->assertSame('application/json', $response->getHeaders()['Content-Type']);
        $this->assertSame(['a' => 1], json_decode((string) $response->content(), true));
    }

    public function testRedirectSetsLocationHeader(): void
    {
        $response = (new ResponseFactory())->redirect('/login', 301);

        $this->assertSame(301, $response->getStatusCode());
        $this->assertSame('/login', $response->getHeaders()['Location']);
    }

    public function testNoContentReturns204(): void
    {
        $response = (new ResponseFactory())->noContent();

        $this->assertSame(204, $response->getStatusCode());
    }

    public function testDownloadStreamsFileWithDisposition(): void
    {
        $file = $this->tmp . '/sample.txt';
        file_put_contents($file, 'file-body');

        $response = (new ResponseFactory())->download($file, 'renamed.txt');

        $this->assertSame('file-body', $response->content());
        $this->assertStringContainsString('attachment; filename="renamed.txt"', $response->getHeaders()['Content-Disposition']);
    }

    public function testViewRendersThroughViewLayer(): void
    {
        $response = (new ResponseFactory())->view('hello', ['name' => 'Ada']);

        $this->assertStringContainsString('Hi Ada', (string) $response->content());
        $this->assertSame('text/html; charset=utf-8', $response->getHeaders()['Content-Type']);
    }

    public function testResponseHelperBuildsResponse(): void
    {
        $response = response('body', 200);

        $this->assertInstanceOf(\App\Core\Http\Response::class, $response);
        $this->assertSame('body', $response->content());
    }

    public function testRedirectHelperReturnsResponse(): void
    {
        $response = redirect('/home', 302);

        $this->assertSame('/home', $response->getHeaders()['Location']);
        $this->assertSame(302, $response->getStatusCode());
    }
}
