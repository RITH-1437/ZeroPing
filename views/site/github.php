<?php require_once __DIR__ . '/../components/component.php'; ?>
<section class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
    <?php render_component('breadcrumb', ['items' => [['label' => 'Home', 'href' => '/'], ['label' => 'GitHub']]]); ?>
    <h1 class="mt-6 text-4xl font-bold tracking-tight">GitHub</h1>
    <p class="mt-4 text-slate-600 dark:text-slate-300">Follow development, submit issues, and contribute to ZeroPing Framework.</p>
    <div class="mt-8 rounded-2xl border border-slate-200 dark:border-slate-800 bg-white/80 dark:bg-slate-900/70 p-6">
        <p class="text-sm text-slate-600 dark:text-slate-400">Official Repository</p>
        <p class="mt-2 font-mono text-sm break-all"><?= htmlspecialchars($repositoryUrl ?? '', ENT_QUOTES, 'UTF-8') ?></p>
        <div class="mt-5 flex gap-3">
            <?php render_component('button', ['label' => 'Open Repository', 'href' => $repositoryUrl ?? '#']); ?>
            <?php render_component('button', ['label' => 'Documentation', 'href' => '/docs/introduction', 'variant' => 'secondary']); ?>
        </div>
    </div>
</section>
