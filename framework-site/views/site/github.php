<?php require_once __DIR__ . '/../components/component.php'; ?>
<section class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8" data-animate>
    <?php render_component('breadcrumb', ['items' => [['label' => 'Home', 'href' => '/'], ['label' => 'GitHub']]]); ?>
    <h1 class="mt-6 text-4xl sm:text-5xl font-extrabold tracking-tight text-zp-ink">GitHub</h1>
    <p class="mt-4 text-zp-desc">Follow development, submit issues, and contribute to ZeroPing Framework.</p>

    <div class="mt-8 rounded-2xl border border-zp-border bg-zp-surface p-6 shadow-sm">
        <div class="flex items-center gap-3">
            <div class="flex items-center justify-center h-10 w-10 rounded-xl bg-zp-primary/10 ring-1 ring-zp-primary/20">
                <img src="/assets/images/github.png" alt="GitHub" class="h-5 w-5 object-contain">
            </div>
            <div>
                <h2 class="text-lg font-semibold text-zp-ink">Official Repository</h2>
                <p class="text-sm text-zp-desc font-mono"><?= htmlspecialchars($repositoryUrl ?? '', ENT_QUOTES, 'UTF-8') ?></p>
            </div>
        </div>

        <div class="mt-6 grid grid-cols-2 sm:grid-cols-4 gap-4" data-animate-stagger>
            <div class="rounded-xl border border-zp-border bg-zp-surface/50 p-4 text-center">
                <img src="/assets/images/star.png" alt="" class="h-5 w-5 mx-auto object-contain">
                <p class="mt-2 text-lg font-bold text-zp-ink">0</p>
                <p class="text-xs text-zp-desc">Stars</p>
            </div>
            <div class="rounded-xl border border-zp-border bg-zp-surface/50 p-4 text-center">
                <svg class="h-5 w-5 mx-auto text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 3.75v4.5m0-4.5h4.5m-4.5 0L9 9M3.75 20.25v-4.5m0 4.5h4.5m-4.5 0L9 15M20.25 3.75h-4.5m4.5 0v4.5m0-4.5L15 9m5.25 11.25h-4.5m4.5 0v-4.5m0 4.5L15 15"/></svg>
                <p class="mt-2 text-lg font-bold text-zp-ink">0</p>
                <p class="text-xs text-zp-desc">Forks</p>
            </div>
            <div class="rounded-xl border border-zp-border bg-zp-surface/50 p-4 text-center">
                <img src="/assets/images/issue.png" alt="" class="h-5 w-5 mx-auto object-contain">
                <p class="mt-2 text-lg font-bold text-zp-ink">0</p>
                <p class="text-xs text-zp-desc">Issues</p>
            </div>
            <div class="rounded-xl border border-zp-border bg-zp-surface/50 p-4 text-center">
                <img src="/assets/images/opensource.png" alt="" class="h-5 w-5 mx-auto object-contain">
                <p class="mt-2 text-lg font-bold text-zp-ink">1</p>
                <p class="text-xs text-zp-desc">Contributor</p>
            </div>
        </div>

        <div class="mt-6 flex flex-wrap gap-3 pt-6 border-t border-zp-border">
            <?php render_component('button', ['label' => 'View Repository', 'href' => $repositoryUrl ?? '#']); ?>
            <?php render_component('button', ['label' => 'Report Issue', 'href' => ($repositoryUrl ?? '') . '/issues/new', 'variant' => 'secondary']); ?>
            <?php render_component('button', ['label' => 'Feature Request', 'href' => ($repositoryUrl ?? '') . '/issues/new?template=feature_request.md', 'variant' => 'secondary']); ?>
            <?php render_component('button', ['label' => 'Documentation', 'href' => '/docs/introduction', 'variant' => 'secondary']); ?>
        </div>
    </div>

    <div class="mt-6 rounded-2xl border border-zp-border bg-zp-surface p-6 shadow-sm">
        <h2 class="text-xs font-semibold uppercase tracking-widest text-zp-desc">Contribution Guidelines</h2>
        <ul class="mt-3 space-y-1.5 text-sm text-zp-desc">
            <li class="flex items-start gap-2.5"><svg class="h-4 w-4 shrink-0 mt-0.5 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>Fork the repository and create a feature branch</li>
            <li class="flex items-start gap-2.5"><svg class="h-4 w-4 shrink-0 mt-0.5 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>Follow PSR-12 coding standards</li>
            <li class="flex items-start gap-2.5"><svg class="h-4 w-4 shrink-0 mt-0.5 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>Write tests for new features or bug fixes</li>
            <li class="flex items-start gap-2.5"><svg class="h-4 w-4 shrink-0 mt-0.5 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>Submit a pull request with a clear description</li>
        </ul>
    </div>
</section>
