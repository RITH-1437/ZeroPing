<?php require_once __DIR__ . '/../components/component.php'; ?>
<?php render_component('hero', [
    'title' => 'Build Modern PHP Applications Without the Complexity',
    'subtitle' => 'Ship modern PHP apps with an expressive architecture, practical tooling, and premium developer experience.'
]); ?>

<section class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 mt-8">
    <div class="grid md:grid-cols-3 gap-5">
        <?php render_component('feature-card', [
            'icon' => '<img src="/assets/images/core.png" alt="" class="h-8 w-8 object-contain">',
            'title' => 'Composable Core',
            'description' => 'Routing, ORM, queue, cache, mail, and scheduling designed as clean, focused modules.'
        ]); ?>
        <?php render_component('feature-card', [
            'icon' => '<img src="/assets/images/cli.png" alt="" class="h-8 w-8 object-contain">',
            'title' => 'Powerful CLI',
            'description' => 'Generate files, run migrations, inspect routes, and test framework features from one command surface.'
        ]); ?>
        <?php render_component('feature-card', [
            'icon' => '<img src="/assets/images/production.png" alt="" class="h-8 w-8 object-contain">',
            'title' => 'Production Ready',
            'description' => 'Strong defaults for security, performance, and maintainability without sacrificing flexibility.'
        ]); ?>
    </div>
</section>

<section class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 mt-20" data-animate>
    <div class="text-center">
        <h2 class="text-3xl sm:text-4xl font-bold text-slate-900 dark:text-slate-50">Everything you need to build</h2>
        <p class="mt-3 text-slate-600 dark:text-slate-400 max-w-xl mx-auto">From scaffolding to deployment, ZeroPing provides the tools for modern PHP development.</p>
    </div>
    <div class="mt-10 grid lg:grid-cols-2 gap-6">
        <?php render_component('code-block', [
            'title' => 'config/routes.php',
            'language' => 'PHP',
            'codeId' => 'home-code-preview',
            'code' => <<<'CODE'
Router::get('/', [WebsiteController::class, 'home']);
Router::get('/docs/{slug}', [WebsiteController::class, 'docs']);
Router::get('/features', [WebsiteController::class, 'features']);

public function home(): void
{
    $this->view('site/home', [
        'title' => 'ZeroPing',
        'active' => 'home'
    ]);
}
CODE
        ]); ?>

        <?php render_component('terminal-window', [
            'title' => 'CLI Preview',
            'lines' => [
                '$ php cli\\migrate.php',
                '✔ Migrated: 001_create_users_table',
                '$ php public\\index.php',
                'ZeroPing server started on http://localhost:1437',
                '  ➜ Local:    http://localhost:1437',
                '  ➜ Network:  http://0.0.0.0:1437',
                '',
                '$ php zero make:controller DocsController',
                '✔ Controller created successfully.',
            ],
        ]); ?>
    </div>
</section>

