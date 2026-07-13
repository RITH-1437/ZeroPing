<?php
$variant = $variant ?? 'primary';
$href = $href ?? '#';
$label = $label ?? 'Button';
?>
<?php if ($variant === 'secondary'): ?>
    <a href="<?= htmlspecialchars($href, ENT_QUOTES, 'UTF-8') ?>" class="inline-flex items-center gap-2 rounded-2xl border border-zp-border bg-zp-surface/50 px-5 py-2.5 text-sm font-semibold text-zp-white hover:bg-zp-surface hover:border-cyan-500/30 transition-all duration-200 focus-ring"><?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8') ?></a>
<?php else: ?>
    <a href="<?= htmlspecialchars($href, ENT_QUOTES, 'UTF-8') ?>" class="inline-flex items-center gap-2 rounded-2xl bg-gradient-to-r from-cyan-500 to-emerald-500 px-5 py-2.5 text-sm font-semibold text-zp-bg shadow-lg shadow-cyan-500/20 hover:shadow-xl hover:shadow-cyan-500/30 hover:scale-[1.02] active:scale-[0.98] transition-all duration-200 focus-ring"><?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8') ?></a>
<?php endif; ?>
