<?php require_once __DIR__ . '/../components/component.php'; ?>
<section class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
    <?php render_component('breadcrumb', ['items' => [['label' => 'Home', 'href' => '/'], ['label' => 'Documentation', 'href' => '/docs/introduction'], ['label' => $currentDoc['title'] ?? '']]]); ?>
    <div class="mt-6 grid lg:grid-cols-[260px_minmax(0,1fr)_200px] gap-6">
        <?php render_component('sidebar', ['documents' => $documents ?? [], 'currentSlug' => $currentDoc['slug'] ?? '']); ?>

        <article class="min-w-0 rounded-2xl border border-zp-border bg-zp-surface p-6 lg:p-10 shadow-sm">
            <div class="prose prose-slate dark:prose-invert max-w-none
                prose-headings:scroll-mt-28 prose-headings:font-semibold prose-headings:text-zp-ink
                prose-a:text-zp-link prose-a:font-medium
                prose-strong:text-zp-ink
                prose-code:px-1 prose-code:py-0.5 prose-code:rounded prose-code:text-sm prose-code:font-normal prose-code:font-[inherit]
                prose-pre:bg-transparent prose-pre:p-0
                prose-blockquote:border-cyan-500/40 prose-blockquote:bg-cyan-500/5 prose-blockquote:py-1 prose-blockquote:px-4 prose-blockquote:rounded-r-lg
                prose-li:marker:text-zp-muted">
                <?= $docHtml ?? '' ?>
            </div>
            <?php render_component('pagination', ['previous' => $previous ?? null, 'next' => $next ?? null]); ?>
        </article>

        <aside class="hidden lg:block lg:sticky lg:top-28 h-fit rounded-2xl border border-zp-border bg-zp-surface p-4 shadow-sm" data-toc>
            <h2 class="text-xs font-semibold uppercase tracking-widest text-zp-muted">On this page</h2>
            <nav class="mt-3" aria-label="Table of contents">
                <ul class="space-y-1 text-sm">
                    <?php foreach (($toc ?? []) as $item): ?>
                        <li style="padding-left: <?= (($item['level'] ?? 2) - 2) * 12 ?>px">
                            <a href="#<?= htmlspecialchars($item['id'], ENT_QUOTES, 'UTF-8') ?>" data-toc-link class="block rounded px-2 py-1 text-zp-muted hover:text-zp-link focus-ring transition-all duration-200 border-l-2 border-transparent hover:border-cyan-500/50">
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
                    l.classList.remove('text-zp-link', 'font-medium', 'border-cyan-500/70');
                    l.classList.add('border-transparent');
                });
                const match = headings.find(h => h.el === entry.target);
                if (match) {
                    match.link.classList.add('text-zp-link', 'font-medium', 'border-cyan-500/70');
                    match.link.classList.remove('border-transparent');
                }
            }
        });
    }, { threshold: 0, rootMargin: '-80px 0px -60% 0px' });

    headings.forEach(h => observer.observe(h.el));
})();
</script>