<?php

declare(strict_types=1);

namespace Tests\Unit;

use FrameworkSite\WebsiteController;
use PHPUnit\Framework\TestCase;

/**
 * Tests for the five ecosystem hub pages introduced in WebsiteController.
 *
 * Each method is expected to return a non-empty HTML string containing
 * the page's title, headline, and key phrases rendered through the hub
 * view template.
 */
class EcosystemPagesTest extends TestCase
{
    // -------------------------------------------------------------------------
    // showcase()
    // -------------------------------------------------------------------------

    public function testShowcasePageReturnsNonEmptyHtml(): void
    {
        $_SERVER['REQUEST_URI'] = '/showcase';
        $html = (new WebsiteController())->showcase();

        $this->assertIsString($html);
        $this->assertNotEmpty($html);
    }

    public function testShowcasePageContainsExpectedContent(): void
    {
        $_SERVER['REQUEST_URI'] = '/showcase';
        $html = (new WebsiteController())->showcase();

        $this->assertStringContainsString('Showcase', $html);
        $this->assertStringContainsString('Built with ZeroPing.', $html);
        $this->assertStringContainsString('Community projects', $html);
        $this->assertStringContainsString('ZeroPing Framework Site', $html);
        $this->assertStringContainsString('Templates you can deploy today', $html);
        $this->assertStringContainsString('MVC Starter', $html);
    }

    // -------------------------------------------------------------------------
    // deploy()
    // -------------------------------------------------------------------------

    public function testDeployPageReturnsNonEmptyHtml(): void
    {
        $_SERVER['REQUEST_URI'] = '/deploy';
        $html = (new WebsiteController())->deploy();

        $this->assertIsString($html);
        $this->assertNotEmpty($html);
    }

    public function testDeployPageContainsExpectedContent(): void
    {
        $_SERVER['REQUEST_URI'] = '/deploy';
        $html = (new WebsiteController())->deploy();

        $this->assertStringContainsString('ZeroPing Deploy', $html);
        $this->assertStringContainsString('One command. Live in seconds.', $html);
        $this->assertStringContainsString('Planned capabilities', $html);
        $this->assertStringContainsString('Zero-downtime deploys', $html);
        $this->assertStringContainsString('Migration automation', $html);
        $this->assertStringContainsString('Production checklist', $html);
    }

    // -------------------------------------------------------------------------
    // studio()
    // -------------------------------------------------------------------------

    public function testStudioPageReturnsNonEmptyHtml(): void
    {
        $_SERVER['REQUEST_URI'] = '/studio';
        $html = (new WebsiteController())->studio();

        $this->assertIsString($html);
        $this->assertNotEmpty($html);
    }

    public function testStudioPageContainsExpectedContent(): void
    {
        $_SERVER['REQUEST_URI'] = '/studio';
        $html = (new WebsiteController())->studio();

        $this->assertStringContainsString('ZeroPing Studio', $html);
        $this->assertStringContainsString('Visual tools for ZeroPing applications.', $html);
        $this->assertStringContainsString('Planned panels', $html);
        $this->assertStringContainsString('Model browser', $html);
        $this->assertStringContainsString('Queue monitor', $html);
        $this->assertStringContainsString('Debug Toolbar', $html);
    }

    // -------------------------------------------------------------------------
    // cloud()
    // -------------------------------------------------------------------------

    public function testCloudPageReturnsNonEmptyHtml(): void
    {
        $_SERVER['REQUEST_URI'] = '/cloud';
        $html = (new WebsiteController())->cloud();

        $this->assertIsString($html);
        $this->assertNotEmpty($html);
    }

    public function testCloudPageContainsExpectedContent(): void
    {
        $_SERVER['REQUEST_URI'] = '/cloud';
        $html = (new WebsiteController())->cloud();

        $this->assertStringContainsString('ZeroPing Cloud', $html);
        $this->assertStringContainsString('Managed hosting for PHP applications.', $html);
        $this->assertStringContainsString('What Cloud will offer', $html);
        $this->assertStringContainsString('Git-based deployments', $html);
        $this->assertStringContainsString('Managed databases', $html);
        $this->assertStringContainsString('Docker-ready', $html);
    }

    // -------------------------------------------------------------------------
    // forge()
    // -------------------------------------------------------------------------

    public function testForgePageReturnsNonEmptyHtml(): void
    {
        $_SERVER['REQUEST_URI'] = '/forge';
        $html = (new WebsiteController())->forge();

        $this->assertIsString($html);
        $this->assertNotEmpty($html);
    }

    public function testForgePageContainsExpectedContent(): void
    {
        $_SERVER['REQUEST_URI'] = '/forge';
        $html = (new WebsiteController())->forge();

        $this->assertStringContainsString('ZeroPing Forge', $html);
        $this->assertStringContainsString('Server provisioning and site management.', $html);
        $this->assertStringContainsString('Planned features', $html);
        $this->assertStringContainsString('One-click server provisioning', $html);
        $this->assertStringContainsString('SSL &amp; domain management', $html);
        $this->assertStringContainsString('Docker deployment', $html);
    }

    // -------------------------------------------------------------------------
    // Shared hub structure
    // -------------------------------------------------------------------------

    public function testAllEcosystemPagesShareHubStructure(): void
    {
        $controller = new WebsiteController();
        $pages = [
            '/showcase' => $controller->showcase(),
            '/deploy'   => $controller->deploy(),
            '/studio'   => $controller->studio(),
            '/cloud'    => $controller->cloud(),
            '/forge'    => $controller->forge(),
        ];

        foreach ($pages as $uri => $html) {
            $this->assertIsString($html, "Page $uri did not return a string.");
            $this->assertGreaterThan(0, strlen($html), "Page $uri returned empty HTML.");
            // All hub pages share the same base layout which includes navigation.
            $this->assertStringContainsString('ZeroPing', $html, "Page $uri missing ZeroPing branding.");
        }
    }
}
