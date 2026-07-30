<?php

declare(strict_types=1);

namespace Tests\Unit;

use FrameworkSite\ArenaCatalog;
use FrameworkSite\WebsiteController;
use PHPUnit\Framework\TestCase;

class ArenaPageTest extends TestCase
{
    public function testArenaCatalogContainsRequestedPlatformSurfaces(): void
    {
        $catalog = (new ArenaCatalog())->all();

        $this->assertCount(17, $catalog['ecosystem']);
        $this->assertCount(12, $catalog['playground']);
        $this->assertCount(14, $catalog['packages']);
        $this->assertCount(5, $catalog['roadmap']);
        $this->assertCount(6, $catalog['stats']);
    }

    public function testArenaPageRendersPremiumPortalAndSpecificAssets(): void
    {
        $_SERVER['REQUEST_URI'] = '/arena';
        $html = (new WebsiteController())->arena();

        $this->assertStringContainsString('Build Faster.', $html);
        $this->assertStringContainsString('A playground for serious work.', $html);
        $this->assertStringContainsString('Official packages', $html);
        $this->assertStringContainsString('ZeroPing CLI examples', $html);
        $this->assertStringContainsString('Product roadmap', $html);
        $this->assertStringContainsString('/assets/css/arena.css', $html);
        $this->assertStringContainsString('/assets/js/arena.js', $html);
        $this->assertStringContainsString('arena-terminal-footer', $html);
        $this->assertStringContainsString('Skip to content', $html);
        $this->assertSame(2, substr_count($html, '<details class="nav-menu" data-nav-dropdown>'));
        $this->assertStringContainsString('hoverNavigation', $html);
        $this->assertStringContainsString("event.key !== 'Escape'", $html);
    }

    public function testTerminalFooterIsSharedAcrossFrameworkSite(): void
    {
        $_SERVER['REQUEST_URI'] = '/';
        $html = (new WebsiteController())->home();

        $this->assertStringContainsString('arena-terminal-footer', $html);
        $this->assertStringContainsString('/assets/css/terminal-footer.css', $html);
        $this->assertStringContainsString('zero about', $html);
        $this->assertStringNotContainsString('Made with', $html);
        $this->assertStringNotContainsString('Cambodia', $html);
    }

}