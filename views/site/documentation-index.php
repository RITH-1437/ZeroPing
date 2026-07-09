<?php require_once __DIR__ . '/../components/component.php'; ?>
<section class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8" data-animate>
    <?php render_component('breadcrumb', ['items' => [['label' => 'Home', 'href' => '/'], ['label' => 'Documentation']]]); ?>
    <h1 class="mt-6 text-4xl sm:text-5xl font-extrabold tracking-tight text-slate-900 dark:text-slate-50">Documentation Portal</h1>
    <p class="mt-4 text-slate-600 dark:text-slate-400">Search-ready docs with markdown rendering, table of contents, copyable code blocks, and sequential navigation.</p>

    <div class="mt-8 grid sm:grid-cols-2 gap-4" data-animate-stagger>
        <?php foreach (($documents ?? []) as $doc): ?>
            <a href="/docs/<?= htmlspecialchars($doc['slug'], ENT_QUOTES, 'UTF-8') ?>" class="group block relative rounded-2xl border border-slate-200/70 dark:border-slate-800/70 bg-white/80 dark:bg-slate-900/80 p-5 shadow-sm hover:shadow-lg hover:shadow-blue-500/5 dark:hover:shadow-blue-500/5 hover:-translate-y-0.5 focus-ring transition-all duration-300">
                <h2 class="font-semibold text-lg text-slate-900 dark:text-slate-100 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors"><?= htmlspecialchars($doc['title'], ENT_QUOTES, 'UTF-8') ?></h2>
                <p class="mt-2 text-sm text-slate-600 dark:text-slate-400"><?= htmlspecialchars($doc['description'], ENT_QUOTES, 'UTF-8') ?></p>
            </a>
        <?php endforeach; ?>
    </div>
</section>