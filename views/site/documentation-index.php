<?php require_once __DIR__ . '/../components/component.php'; ?>
<section class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8" data-animate>
    <?php render_component('breadcrumb', ['items' => [['label' => 'Home', 'href' => '/'], ['label' => 'Documentation']]]); ?>
    <h1 class="mt-6 text-4xl sm:text-5xl font-extrabold tracking-tight text-zp-white">Documentation Portal</h1>
    <p class="mt-4 text-zp-muted">Search-ready docs with markdown rendering, table of contents, copyable code blocks, and sequential navigation.</p>

    <div class="mt-8 grid sm:grid-cols-2 gap-4" data-animate-stagger>
        <?php foreach (($documents ?? []) as $doc): ?>
            <a href="/docs/<?= htmlspecialchars($doc['slug'], ENT_QUOTES, 'UTF-8') ?>" class="group block relative rounded-2xl border border-zp-border bg-zp-surface p-5 shadow-sm hover:shadow-lg hover:shadow-cyan-500/5 dark:hover:shadow-cyan-500/5 hover:-translate-y-0.5 focus-ring transition-all duration-300">
                <h2 class="font-semibold text-lg text-zp-white group-hover:text-cyan-400 transition-colors"><?= htmlspecialchars($doc['title'], ENT_QUOTES, 'UTF-8') ?></h2>
                <p class="mt-2 text-sm text-zp-muted"><?= htmlspecialchars($doc['description'], ENT_QUOTES, 'UTF-8') ?></p>
            </a>
        <?php endforeach; ?>
    </div>
</section>