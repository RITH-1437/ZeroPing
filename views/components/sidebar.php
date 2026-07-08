<?php
$documents = $documents ?? [];
$currentSlug = $currentSlug ?? '';
?>
<aside class="lg:sticky lg:top-24 h-fit rounded-2xl border border-slate-200 dark:border-slate-800 bg-white/95 dark:bg-slate-900/85 p-4">
    <label for="doc-search" class="sr-only">Search documentation</label>
    <input id="doc-search" type="search" placeholder="Search docs..." class="w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-transparent px-3 py-2 text-sm text-slate-800 dark:text-slate-100 placeholder:text-slate-500 dark:placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500">
    <nav class="mt-4" aria-label="Documentation sidebar">
        <ul class="space-y-1">
            <?php foreach ($documents as $doc): ?>
                <li data-doc-link>
                    <a
                        href="/docs/<?= htmlspecialchars($doc['slug'], ENT_QUOTES, 'UTF-8') ?>"
                        class="block rounded-lg px-3 py-2 text-sm font-medium focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 transition-colors <?= $currentSlug === $doc['slug'] ? 'bg-slate-900 text-white dark:bg-white dark:text-slate-900' : 'text-slate-700 hover:bg-slate-100 dark:text-slate-300 dark:hover:bg-slate-800' ?>"
                    >
                        <?= htmlspecialchars($doc['title'], ENT_QUOTES, 'UTF-8') ?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    </nav>
</aside>
