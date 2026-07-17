<?php
$currentPath = parse_url(($_SERVER['REQUEST_URI'] ?? '/'), PHP_URL_PATH);

// Navigation definition per the official site structure:
// Home · Getting Started · Installation · Documentation · Features · API · Roadmap · GitHub · Community
$navItems = [
    ['label' => 'Home', 'href' => '/', 'match' => fn($p) => $p === '/'],
    ['label' => 'Getting Started', 'href' => '/getting-started', 'match' => fn($p) => $p === '/getting-started'],
    ['label' => 'Installation', 'href' => '/installation', 'match' => fn($p) => $p === '/installation'],
    ['label' => 'Documentation', 'href' => '/docs/introduction', 'match' => fn($p) => str_starts_with($p, '/docs')],
    ['label' => 'Features', 'href' => '/features', 'match' => fn($p) => $p === '/features'],
    ['label' => 'API', 'href' => '/api', 'match' => fn($p) => $p === '/api'],
    ['label' => 'Roadmap', 'href' => '/roadmap', 'match' => fn($p) => $p === '/roadmap'],
    ['label' => 'Community', 'href' => '/community', 'match' => fn($p) => $p === '/community'],
];

$isActive = fn($item) => $item['match']($currentPath);

$githubUrl = 'https://github.com/RITH-1437/ZeroPing';
?>
<header class="sticky top-0 inset-x-0 z-50 border-b border-zp-border/50 bg-zp-bg/70 backdrop-blur-2xl">
    <nav class="mx-auto flex max-w-7xl items-center justify-between px-4 sm:px-6 lg:px-8 h-16" aria-label="Top navigation">
        <a href="/" class="inline-flex items-center gap-3 font-semibold text-zp-ink focus-ring shrink-0">
            <img src="/assets/images/mascot.svg" alt="ZeroPing" class="h-6 w-6 md:h-7 md:w-7 lg:h-8 lg:w-8 shrink-0 object-contain">
            <span class="hidden sm:inline">ZeroPing</span>
            <span class="hidden sm:inline-flex items-center rounded-full border border-zp-border bg-zp-surface/80 px-2 py-0.5 text-[10px] font-medium text-zp-muted"><?= 'v' . \App\Core\Application\App::VERSION ?></span>
        </a>

        <div class="hidden lg:flex items-center gap-1">
            <?php foreach ($navItems as $item):
                $active = $isActive($item); ?>
                <a href="<?= htmlspecialchars($item['href'], ENT_QUOTES, 'UTF-8') ?>"
                   class="rounded-lg px-3 py-2 text-sm font-medium transition-all duration-200 focus-ring nav-link-underline <?= $active ? 'text-zp-ink' : 'text-zp-muted hover:text-zp-ink' ?> <?= $active ? 'active' : '' ?>">
                    <?= htmlspecialchars($item['label'], ENT_QUOTES, 'UTF-8') ?>
                </a>
            <?php endforeach; ?>
        </div>

        <div class="flex items-center gap-2">
            <a href="<?= $githubUrl ?>" target="_blank" rel="noopener noreferrer"
               class="hidden sm:inline-flex items-center gap-2 rounded-xl border border-zp-border hover:bg-zp-surface px-3 py-1.5 text-sm font-medium text-zp-muted hover:text-zp-ink transition-all duration-200 focus-ring shrink-0">
                <svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor"><path d="M12 .5C5.7.5.5 5.7.5 12c0 5.1 3.3 9.4 7.9 10.9.6.1.8-.2.8-.5v-2c-3.2.7-3.9-1.4-3.9-1.4-.5-1.3-1.3-1.7-1.3-1.7-1-.7.1-.7.1-.7 1.2.1 1.8 1.2 1.8 1.2 1 1.8 2.7 1.3 3.4 1 .1-.8.4-1.3.7-1.6-2.6-.3-5.3-1.3-5.3-5.8 0-1.3.5-2.3 1.2-3.1-.1-.3-.5-1.5.1-3.1 0 0 1-.3 3.3 1.2a11.5 11.5 0 0 1 6 0C17.3 4.7 18.3 5 18.3 5c.6 1.6.2 2.8.1 3.1.8.8 1.2 1.8 1.2 3.1 0 4.5-2.7 5.5-5.3 5.8.4.4.8 1.1.8 2.2v3.3c0 .3.2.6.8.5A11.5 11.5 0 0 0 23.5 12C23.5 5.7 18.3.5 12 .5z"/></svg>
                Star
            </a>
            <a href="/github"
               class="inline-flex h-9 w-9 items-center justify-center rounded-xl border border-zp-border text-zp-muted hover:text-zp-ink hover:bg-zp-surface focus-ring transition-all duration-200"
               aria-label="GitHub">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182M2.985 19.644l3.181-3.182"/></svg>
            </a>
            <button id="mobile-menu-toggle" data-mobile-toggle aria-label="Toggle navigation menu" aria-expanded="false"
                    class="lg:hidden inline-flex h-9 w-9 items-center justify-center rounded-xl border border-zp-border text-zp-muted hover:text-zp-ink hover:bg-zp-surface focus-ring transition-all duration-200">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"/></svg>
            </button>
        </div>
    </nav>

    <div id="mobile-nav" data-mobile-menu class="hidden border-t border-zp-border/50 bg-zp-bg/95 backdrop-blur-xl px-4 py-3 lg:hidden animate-fade-in-down">
        <div class="flex flex-col gap-1">
            <?php foreach ($navItems as $item):
                $active = $isActive($item); ?>
                <a href="<?= htmlspecialchars($item['href'], ENT_QUOTES, 'UTF-8') ?>"
                   class="rounded-xl px-3 py-2.5 text-sm font-medium transition-all duration-200 <?= $active ? 'bg-zp-primary text-zp-ink shadow-md' : 'text-zp-muted hover:text-zp-ink hover:bg-zp-surface' ?>">
                    <?= htmlspecialchars($item['label'], ENT_QUOTES, 'UTF-8') ?>
                </a>
            <?php endforeach; ?>
            <a href="<?= $githubUrl ?>" target="_blank" rel="noopener noreferrer"
               class="rounded-xl px-3 py-2.5 text-sm font-medium text-zp-muted hover:text-zp-ink hover:bg-zp-surface flex items-center gap-2">
                <svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor"><path d="M12 .5C5.7.5.5 5.7.5 12c0 5.1 3.3 9.4 7.9 10.9.6.1.8-.2.8-.5v-2c-3.2.7-3.9-1.4-3.9-1.4-.5-1.3-1.3-1.7-1.3-1.7-1-.7.1-.7.1-.7 1.2.1 1.8 1.2 1.8 1.2 1 1.8 2.7 1.3 3.4 1 .1-.8.4-1.3.7-1.6-2.6-.3-5.3-1.3-5.3-5.8 0-1.3.5-2.3 1.2-3.1-.1-.3-.5-1.5.1-3.1 0 0 1-.3 3.3 1.2a11.5 11.5 0 0 1 6 0C17.3 4.7 18.3 5 18.3 5c.6 1.6.2 2.8.1 3.1.8.8 1.2 1.8 1.2 3.1 0 4.5-2.7 5.5-5.3 5.8.4.4.8 1.1.8 2.2v3.3c0 .3.2.6.8.5A11.5 11.5 0 0 0 23.5 12C23.5 5.7 18.3.5 12 .5z"/></svg>
                GitHub
            </a>
        </div>
    </div>
</header>
<script>
document.addEventListener('DOMContentLoaded', function () {
    var btn = document.getElementById('mobile-menu-toggle');
    var menu = document.getElementById('mobile-nav');
    if (btn && menu) {
        btn.addEventListener('click', function () {
            var expanded = btn.getAttribute('aria-expanded') === 'true';
            btn.setAttribute('aria-expanded', expanded ? 'false' : 'true');
            menu.classList.toggle('hidden');
        });
    }
});
</script>
