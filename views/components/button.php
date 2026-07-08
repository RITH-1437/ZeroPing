<?php
$label = $label ?? 'Button';
$href = $href ?? '#';
$variant = $variant ?? 'primary';
$classes = $variant === 'secondary'
    ? 'bg-white text-slate-900 border border-slate-300 hover:bg-slate-100 dark:bg-slate-900 dark:text-slate-100 dark:border-slate-700 dark:hover:bg-slate-800'
    : 'bg-gradient-to-r from-blue-600 to-emerald-500 text-white shadow-sm hover:brightness-110';
?>
<a href="<?= htmlspecialchars($href, ENT_QUOTES, 'UTF-8') ?>" class="inline-flex items-center justify-center rounded-xl px-5 py-2.5 text-sm font-medium transition-colors duration-200 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 <?= $classes ?>">
    <?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8') ?>
</a>
