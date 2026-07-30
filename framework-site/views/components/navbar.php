<?php
$currentPath = parse_url(($_SERVER['REQUEST_URI'] ?? '/'), PHP_URL_PATH) ?: '/';
$version = 'v' . \App\Core\Application\App::VERSION;
$githubUrl = 'https://github.com/RITH-1437/ZeroPing';
$isCurrent = static fn (array $paths): bool => in_array($currentPath, $paths, true);
?>
<header class="site-header sticky top-0 inset-x-0 z-50">
    <a class="skip-link" href="#main-content">Skip to content</a>
    <nav class="mx-auto flex h-[72px] max-w-7xl items-center justify-between gap-3 px-4 sm:px-6 lg:px-8" aria-label="Primary navigation">
        <a href="/" class="brand-mark shrink-0 focus-ring" aria-label="ZeroPing home">
            <img src="/assets/images/mascot.svg" alt="" width="32" height="32" class="h-8 w-8" fetchpriority="high">
            <span class="hidden sm:inline">ZeroPing</span>
            <span class="version-badge hidden md:inline-flex"><?= htmlspecialchars($version, ENT_QUOTES, 'UTF-8') ?></span>
        </a>

        <div class="hidden items-center gap-1 lg:flex">
            <a href="/" class="site-nav-link <?= $currentPath === '/' ? 'is-active' : '' ?>">Home</a>
            <a href="/arena" class="site-nav-link <?= $currentPath === '/arena' ? 'is-active' : '' ?>">Arena</a>
            <a href="/docs/introduction" class="site-nav-link <?= str_starts_with($currentPath, '/docs') ? 'is-active' : '' ?>">Docs</a>
            <a href="/features" class="site-nav-link <?= $currentPath === '/features' ? 'is-active' : '' ?>">Features</a>
            <a href="/api" class="site-nav-link <?= $currentPath === '/api' ? 'is-active' : '' ?>">API</a>
            <details class="nav-menu" data-nav-dropdown>
                <summary class="site-nav-link">Explore <svg class="h-3.5 w-3.5" aria-hidden="true" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="m6 9 6 6 6-6"/></svg></summary>
                <div class="nav-popover">
                    <a href="/getting-started"><strong>Getting started</strong><span>Build your first application</span></a>
                    <a href="/installation"><strong>Installation</strong><span>Choose your fastest setup</span></a>
                    <a href="/packages"><strong>Packages</strong><span>Extend the framework</span></a>
                    <a href="/examples"><strong>Examples</strong><span>Learn from working patterns</span></a>
                    <a href="/changelog"><strong>Changelog</strong><span>Track framework releases</span></a>
                </div>
            </details>
            <details class="nav-menu" data-nav-dropdown>
                <summary class="site-nav-link <?= $isCurrent(['/roadmap', '/community', '/sponsors', '/blog']) ? 'is-active' : '' ?>">Community <svg class="h-3.5 w-3.5" aria-hidden="true" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="m6 9 6 6 6-6"/></svg></summary>
                <div class="nav-popover nav-popover--right">
                    <a href="/roadmap"><strong>Roadmap</strong><span>See what is shipping next</span></a>
                    <a href="/community"><strong>Community</strong><span>Contribute and get support</span></a>
                    <a href="/sponsors"><strong>Sponsors</strong><span>Support sustainable maintenance</span></a>
                    <a href="/blog"><strong>Blog <em>Coming soon</em></strong><span>Engineering notes and release stories</span></a>
                </div>
            </details>
        </div>

        <div class="flex items-center gap-1.5 sm:gap-2">
            <button type="button" class="icon-button search-trigger" data-search-open aria-label="Search documentation" title="Search documentation (Ctrl+K)">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.2-5.2m2.2-5.3a7.5 7.5 0 1 1-15 0 7.5 7.5 0 0 1 15 0Z"/></svg>
                <kbd class="hidden xl:inline">⌘K</kbd>
            </button>
            <button type="button" class="icon-button" data-theme-toggle aria-label="Switch to dark mode" title="Switch color theme">
                <svg data-theme-icon-sun class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="12" cy="12" r="4"/><path stroke-linecap="round" d="M12 2v2m0 16v2M4.93 4.93l1.41 1.41m11.32 11.32 1.41 1.41M2 12h2m16 0h2M4.93 19.07l1.41-1.41M17.66 6.34l1.41-1.41"/></svg>
                <svg data-theme-icon-moon class="hidden h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M20.5 14.4A8.5 8.5 0 0 1 9.6 3.5 8.5 8.5 0 1 0 20.5 14.4Z"/></svg>
            </button>
            <a href="<?= $githubUrl ?>" target="_blank" rel="noopener noreferrer" class="github-button hidden sm:inline-flex focus-ring">
                <img src="/assets/images/github.png" alt="" class="h-4 w-4" width="16" height="16" loading="lazy"> <span>GitHub</span>
            </a>
            <button type="button" data-mobile-toggle aria-label="Open navigation menu" aria-expanded="false" class="icon-button lg:hidden">
                <svg data-menu-open class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" d="M4 7h16M4 12h16M4 17h16"/></svg>
                <svg data-menu-close class="hidden h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" d="m6 6 12 12M18 6 6 18"/></svg>
            </button>
        </div>
    </nav>

    <div data-mobile-menu class="mobile-navigation hidden lg:hidden">
        <div class="mx-auto grid max-w-7xl grid-cols-2 gap-1 px-4 py-4 sm:px-6" aria-label="Mobile navigation">
            <?php foreach ([
                ['Home', '/'], ['Arena', '/arena'], ['Docs', '/docs/introduction'], ['Features', '/features'], ['API', '/api'],
                ['Getting started', '/getting-started'], ['Installation', '/installation'], ['Packages', '/packages'], ['Examples', '/examples'],
                ['Changelog', '/changelog'], ['Roadmap', '/roadmap'], ['Community', '/community'], ['Sponsors', '/sponsors'],
            ] as [$label, $href]): ?>
                <a href="<?= htmlspecialchars($href, ENT_QUOTES, 'UTF-8') ?>" class="mobile-nav-link <?= $currentPath === $href || ($href === '/docs/introduction' && str_starts_with($currentPath, '/docs')) ? 'is-active' : '' ?>"><?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8') ?></a>
            <?php endforeach; ?>
        </div>
    </div>
</header>
