<?php require_once __DIR__ . '/../components/component.php'; ?>
<section class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8" data-animate>
    <?php render_component('breadcrumb', ['items' => [['label' => 'Home', 'href' => '/'], ['label' => $eyebrow ?? 'Explore']]]); ?>
    <div class="relative mt-6 overflow-hidden rounded-3xl border border-zp-border bg-zp-surface/75 px-6 py-10 shadow-sm sm:px-10 sm:py-14">
        <div class="absolute -right-24 -top-28 h-72 w-72 rounded-full bg-teal-400/10 blur-3xl" aria-hidden="true"></div>
        <div class="relative max-w-3xl">
            <p class="text-xs font-bold uppercase tracking-[.16em] text-zp-link"><?= htmlspecialchars($eyebrow ?? '', ENT_QUOTES, 'UTF-8') ?></p>
            <h1 class="mt-4 font-display text-4xl font-bold tracking-[-.045em] text-zp-ink sm:text-5xl"><?= htmlspecialchars($headline ?? '', ENT_QUOTES, 'UTF-8') ?></h1>
            <p class="mt-5 max-w-2xl text-base leading-7 text-zp-desc sm:text-lg"><?= htmlspecialchars($description ?? '', ENT_QUOTES, 'UTF-8') ?></p>
            <?php if (!empty($actions)): ?><div class="mt-7 flex flex-wrap gap-3"><?php foreach ($actions as $index => $action): ?><a href="<?= htmlspecialchars($action['href'], ENT_QUOTES, 'UTF-8') ?>" <?= str_starts_with($action['href'], 'http') ? 'target="_blank" rel="noopener noreferrer"' : '' ?> class="<?= $index === 0 ? 'bg-zp-primary text-zp-ink shadow-md shadow-teal-500/20 hover:bg-zp-primary-hover' : 'border border-zp-border bg-zp-surface/70 text-zp-ink hover:border-teal-500/30 hover:bg-teal-500/5' ?> inline-flex items-center gap-2 rounded-xl px-4 py-2.5 text-sm font-bold transition focus-ring"><?= htmlspecialchars($action['label'], ENT_QUOTES, 'UTF-8') ?><?= str_starts_with($action['href'], 'http') ? '<span aria-hidden="true">↗</span>' : '' ?></a><?php endforeach; ?></div><?php endif; ?>
        </div>
    </div>

    <?php foreach (($sections ?? []) as $section): ?>
        <div class="mt-16">
            <div class="max-w-2xl"><h2 class="font-display text-2xl font-bold tracking-[-.03em] text-zp-ink"><?= htmlspecialchars($section['title'], ENT_QUOTES, 'UTF-8') ?></h2><p class="mt-2 text-sm leading-6 text-zp-desc"><?= htmlspecialchars($section['intro'], ENT_QUOTES, 'UTF-8') ?></p></div>
            <div class="mt-6 grid gap-4 <?= count($section['items']) > 1 ? 'md:grid-cols-2 lg:grid-cols-3' : 'max-w-3xl' ?>">
                <?php foreach ($section['items'] as $item): ?>
                    <article class="group rounded-2xl border border-zp-border bg-zp-surface/70 p-5 shadow-sm transition duration-300 hover:-translate-y-0.5 hover:border-teal-500/30 hover:shadow-lg hover:shadow-teal-500/5">
                        <div class="flex items-center justify-between gap-3"><h3 class="font-semibold text-zp-ink"><?= htmlspecialchars($item['title'], ENT_QUOTES, 'UTF-8') ?></h3><?php if (!empty($item['tag'])): ?><span class="rounded-full border border-teal-500/15 bg-teal-500/10 px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider text-zp-link"><?= htmlspecialchars($item['tag'], ENT_QUOTES, 'UTF-8') ?></span><?php endif; ?></div>
                        <p class="mt-3 text-sm leading-6 text-zp-desc"><?= htmlspecialchars($item['description'], ENT_QUOTES, 'UTF-8') ?></p>
                        <?php if (!empty($item['code'])): ?><?php render_component('code-block', ['title' => $item['tag'] ?? 'terminal', 'language' => str_contains($item['code'], 'Router') ? 'PHP' : 'bash', 'codeId' => 'hub-' . md5($item['title']), 'code' => $item['code']]); ?><?php elseif (!empty($item['href'])): ?><a href="<?= htmlspecialchars($item['href'], ENT_QUOTES, 'UTF-8') ?>" class="mt-5 inline-flex items-center gap-1 text-sm font-bold text-zp-link transition group-hover:gap-2 focus-ring"><?= htmlspecialchars($item['cta'] ?? 'Explore', ENT_QUOTES, 'UTF-8') ?><span aria-hidden="true">→</span></a><?php endif; ?>
                    </article>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endforeach; ?>
</section>