<section class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 mt-20" data-animate>
    <div class="text-center">
        <h2 class="text-3xl sm:text-4xl font-bold text-slate-900 dark:text-slate-50">Architecture</h2>
        <p class="mt-3 text-slate-600 dark:text-slate-400 max-w-xl mx-auto">Clean MVC architecture designed for predictability and scalability.</p>
    </div>
    <div class="mt-10 flex flex-col items-center">
        <div class="inline-flex flex-col items-center gap-0 w-full max-w-lg">
            <?php
            $archSteps = [
                ['label' => 'Browser', 'icon' => '/assets/images/browser.png'],
                ['label' => 'Router', 'icon' => '/assets/images/router.png'],
                ['label' => 'Middleware', 'icon' => '/assets/images/middleware.png'],
                ['label' => 'Controller', 'icon' => '/assets/images/controller.png'],
                ['label' => 'Service / ORM', 'icon' => '/assets/images/service.png'],
                ['label' => 'Database', 'icon' => '/assets/images/database.png'],
                ['label' => 'Response', 'icon' => '/assets/images/response.png'],
            ];
            foreach ($archSteps as $i => $step): ?>
                <div class="flex items-center gap-4 w-full max-w-sm rounded-xl border border-slate-200/70 dark:border-slate-800/70 bg-white/80 dark:bg-slate-900/80 px-5 py-3.5 shadow-sm transition-all duration-300 hover:shadow-md hover:border-blue-300/50 dark:hover:border-blue-700/50 group">
                    <div class="flex items-center justify-center h-8 w-8 rounded-lg bg-gradient-to-br from-blue-50 to-indigo-50 dark:from-blue-950/60 dark:to-indigo-950/60 ring-1 ring-blue-200/40 dark:ring-blue-800/40 shrink-0">
                        <img src="<?= $step['icon'] ?>" alt="" class="h-5 w-5 object-contain">
                    </div>
                    <span class="font-medium text-sm text-slate-900 dark:text-slate-100"><?= $step['label'] ?></span>
                    <span class="ml-auto text-[10px] font-mono text-slate-400 dark:text-slate-500 opacity-0 group-hover:opacity-100 transition-opacity">Step <?= $i + 1 ?></span>
                </div>
                <?php if ($i < count($archSteps) - 1): ?>
                    <div class="flex items-center justify-center h-6 text-slate-300 dark:text-slate-600">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="m19 9-7 7-7-7"/></svg>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 mt-20" data-animate>
    <div class="grid lg:grid-cols-2 gap-6">
        <div class="rounded-2xl border border-slate-200/70 dark:border-slate-800/70 bg-white/80 dark:bg-slate-900/80 p-8 shadow-sm">
            <div class="inline-flex items-center justify-center h-10 w-10 rounded-xl bg-gradient-to-br from-blue-50 to-indigo-50 dark:from-blue-950/60 dark:to-indigo-950/60 ring-1 ring-blue-200/50 dark:ring-blue-800/50" aria-hidden="true">
                <img src="/assets/images/installation.png" alt="" class="h-5 w-5 object-contain">
            </div>
            <h3 class="mt-4 text-xl font-semibold text-slate-900 dark:text-slate-100">Installation</h3>
            <p class="mt-2 text-sm text-slate-600 dark:text-slate-400">Get started in under 3 minutes with a clean install flow.</p>
            <?php render_component('code-block', [
                'title' => 'terminal',
                'codeId' => 'home-install-preview',
                'code' => "git clone https://github.com/RITH-1437/ZeroPing.git\ncd ZeroPing\ncomposer install\ncp .env.example .env\nphp cli\\migrate.php",
            ]); ?>
            <div class="mt-4"><?php render_component('button', ['label' => 'Full Installation Guide', 'href' => '/installation']); ?></div>
        </div>

        <div class="rounded-2xl border border-slate-200/70 dark:border-slate-800/70 bg-white/80 dark:bg-slate-900/80 p-6 shadow-sm">
            <div class="inline-flex items-center justify-center h-10 w-10 rounded-xl bg-gradient-to-br from-blue-50 to-indigo-50 dark:from-blue-950/60 dark:to-indigo-950/60 ring-1 ring-blue-200/50 dark:ring-blue-800/50" aria-hidden="true">
                <img src="/assets/images/documentation.png" alt="" class="h-5 w-5 object-contain">
            </div>
            <h3 class="mt-4 text-xl font-semibold text-slate-900 dark:text-slate-100">Documentation</h3>
            <p class="mt-2 text-sm text-slate-600 dark:text-slate-400">Structured docs with table of contents, copyable snippets, and sequential navigation.</p>
            <ul class="mt-4 space-y-2 text-sm text-slate-600 dark:text-slate-400">
                <li class="flex items-center gap-2.5"><svg class="h-4 w-4 text-blue-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>Introduction and architecture</li>
                <li class="flex items-center gap-2.5"><svg class="h-4 w-4 text-blue-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>Installation and setup workflow</li>
                <li class="flex items-center gap-2.5"><svg class="h-4 w-4 text-blue-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>API references and usage examples</li>
                <li class="flex items-center gap-2.5"><svg class="h-4 w-4 text-blue-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>Search-ready with keyboard shortcuts</li>
            </ul>
            <div class="mt-5"><?php render_component('button', ['label' => 'Open Docs', 'href' => '/docs/introduction', 'variant' => 'secondary']); ?></div>
        </div>
    </div>
</section>

