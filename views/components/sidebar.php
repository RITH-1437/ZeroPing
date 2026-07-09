<?php
$documents = $documents ?? [];
$currentSlug = $currentSlug ?? '';
?>
<aside class="hidden lg:block lg:sticky lg:top-28 h-fit rounded-2xl border border-slate-200/70 dark:border-slate-800/70 bg-white/80 dark:bg-slate-900/80 p-4 shadow-sm max-h-[calc(100vh-8rem)] overflow-y-auto">
    <label for="doc-search" class="sr-only">Search documentation</label>
    <div class="relative">
        <svg class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-slate-400 pointer-events-none" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="m21 21-6-6m2-5a7 7 0 1 1-14 0 7 7 0 0 1 14 0z"/></svg>
        <input id="doc-search" type="search" placeholder="Search docs..." class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-950/50 pl-9 pr-3 py-2 text-sm text-slate-800 dark:text-slate-100 placeholder:text-slate-400 dark:placeholder:text-slate-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200">
    </div>
    <nav class="mt-4" aria-label="Documentation sidebar">
        <ul class="space-y-1">
            <?php foreach ($documents as $doc): ?>
                <li data-doc-link>
                    <a
                        href="/docs/<?= htmlspecialchars($doc['slug'], ENT_QUOTES, 'UTF-8') ?>"
                        class="block rounded-lg px-3 py-2 text-sm font-medium focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 transition-all duration-200 <?= $currentSlug === $doc['slug'] ? 'bg-gradient-to-r from-blue-600 to-indigo-600 text-white shadow-md' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100 dark:text-slate-400 dark:hover:text-white dark:hover:bg-slate-800/60' ?>"
                    >
                        <?= htmlspecialchars($doc['title'], ENT_QUOTES, 'UTF-8') ?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    </nav>
</aside>