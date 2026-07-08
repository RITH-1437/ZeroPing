<?php require_once __DIR__ . '/../components/component.php'; ?>
<section class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">
    <?php render_component('breadcrumb', ['items' => [['label' => 'Home', 'href' => '/'], ['label' => 'Documentation']]]); ?>
    <h1 class="mt-6 text-4xl font-bold tracking-tight">Documentation Portal</h1>
    <p class="mt-4 text-slate-600 dark:text-slate-300">Search-ready docs with markdown rendering, table of contents, copyable code blocks, and sequential navigation.</p>

    <div class="mt-8 grid sm:grid-cols-2 gap-4">
        <?php foreach (($documents ?? []) as $doc): ?>
            <a href="/docs/<?= htmlspecialchars($doc['slug'], ENT_QUOTES, 'UTF-8') ?>" class="block rounded-2xl border border-slate-200 dark:border-slate-800 bg-white/95 dark:bg-slate-900/85 p-5 hover:bg-slate-100 dark:hover:bg-slate-900 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500">
                <h2 class="font-semibold text-lg"><?= htmlspecialchars($doc['title'], ENT_QUOTES, 'UTF-8') ?></h2>
                <p class="mt-2 text-sm text-slate-600 dark:text-slate-400"><?= htmlspecialchars($doc['description'], ENT_QUOTES, 'UTF-8') ?></p>
            </a>
        <?php endforeach; ?>
    </div>
</section>
