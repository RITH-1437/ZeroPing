<?php require_once __DIR__ . '/../components/component.php'; ?>
<section class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8" data-animate>
    <?php render_component('breadcrumb', ['items' => [['label' => 'Home', 'href' => '/'], ['label' => 'GitHub']]]); ?>
    <h1 class="mt-6 text-4xl sm:text-5xl font-extrabold tracking-tight text-slate-900 dark:text-slate-50">GitHub</h1>
    <p class="mt-4 text-slate-600 dark:text-slate-400">Follow development, submit issues, and contribute to ZeroPing Framework.</p>

    <div class="mt-8 rounded-2xl border border-slate-200/70 dark:border-slate-800/70 bg-white/80 dark:bg-slate-900/80 p-6 shadow-sm">
        <div class="flex items-center gap-3">
            <div class="flex items-center justify-center h-10 w-10 rounded-xl bg-gradient-to-br from-blue-50 to-indigo-50 dark:from-blue-950/60 dark:to-indigo-950/60 ring-1 ring-blue-200/50 dark:ring-blue-800/50">
                <svg class="h-5 w-5 text-slate-700 dark:text-slate-300" fill="currentColor" viewBox="0 0 24 24"><path d="M12 0c-6.626 0-12 5.373-12 12 0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.73.083-.73 1.205.085 1.838 1.236 1.838 1.236 1.07 1.835 2.809 1.305 3.495.998.108-.776.417-1.305.76-1.605-2.665-.3-5.466-1.332-5.466-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23.957-.266 1.983-.399 3.003-.404 1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.803 5.624-5.475 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576 4.765-1.589 8.199-6.086 8.199-11.386 0-6.627-5.373-12-12-12z"/></svg>
            </div>
            <div>
                <h2 class="text-lg font-semibold text-slate-900 dark:text-slate-100">Official Repository</h2>
                <p class="text-sm text-slate-500 dark:text-slate-400 font-mono"><?= htmlspecialchars($repositoryUrl ?? '', ENT_QUOTES, 'UTF-8') ?></p>
            </div>
        </div>

        <div class="mt-6 grid grid-cols-2 sm:grid-cols-4 gap-4" data-animate-stagger>
            <div class="rounded-xl border border-slate-200/60 dark:border-slate-800/60 bg-slate-50/50 dark:bg-slate-900/50 p-4 text-center">
                <svg class="h-5 w-5 mx-auto text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 00-.182.557l1.285 5.385a.562.562 0 01-.84.61l-4.725-2.885a.563.563 0 00-.586 0L6.982 20.54a.562.562 0 01-.84-.61l1.285-5.386a.562.562 0 00-.182-.557l-4.204-3.602a.563.563 0 01.321-.988l5.518-.442a.563.563 0 00.475-.345L11.48 3.5z"/></svg>
                <p class="mt-2 text-lg font-bold text-slate-900 dark:text-white">0</p>
                <p class="text-xs text-slate-500 dark:text-slate-400">Stars</p>
            </div>
            <div class="rounded-xl border border-slate-200/60 dark:border-slate-800/60 bg-slate-50/50 dark:bg-slate-900/50 p-4 text-center">
                <svg class="h-5 w-5 mx-auto text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 3.75v4.5m0-4.5h4.5m-4.5 0L9 9M3.75 20.25v-4.5m0 4.5h4.5m-4.5 0L9 15M20.25 3.75h-4.5m4.5 0v4.5m0-4.5L15 9m5.25 11.25h-4.5m4.5 0v-4.5m0 4.5L15 15"/></svg>
                <p class="mt-2 text-lg font-bold text-slate-900 dark:text-white">0</p>
                <p class="text-xs text-slate-500 dark:text-slate-400">Forks</p>
            </div>
            <div class="rounded-xl border border-slate-200/60 dark:border-slate-800/60 bg-slate-50/50 dark:bg-slate-900/50 p-4 text-center">
                <svg class="h-5 w-5 mx-auto text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/></svg>
                <p class="mt-2 text-lg font-bold text-slate-900 dark:text-white">0</p>
                <p class="text-xs text-slate-500 dark:text-slate-400">Issues</p>
            </div>
            <div class="rounded-xl border border-slate-200/60 dark:border-slate-800/60 bg-slate-50/50 dark:bg-slate-900/50 p-4 text-center">
                <svg class="h-5 w-5 mx-auto text-purple-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/></svg>
                <p class="mt-2 text-lg font-bold text-slate-900 dark:text-white">1</p>
                <p class="text-xs text-slate-500 dark:text-slate-400">Contributor</p>
            </div>
        </div>

        <div class="mt-6 flex flex-wrap gap-3 pt-6 border-t border-slate-200/60 dark:border-slate-800/60">
            <?php render_component('button', ['label' => 'View Repository', 'href' => $repositoryUrl ?? '#']); ?>
            <?php render_component('button', ['label' => 'Report Issue', 'href' => ($repositoryUrl ?? '') . '/issues/new', 'variant' => 'secondary']); ?>
            <?php render_component('button', ['label' => 'Feature Request', 'href' => ($repositoryUrl ?? '') . '/issues/new?template=feature_request.md', 'variant' => 'secondary']); ?>
            <?php render_component('button', ['label' => 'Documentation', 'href' => '/docs/introduction', 'variant' => 'secondary']); ?>
        </div>
    </div>

    <div class="mt-6 rounded-2xl border border-slate-200/70 dark:border-slate-800/70 bg-white/80 dark:bg-slate-900/80 p-6 shadow-sm">
        <h2 class="text-xs font-semibold uppercase tracking-widest text-slate-500 dark:text-slate-400">Contribution Guidelines</h2>
        <ul class="mt-3 space-y-1.5 text-sm text-slate-600 dark:text-slate-400">
            <li class="flex items-start gap-2.5"><svg class="h-4 w-4 shrink-0 mt-0.5 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>Fork the repository and create a feature branch</li>
            <li class="flex items-start gap-2.5"><svg class="h-4 w-4 shrink-0 mt-0.5 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>Follow PSR-12 coding standards</li>
            <li class="flex items-start gap-2.5"><svg class="h-4 w-4 shrink-0 mt-0.5 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>Write tests for new features or bug fixes</li>
            <li class="flex items-start gap-2.5"><svg class="h-4 w-4 shrink-0 mt-0.5 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>Submit a pull request with a clear description</li>
        </ul>
    </div>
</section>