<section class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 mt-20" data-animate>
    <div class="grid lg:grid-cols-3 gap-6">
        <div class="rounded-2xl border border-slate-200/70 dark:border-slate-800/80 bg-white/80 dark:bg-slate-900/80 p-8 shadow-sm text-center hover:shadow-lg hover:shadow-blue-500/10 dark:hover:shadow-blue-500/10 hover:-translate-y-1 transition-all duration-300">
            <div class="inline-flex items-center justify-center h-12 w-12 rounded-2xl bg-gradient-to-br from-blue-50 to-indigo-50 dark:from-blue-950/60 dark:to-indigo-950/60 ring-1 ring-blue-200/50 dark:ring-blue-800/50 mx-auto" aria-hidden="true">
                <img src="/assets/images/opensource.png" alt="" class="h-6 w-6 object-contain">
            </div>
            <h3 class="mt-4 text-lg font-semibold text-slate-900 dark:text-slate-100">Open Source</h3>
            <p class="mt-2 text-sm text-slate-600 dark:text-slate-400">ZeroPing is free and open source. Contribute, report issues, or suggest features on GitHub.</p>
            <div class="mt-5"><?php render_component('button', ['label' => 'View on GitHub', 'href' => '/github', 'variant' => 'secondary']); ?></div>
        </div>

        <div class="rounded-2xl border border-slate-200/70 dark:border-slate-800/70 bg-white/80 dark:bg-slate-900/80 p-8 shadow-sm text-center hover:shadow-lg hover:shadow-emerald-500/15 dark:hover:shadow-emerald-500/10 hover:-translate-y-1 transition-all duration-300">
            <div class="inline-flex items-center justify-center h-12 w-12 rounded-2xl bg-gradient-to-br from-emerald-50 to-teal-50 dark:from-emerald-950/60 dark:to-teal-950/60 ring-1 ring-emerald-200/50 dark:ring-emerald-800/50 mx-auto" aria-hidden="true">
                <img src="/assets/images/documentation.png" alt="" class="h-6 w-6 object-contain">
            </div>
            <h3 class="mt-4 text-lg font-semibold text-slate-900 dark:text-slate-100">Documentation</h3>
            <p class="mt-2 text-sm text-slate-600 dark:text-slate-400">Comprehensive docs with search, code examples, and guided navigation from start to finish.</p>
            <div class="mt-5"><?php render_component('button', ['label' => 'Read Docs', 'href' => '/docs/introduction', 'variant' => 'secondary']); ?></div>
        </div>

        <div class="rounded-2xl border border-slate-200/70 dark:border-slate-800/70 bg-white/80 dark:bg-slate-900/80 p-8 shadow-sm text-center hover:shadow-lg hover:shadow-amber-500/15 dark:hover:shadow-amber-500/10 hover:-translate-y-1 transition-all duration-300">
            <div class="inline-flex items-center justify-center h-12 w-12 rounded-2xl bg-gradient-to-br from-amber-50 to-orange-50 dark:from-amber-950/60 dark:to-orange-950/60 ring-1 ring-amber-200/50 dark:ring-amber-800/50 mx-auto" aria-hidden="true">
                <img src="/assets/images/sponser.png" alt="" class="h-6 w-6 object-contain">
            </div>
            <h3 class="mt-4 text-lg font-semibold text-slate-900 dark:text-slate-100">Sponsors</h3>
            <p class="mt-2 text-sm text-slate-600 dark:text-slate-400">Support ZeroPing development. Sponsorships and partnerships help us build better tools.</p>
            <div class="mt-5"><?php render_component('button', ['label' => 'Coming Soon', 'href' => '#', 'variant' => 'secondary']); ?></div>
        </div>
    </div>
</section>

<?php if (!empty($stats ?? [])): ?>
<section class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 mt-20" data-animate>
    <div class="rounded-2xl border border-slate-200/70 dark:border-slate-800/70 bg-white/80 dark:bg-slate-900/80 p-8 shadow-sm">
        <div class="text-center mb-8">
            <h2 class="text-2xl sm:text-3xl font-bold text-slate-900 dark:text-slate-50">Statistics</h2>
            <p class="mt-2 text-sm text-slate-600 dark:text-slate-400">Key metrics about the ZeroPing Framework ecosystem.</p>
        </div>
        <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <?php foreach ($stats as $item): ?>
                <div class="text-center">
                    <div class="inline-flex items-center justify-center h-10 w-10 rounded-xl bg-gradient-to-br from-blue-50 to-indigo-50 dark:from-blue-950/60 dark:to-indigo-950/60 ring-1 ring-blue-200/40 dark:ring-blue-800/40 mb-3" aria-hidden="true">
                        <?php if (!empty($item['icon'])): ?>
                            <img src="<?= htmlspecialchars($item['icon'], ENT_QUOTES, 'UTF-8') ?>" alt="" class="h-5 w-5 object-contain">
                        <?php else: ?>
                            <img src="/assets/images/core.png" alt="" class="h-5 w-5 object-contain">
                        <?php endif; ?>
                    </div>
                    <p class="text-3xl font-bold text-slate-900 dark:text-white" data-count><?= htmlspecialchars($item['value'], ENT_QUOTES, 'UTF-8') ?></p>
                    <p class="mt-1 text-xs text-slate-500 dark:text-slate-400"><?= htmlspecialchars($item['label'], ENT_QUOTES, 'UTF-8') ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>


<script>
(function() {
    const stats = document.querySelectorAll('[data-count]');
    if (!stats.length) return;
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                observer.unobserve(entry.target);
                const target = entry.target;
                const val = target.textContent.trim();
                const num = parseInt(val.replace(/[^0-9]/g, ''), 10);
                if (isNaN(num)) return;
                const suffix = val.replace(/[0-9]/g, '');
                const duration = 1000;
                const start = performance.now();
                function update() {
                    const elapsed = performance.now() - start;
                    const progress = Math.min(elapsed / duration, 1);
                    const eased = 1 - Math.pow(1 - progress, 3);
                    target.textContent = Math.round(eased * num) + suffix;
                    if (progress < 1) requestAnimationFrame(update);
                }
                update();
            }
        });
    }, { threshold: 0.5 });
    stats.forEach(el => observer.observe(el));
})();
</script>