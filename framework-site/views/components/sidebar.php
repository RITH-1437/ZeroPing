<?php
$documents = $documents ?? [];
$currentSlug = $currentSlug ?? '';
?>
<aside class="hidden lg:block lg:sticky lg:top-28 h-fit rounded-2xl border border-zp-border bg-zp-surface p-4 shadow-sm max-h-[calc(100vh-8rem)] overflow-y-auto">
    <label for="doc-search" class="sr-only">Search documentation</label>
    <div class="relative">
        <svg class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-zp-muted pointer-events-none" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="m21 21-6-6m2-5a7 7 0 1 1-14 0 7 7 0 0 1 14 0z"/></svg>
        <input id="doc-search" type="search" placeholder="Search docs..." class="w-full rounded-xl border border-zp-border bg-zp-bg pl-9 pr-3 py-2 text-sm text-zp-ink placeholder:text-zp-muted focus:outline-none focus:ring-2 focus:ring-zp-link focus:border-zp-link transition-all duration-200">
    </div>
    <nav class="mt-4" aria-label="Documentation sidebar">
        <ul class="space-y-1">
            <?php foreach ($documents as $doc): ?>
                <li data-doc-link>
                    <a
                        href="/docs/<?= htmlspecialchars($doc['slug'], ENT_QUOTES, 'UTF-8') ?>"
                        class="block rounded-lg px-3 py-2 text-sm font-medium focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-zp-link transition-all duration-200 <?= $currentSlug === $doc['slug'] ? 'bg-zp-primary text-zp-ink shadow-md' : 'text-zp-muted hover:text-zp-ink hover:bg-zp-surface' ?>"
                    >
                        <?= htmlspecialchars($doc['title'], ENT_QUOTES, 'UTF-8') ?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    </nav>
</aside>