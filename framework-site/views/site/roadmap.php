<?php require_once __DIR__ . '/../components/component.php'; ?>
<section class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8" data-animate>
    <?php render_component('breadcrumb', ['items' => [['label' => 'Home', 'href' => '/'], ['label' => 'Roadmap']]]); ?>
    <div class="text-center">
        <h1 class="mt-6 font-display text-4xl sm:text-5xl font-bold tracking-tight text-zp-ink">Roadmap</h1>
        <p class="mt-4 max-w-2xl mx-auto text-zp-desc">Where ZeroPing is heading. Released, in progress, and planned milestones for the framework.</p>
    </div>

    <?php
    $milestones = [
        [
            'version' => 'v1.0.0',
            'status' => 'Released',
            'dotClass' => 'bg-emerald-500',
            'checkClass' => 'text-emerald-500',
            'badgeClass' => 'bg-emerald-500/10 text-emerald-600 border-emerald-500/20',
            'features' => [
                'Expressive Routing System',
                'Active Record ORM',
                'Dependency Injection Container',
                'Middleware Pipeline',
                'Blade-style Templating Engine',
                'Zero CLI Tool',
                'Database Migrations',
                'Authentication & Authorization Guards',
                'CSRF & Security Layer',
                'Session Management',
                'Form Request Validation',
                'Caching System',
            ],
        ],
        [
            'version' => 'v1.5.0',
            'status' => 'Released',
            'dotClass' => 'bg-emerald-500',
            'checkClass' => 'text-emerald-500',
            'badgeClass' => 'bg-emerald-500/10 text-emerald-600 border-emerald-500/20',
            'features' => [
                'Eloquent-style Relationships',
                'Fluent Query Builder',
                'Queue & Jobs',
                'Mail Facade',
                'Built-in Testing Suite',
                'Debug Bar & Profiler',
                'Rate Limiting',
                'API Resource Transformers',
            ],
        ],
        [
            'version' => 'v2.0.0',
            'status' => 'Released',
            'dotClass' => 'bg-emerald-500',
            'checkClass' => 'text-emerald-500',
            'badgeClass' => 'bg-emerald-500/10 text-emerald-600 border-emerald-500/20',
            'features' => [
                'Real-time Broadcasting (WebSockets)',
                'Localization & i18n',
                'Sliding Window Rate Limiter',
                'Health Check & Metrics Endpoint',
                'First-party Admin Starter Kit',
                'Plugin Architecture',
            ],
        ],
        [
            'version' => 'v2.0.1',
            'status' => 'Current Stable',
            'dotClass' => 'bg-zp-primary',
            'checkClass' => 'text-zp-primary',
            'badgeClass' => 'bg-zp-primary/10 text-zp-link border-zp-primary/30',
            'features' => [
                'Premium Documentation Website',
                'Green Terminal Code Block Styling',
                'Expanded Docs Coverage (16+ Pages)',
                'Composer Package Standardized',
                'Version Tracking System',
            ],
        ],
        [
            'version' => 'v2.1',
            'status' => 'In Progress',
            'dotClass' => 'bg-amber-500',
            'checkClass' => 'text-amber-500',
            'badgeClass' => 'bg-amber-500/10 text-amber-600 border-amber-500/20',
            'features' => [
                'GraphQL Support',
                'Serverless Deployment Adapter',
                'Built-in Admin Panel Generator',
                'Enhanced Async I/O',
            ],
        ],
        [
            'version' => 'v3.0',
            'status' => 'Planned',
            'dotClass' => 'bg-violet-500',
            'checkClass' => 'text-violet-500',
            'badgeClass' => 'bg-violet-500/10 text-violet-600 border-violet-500/20',
            'features' => [
                'Async & Parallel Request Handling',
                'Plugin & Extension Marketplace',
                'Distributed Tracing',
                'Edge Runtime Support',
            ],
        ],
    ];
    ?>

    <div class="mt-16 relative">
        <!-- Central spine -->
        <div class="absolute left-4 lg:left-1/2 lg:-translate-x-1/2 top-2 bottom-2 w-px bg-gradient-to-b from-zp-border via-zp-primary/40 to-zp-border"></div>

        <div class="space-y-10">
            <?php foreach ($milestones as $i => $m): ?>
                <?php $left = $i % 2 === 0; ?>
                <div class="relative roadmap-item <?= $left ? 'roadmap-from-left' : 'roadmap-from-right' ?>" style="opacity:0;">
                    <!-- Dot on the spine -->
                    <span class="absolute left-4 lg:left-1/2 lg:-translate-x-1/2 top-6 -ml-1.5 h-3.5 w-3.5 rounded-full <?= $m['dotClass'] ?> ring-4 ring-zp-bg z-10"></span>

                    <div class="pl-12 lg:pl-0 <?= $left ? 'lg:pr-[calc(50%+2rem)]' : 'lg:pl-[calc(50%+2rem)]' ?>">
                        <div class="rounded-2xl border border-zp-border bg-zp-surface p-6 shadow-sm hover:border-zp-primary/30 hover:shadow-lg hover:shadow-zp-primary/5 transition-all duration-300">
                            <div class="flex flex-wrap items-center gap-3">
                                <h2 class="font-display text-2xl font-bold text-zp-ink"><?= htmlspecialchars($m['version'], ENT_QUOTES, 'UTF-8') ?></h2>
                                <span class="rounded-full border px-3 py-0.5 text-xs font-medium <?= $m['badgeClass'] ?>"><?= htmlspecialchars($m['status'], ENT_QUOTES, 'UTF-8') ?></span>
                            </div>
                            <ul class="mt-4 grid sm:grid-cols-2 gap-x-6 gap-y-2 text-sm text-zp-desc">
                                <?php foreach ($m['features'] as $f): ?>
                                    <li class="flex items-start gap-2.5">
                                        <svg class="h-4 w-4 shrink-0 mt-0.5 <?= $m['checkClass'] ?>" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                        <span><?= htmlspecialchars($f, ENT_QUOTES, 'UTF-8') ?></span>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<style>
    @keyframes roadmapSweepLeft {
        from { opacity: 0; transform: translateX(-48px); }
        to   { opacity: 1; transform: translateX(0); }
    }
    @keyframes roadmapSweepRight {
        from { opacity: 0; transform: translateX(48px); }
        to   { opacity: 1; transform: translateX(0); }
    }
    .roadmap-from-left.is-visible {
        animation: roadmapSweepLeft 0.6s cubic-bezier(0.22, 1, 0.36, 1) forwards;
    }
    .roadmap-from-right.is-visible {
        animation: roadmapSweepRight 0.6s cubic-bezier(0.22, 1, 0.36, 1) forwards;
    }
    @media (prefers-reduced-motion: reduce) {
        .roadmap-from-left, .roadmap-from-right { opacity: 1 !important; transform: none !important; animation: none !important; }
    }
</style>

<script>
(function() {
    var items = document.querySelectorAll('.roadmap-item');
    if (!('IntersectionObserver' in window) || !items.length) {
        items.forEach(function (el) { el.style.opacity = 1; el.classList.add('is-visible'); });
        return;
    }
    var observer = new IntersectionObserver(function (entries) {
        entries.forEach(function (entry) {
            if (entry.isIntersecting) {
                entry.target.style.opacity = 1;
                entry.target.classList.add('is-visible');
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.15, rootMargin: '0px 0px -10% 0px' });
    items.forEach(function (el) { observer.observe(el); });
})();
</script>
