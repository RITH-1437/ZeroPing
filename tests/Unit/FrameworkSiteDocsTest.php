<?php

declare(strict_types=1);

namespace Tests\Unit;

use FrameworkSite\DocsService;
use PHPUnit\Framework\TestCase;

class FrameworkSiteDocsTest extends TestCase
{
    public function testMarkdownRendererRejectsExecutableLinkSchemes(): void
    {
        $rendered = (new DocsService())->render('[unsafe](javascript:alert(1)) [safe](/docs/introduction)');

        $this->assertStringNotContainsString('javascript:', $rendered['html']);
        $this->assertStringContainsString('href="/docs/introduction"', $rendered['html']);
    }
}
