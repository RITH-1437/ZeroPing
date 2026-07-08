<?php
$active = $active ?? '';
$links = [
    ['key' => 'home', 'label' => 'Home', 'href' => '/'],
    ['key' => 'features', 'label' => 'Features', 'href' => '/features'],
    ['key' => 'documentation', 'label' => 'Documentation', 'href' => '/docs/introduction'],
    ['key' => 'installation', 'label' => 'Installation', 'href' => '/installation'],
    ['key' => 'getting-started', 'label' => 'Getting Started', 'href' => '/getting-started'],
    ['key' => 'api', 'label' => 'API', 'href' => '/api'],
    ['key' => 'roadmap', 'label' => 'Roadmap', 'href' => '/roadmap'],
    ['key' => 'github', 'label' => 'GitHub', 'href' => '/github'],
];
?>
<header class="fixed inset-x-0 top-0 z-50 border-b border-slate-200/85 bg-white/75 backdrop-blur-xl dark:border-slate-800/85 dark:bg-slate-950/75">
    <nav class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between gap-3" aria-label="Primary">
        <a href="/" class="inline-flex items-center gap-3 font-semibold text-slate-900 dark:text-white focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 rounded-lg">
            <span class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-gradient-to-tr from-blue-600 to-emerald-500 text-white text-sm shadow-sm">ZP</span>
            <span class="hidden sm:inline">ZeroPing Framework</span>
            <span class="sm:hidden">ZeroPing</span>
        </a>

        <div class="hidden lg:flex items-center gap-1.5">
            <?php foreach ($links as $item): ?>
                <a
                    href="<?= htmlspecialchars($item['href'], ENT_QUOTES, 'UTF-8') ?>"
                    aria-current="<?= $active === $item['key'] ? 'page' : 'false' ?>"
                    class="px-3.5 py-2 rounded-lg text-sm font-medium focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 transition-colors <?= $active === $item['key'] ? 'bg-slate-900 text-white dark:bg-white dark:text-slate-950' : 'text-slate-700 hover:bg-slate-100 hover:text-slate-900 dark:text-slate-300 dark:hover:bg-slate-800 dark:hover:text-white' ?>"
                >
                    <?= htmlspecialchars($item['label'], ENT_QUOTES, 'UTF-8') ?>
                </a>
            <?php endforeach; ?>
        </div>

        <div class="flex items-center gap-2">
            <button
                type="button"
                data-theme-toggle
                class="relative inline-flex h-9 w-9 items-center justify-center rounded-lg border border-slate-300 text-slate-700 hover:bg-slate-100 dark:border-slate-700 dark:text-slate-200 dark:hover:bg-slate-800 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500"
                aria-label="Toggle theme"
                title="Toggle theme"
            >
                <svg data-theme-icon-sun xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="h-4.5 w-4.5 dark:hidden" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <circle cx="12" cy="12" r="4"></circle>
                    <path d="M12 2v2"></path>
                    <path d="M12 20v2"></path>
                    <path d="m4.93 4.93 1.41 1.41"></path>
                    <path d="m17.66 17.66 1.41 1.41"></path>
                    <path d="M2 12h2"></path>
                    <path d="M20 12h2"></path>
                    <path d="m6.34 17.66-1.41 1.41"></path>
                    <path d="m19.07 4.93-1.41 1.41"></path>
                </svg>
                <svg data-theme-icon-moon xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="hidden h-4.5 w-4.5 dark:block" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M12 3a6 6 0 0 0 9 9 9 9 0 1 1-9-9z"></path>
                </svg>
            </button>

            <button
                type="button"
                data-mobile-toggle
                class="lg:hidden inline-flex h-9 w-9 items-center justify-center rounded-lg border border-slate-300 text-slate-700 hover:bg-slate-100 dark:border-slate-700 dark:text-slate-200 dark:hover:bg-slate-800 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500"
                aria-controls="mobile-nav"
                aria-expanded="false"
                aria-label="Open navigation menu"
            >
                <svg data-menu-open xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <line x1="4" x2="20" y1="12" y2="12"></line>
                    <line x1="4" x2="20" y1="6" y2="6"></line>
                    <line x1="4" x2="20" y1="18" y2="18"></line>
                </svg>
                <svg data-menu-close xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="hidden h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <line x1="18" x2="6" y1="6" y2="18"></line>
                    <line x1="6" x2="18" y1="6" y2="18"></line>
                </svg>
            </button>
        </div>
    </nav>

    <div id="mobile-nav" data-mobile-menu class="hidden border-t border-slate-200 bg-white/95 px-4 py-3 dark:border-slate-800 dark:bg-slate-950/95 lg:hidden">
        <div class="mx-auto max-w-7xl grid gap-1.5">
            <?php foreach ($links as $item): ?>
                <a
                    href="<?= htmlspecialchars($item['href'], ENT_QUOTES, 'UTF-8') ?>"
                    aria-current="<?= $active === $item['key'] ? 'page' : 'false' ?>"
                    class="rounded-lg px-3 py-2.5 text-sm font-medium focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 transition-colors <?= $active === $item['key'] ? 'bg-slate-900 text-white dark:bg-white dark:text-slate-950' : 'text-slate-700 hover:bg-slate-100 dark:text-slate-300 dark:hover:bg-slate-800' ?>"
                >
                    <?= htmlspecialchars($item['label'], ENT_QUOTES, 'UTF-8') ?>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</header>
