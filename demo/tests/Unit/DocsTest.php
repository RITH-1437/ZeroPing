<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Core\Docs\Docs;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class DocsTest extends TestCase
{
    public function testToHtmlRendersHeadings(): void
    {
        $html = (new Docs())->toHtml("# Title\n\n## Subtitle");

        $this->assertStringContainsString('<h1>Title</h1>', $html);
        $this->assertStringContainsString('<h2>Subtitle</h2>', $html);
    }

    public function testToHtmlRendersInlineFormatting(): void
    {
        $html = (new Docs())->toHtml("A **bold** word and `code` and [link](https://example.com).");

        $this->assertStringContainsString('<strong>bold</strong>', $html);
        $this->assertStringContainsString('<code>code</code>', $html);
        $this->assertStringContainsString('<a href="https://example.com">link</a>', $html);
    }

    public function testToHtmlRendersLists(): void
    {
        $html = (new Docs())->toHtml("- one\n- two");

        $this->assertStringContainsString('<ul>', $html);
        $this->assertStringContainsString('<li>one</li>', $html);
        $this->assertStringContainsString('<li>two</li>', $html);
        $this->assertStringContainsString('</ul>', $html);
    }

    public function testToHtmlRendersCodeFences(): void
    {
        $html = (new Docs())->toHtml("```php\n echo 1;\n```");

        $this->assertStringContainsString('<pre><code>', $html);
        $this->assertStringContainsString('echo 1;', $html);
    }

    public function testRenderThrowsForMissingPage(): void
    {
        $this->expectException(InvalidArgumentException::class);

        (new Docs(sys_get_temp_dir()))->render('does-not-exist');
    }

    public function testRenderLoadsRealDocumentation(): void
    {
        $docs = new Docs(dirname(__DIR__, 2) . '/resources/docs');

        $html = $docs->render('introduction');

        $this->assertStringContainsString('<h1>ZeroPing</h1>', $html);
        $this->assertStringContainsString('<a href="installation">Installation</a>', $html);
    }
}
