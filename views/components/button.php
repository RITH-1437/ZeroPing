<?php
$label = $label ?? 'Button';
$href = $href ?? '#';
$variant = $variant ?? 'primary';
if ($variant === 'secondary') {
    $classes = 'border border-slate-300 text-slate-700 hover:bg-slate-100 active:bg-slate-200 dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-800/60 dark:active:bg-slate-700/60 shadow-sm';
} else {
    $classes = 'bg-gradient-to-r from-blue-600 via-indigo-600 to-blue-600 bg-[length:200%_200%] animate-gradient text-white shadow-lg shadow-blue-600/20 dark:shadow-blue-500/10 hover:shadow-xl hover:shadow-blue-600/25 dark:hover:shadow-blue-500/20 hover:scale-[1.02] active:scale-[0.98]';
}
?>
<a href="<?= htmlspecialchars($href, ENT_QUOTES, 'UTF-8') ?>" class="btn-ripple inline-flex items-center justify-center rounded-xl px-5 py-2.5 text-sm font-medium transition-all duration-300 focus-ring <?= $classes ?>">
    <?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8') ?>
</a>