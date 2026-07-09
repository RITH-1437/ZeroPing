<?php require_once __DIR__ . '/../components/component.php'; ?>
<section class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
    <?php render_component('breadcrumb', ['items' => [['label' => 'Home', 'href' => '/'], ['label' => 'Documentation', 'href' => '/docs/introduction'], ['label' => $currentDoc['title'] ?? '']]]); ?>
    <div class="mt-6 grid lg:grid-cols-[260px_minmax(0,1fr)_200px] gap-6">
        <?php render_component('sidebar', ['documents' => $documents ?? [], 'currentSlug' => $currentDoc['slug'] ?? '']); ?>

        <article class="min-w-0 rounded-2xl border border-slate-200/70 dark:border-slate-800/70 bg-white/80 dark:bg-slate-900/80 p-6 lg:p-10 shadow-sm" data-animate>
            <div class="prose prose-slate dark:prose-invert max-w-none
                prose-headings:scroll-mt-28 prose-headings:font-semibold prose-headings:text-slate-900 dark:prose-headings:text-slate-100
                prose-a:text-blue-600 dark:prose-a:text-blue-400 prose-a:font-medium
                prose-strong:text-slate-900 dark:prose-strong:text-slate-100
                prose-code:px-1 prose-code:py-0.5 prose-code:rounded prose-code:bg-slate-100 dark:prose-code:bg-slate-800 prose-code:text-sm prose-code:font-normal
                prose-pre:bg-transparent prose-pre:p-0
                prose-blockquote:border-blue-500 prose-blockquote:bg-blue-50/50 dark:prose-blockquote:bg-blue-950/30 prose-blockquote:py-1 prose-blockquote:px-4 prose-blockquote:rounded-r-lg
                prose-li:marker:text-slate-400 dark:prose-li:marker:text-slate-500">
                <?= $docHtml ?? '' ?>
            </div>
            <?php render_component('pagination', ['previous' => $previous ?? null, 'next' => $next ?? null]); ?>
        </article>

        <aside class="hidden lg:block lg:sticky lg:top-28 h-fit rounded-2xl border border-slate-200/70 dark:border-slate-800/70 bg-white/80 dark:bg-slate-900/80 p-4 shadow-sm" data-toc>
            <h2 class="text-xs font-semibold uppercase tracking-widest text-slate-500 dark:text-slate-400">On this page</h2>
            <nav class="mt-3" aria-label="Table of contents">
                <ul class="space-y-1 text-sm">
                    <?php foreach (($toc ?? []) as $item): ?>
                        <li style="padding-left: <?= (($item['level'] ?? 2) - 2) * 12 ?>px">
                            <a href="#<?= htmlspecialchars($item['id'], ENT_QUOTES, 'UTF-8') ?>" data-toc-link class="block rounded px-2 py-1 text-slate-500 hover:text-blue-600 dark:text-slate-400 dark:hover:text-blue-400 focus-ring transition-all duration-200 border-l-2 border-transparent hover:border-blue-500/50">
                                <?= htmlspecialchars($item['title'], ENT_QUOTES, 'UTF-8') ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </nav>
        </aside>
    </div>
</section>
<script>
(function() {
    const tocLinks = document.querySelectorAll('[data-toc-link]');
    const headings = [];
    tocLinks.forEach(link => {
        const id = link.getAttribute('href').slice(1);
        const el = document.getElementById(id);
        if (el) headings.push({ el, link });
    });
    if (!headings.length) return;

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                tocLinks.forEach(l => {
                    l.classList.remove('text-blue-600', 'dark:text-blue-400', 'font-medium', 'border-blue-500/70');
                    l.classList.add('border-transparent');
                });
                const match = headings.find(h => h.el === entry.target);
                if (match) {
                    match.link.classList.add('text-blue-600', 'dark:text-blue-400', 'font-medium', 'border-blue-500/70');
                    match.link.classList.remove('border-transparent');
                }
            }
        });
    }, { threshold: 0, rootMargin: '-80px 0px -60% 0px' });

    headings.forEach(h => observer.observe(h.el));
})();
</script>