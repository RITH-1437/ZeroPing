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
<header class="fixed inset-x-0 top-0 z-50 border-b border-slate-200/50 bg-white/70 backdrop-blur-2xl dark:border-slate-800/50 dark:bg-slate-950/70">
    <nav class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between gap-3" aria-label="Primary">
        <a href="/" class="inline-flex items-center gap-3 font-semibold text-slate-900 dark:text-white focus-ring shrink-0">
            <img src="/assets/images/logo.png" alt="ZeroPing" class="h-8 w-8 rounded-lg object-contain ring-1 ring-slate-200 dark:ring-slate-700">
            <span class="hidden sm:inline">ZeroPing</span>
            <span class="sm:hidden">ZeroPing</span>
            <span class="hidden sm:inline-flex items-center rounded-full border border-slate-300/60 dark:border-slate-700/60 bg-slate-100/80 dark:bg-slate-800/80 px-2 py-0.5 text-[10px] font-medium text-slate-500 dark:text-slate-400">v1.0.0</span>
        </a>

        <div class="hidden lg:flex items-center gap-1">
            <?php foreach ($links as $item): ?>
                <a
                    href="<?= htmlspecialchars($item['href'], ENT_QUOTES, 'UTF-8') ?>"
                    aria-current="<?= $active === $item['key'] ? 'page' : 'false' ?>"
                    class="nav-link-underline px-3.5 py-2 rounded-lg text-sm font-medium focus-ring transition-all duration-200 <?= $active === $item['key']
                        ? 'bg-gradient-to-r from-blue-600 to-indigo-600 text-white shadow-lg shadow-blue-600/20 dark:shadow-blue-500/10 active'
                        : 'text-slate-600 hover:text-slate-900 dark:text-slate-400 dark:hover:text-white' ?>"
                >
                    <?= htmlspecialchars($item['label'], ENT_QUOTES, 'UTF-8') ?>
                </a>
            <?php endforeach; ?>
        </div>

        <div class="flex items-center gap-2">
            <a href="/github" class="hidden sm:inline-flex items-center gap-2 rounded-lg border border-slate-300 hover:bg-slate-100 dark:border-slate-700 dark:hover:bg-slate-800/60 px-3 py-1.5 text-sm font-medium text-slate-700 dark:text-slate-300 transition-all duration-200 focus-ring shrink-0">
                <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M12 0c-6.626 0-12 5.373-12 12 0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.73.083-.73 1.205.085 1.838 1.236 1.838 1.236 1.07 1.835 2.809 1.305 3.495.998.108-.776.417-1.305.76-1.605-2.665-.3-5.466-1.332-5.466-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23.957-.266 1.983-.399 3.003-.404 1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.803 5.624-5.475 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576 4.765-1.589 8.199-6.086 8.199-11.386 0-6.627-5.373-12-12-12z"/></svg>
                <span>Star</span>
            </a>

            <button
                type="button"
                data-theme-toggle
                class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-slate-300 text-slate-600 hover:bg-slate-100 hover:text-slate-900 dark:border-slate-700 dark:text-slate-400 dark:hover:bg-slate-800/60 dark:hover:text-white focus-ring transition-all duration-200"
                aria-label="Toggle theme"
                title="Toggle theme"
            >
                <svg data-theme-icon-sun xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <circle cx="12" cy="12" r="4"></circle>
                    <path d="M12 2v2"></path><path d="M12 20v2"></path>
                    <path d="m4.93 4.93 1.41 1.41"></path><path d="m17.66 17.66 1.41 1.41"></path>
                    <path d="M2 12h2"></path><path d="M20 12h2"></path>
                    <path d="m6.34 17.66-1.41 1.41"></path><path d="m19.07 4.93-1.41 1.41"></path>
                </svg>
                <svg data-theme-icon-moon xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="hidden h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M12 3a6 6 0 0 0 9 9 9 9 0 1 1-9-9z"></path>
                </svg>
            </button>

            <button
                type="button"
                data-mobile-toggle
                class="lg:hidden inline-flex h-9 w-9 items-center justify-center rounded-lg border border-slate-300 text-slate-600 hover:bg-slate-100 hover:text-slate-900 dark:border-slate-700 dark:text-slate-400 dark:hover:bg-slate-800/60 dark:hover:text-white focus-ring transition-all duration-200"
                aria-controls="mobile-nav"
                aria-expanded="false"
                aria-label="Open navigation menu"
            >
                <svg data-menu-open xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <line x1="4" x2="20" y1="12" y2="12"></line><line x1="4" x2="20" y1="6" y2="6"></line><line x1="4" x2="20" y1="18" y2="18"></line>
                </svg>
                <svg data-menu-close xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="hidden h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <line x1="18" x2="6" y1="6" y2="18"></line><line x1="6" x2="18" y1="6" y2="18"></line>
                </svg>
            </button>
        </div>
    </nav>

    <div id="mobile-nav" data-mobile-menu class="hidden border-t border-slate-200/50 bg-white/95 backdrop-blur-xl px-4 py-3 dark:border-slate-800/50 dark:bg-slate-950/95 lg:hidden animate-fade-in-down">
        <div class="mx-auto max-w-7xl grid gap-1">
            <?php foreach ($links as $item): ?>
                <a
                    href="<?= htmlspecialchars($item['href'], ENT_QUOTES, 'UTF-8') ?>"
                    aria-current="<?= $active === $item['key'] ? 'page' : 'false' ?>"
                    class="rounded-lg px-3 py-2.5 text-sm font-medium focus-ring transition-all duration-200 <?= $active === $item['key']
                        ? 'bg-gradient-to-r from-blue-600 to-indigo-600 text-white shadow-md'
                        : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100 dark:text-slate-400 dark:hover:text-white dark:hover:bg-slate-800/60' ?>"
                >
                    <?= htmlspecialchars($item['label'], ENT_QUOTES, 'UTF-8') ?>
                </a>
            <?php endforeach; ?>
            <a href="/github" class="rounded-lg px-3 py-2.5 text-sm font-medium text-slate-600 hover:text-slate-900 hover:bg-slate-100 dark:text-slate-400 dark:hover:text-white dark:hover:bg-slate-800/60 flex items-center gap-2">
                <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 0c-6.626 0-12 5.373-12 12 0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.73.083-.73 1.205.085 1.838 1.236 1.838 1.236 1.07 1.835 2.809 1.305 3.495.998.108-.776.417-1.305.76-1.605-2.665-.3-5.466-1.332-5.466-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23.957-.266 1.983-.399 3.003-.404 1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.803 5.624-5.475 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576 4.765-1.589 8.199-6.086 8.199-11.386 0-6.627-5.373-12-12-12z"/></svg>
                GitHub
            </a>
        </div>
    </div>
</header>