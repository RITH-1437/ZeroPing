<?php
$icon = $icon ?? '';
$title = $title ?? 'Feature';
$description = $description ?? '';
?>
<article class="group relative rounded-2xl border border-zp-border bg-zp-surface/50 p-6 hover:bg-zp-surface hover:border-cyan-500/20 hover:shadow-lg hover:shadow-cyan-500/5 hover:-translate-y-1 transition-all duration-300" data-animate>
      <div class="absolute inset-0 rounded-2xl bg-zp-primary/5 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
    <div class="relative">
        <?php if ($icon): ?>
            <div class="inline-flex items-center justify-center h-12 w-12 rounded-xl bg-zp-surface border border-zp-border text-zp-link transition-all duration-300 group-hover:scale-110 group-hover:border-cyan-500/40 group-hover:shadow-lg group-hover:shadow-cyan-500/10" aria-hidden="true">
                <?= $icon ?>
            </div>
        <?php endif; ?>
        <h3 class="mt-4 text-lg font-semibold text-zp-ink group-hover:text-zp-link transition-colors duration-300"><?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?></h3>
        <p class="mt-2 text-sm text-zp-desc leading-relaxed"><?= htmlspecialchars($description, ENT_QUOTES, 'UTF-8') ?></p>
    </div>
</article>
