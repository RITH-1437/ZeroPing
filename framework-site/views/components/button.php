<?php
$variant = $variant ?? 'primary';
$href = $href ?? '#';
$label = $label ?? 'Button';
?>
<?php if ($variant === 'secondary'): ?>
    <a href="<?= htmlspecialchars($href, ENT_QUOTES, 'UTF-8') ?>" class="inline-flex items-center gap-2 rounded-2xl border border-zp-border bg-zp-surface/50 px-5 py-2.5 text-sm font-semibold text-zp-ink hover:bg-zp-surface hover:border-cyan-500/30 transition-all duration-200 focus-ring"><?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8') ?></a>
<?php else: ?>
    <a href="<?= htmlspecialchars($href, ENT_QUOTES, 'UTF-8') ?>" class="inline-flex items-center gap-2 rounded-2xl bg-zp-primary px-5 py-2.5 text-sm font-semibold text-zp-ink shadow-md shadow-zp-primary/20 hover:bg-zp-primary-hover transition-all duration-200 focus-ring"><?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8') ?></a>
<?php endif; ?>
