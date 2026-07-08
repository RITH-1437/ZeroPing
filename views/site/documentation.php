<?php require_once __DIR__ . '/../components/component.php'; ?>
<section class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
    <?php render_component('breadcrumb', ['items' => [['label' => 'Home', 'href' => '/'], ['label' => 'Documentation', 'href' => '/docs/introduction'], ['label' => $currentDoc['title'] ?? '']]]); ?>
    <div class="mt-6 grid lg:grid-cols-[260px_minmax(0,1fr)_220px] gap-6">
        <?php render_component('sidebar', ['documents' => $documents ?? [], 'currentSlug' => $currentDoc['slug'] ?? '']); ?>

        <article class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white/95 dark:bg-slate-900/85 p-6 lg:p-8">
            <?= $docHtml ?? '' ?>
            <?php render_component('pagination', ['previous' => $previous ?? null, 'next' => $next ?? null]); ?>
        </article>

        <aside class="hidden lg:block lg:sticky lg:top-24 h-fit rounded-2xl border border-slate-200 dark:border-slate-800 bg-white/95 dark:bg-slate-900/85 p-4">
            <h2 class="text-sm font-semibold text-slate-900 dark:text-white">On this page</h2>
            <nav class="mt-3" aria-label="Table of contents">
                <ul class="space-y-2 text-sm">
                    <?php foreach (($toc ?? []) as $item): ?>
                        <li class="<?= ($item['level'] ?? 2) > 2 ? 'pl-3' : '' ?>">
                            <a href="#<?= htmlspecialchars($item['id'], ENT_QUOTES, 'UTF-8') ?>" class="rounded px-1 text-slate-700 hover:text-blue-600 dark:text-slate-300 dark:hover:text-blue-400 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500">
                                <?= htmlspecialchars($item['title'], ENT_QUOTES, 'UTF-8') ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </nav>
        </aside>
    </div>
</section>
