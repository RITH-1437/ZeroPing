<?php
$type = $type ?? 'info';
$message = $message ?? '';
$map = [
    'info' => 'border-cyan-200/60 bg-cyan-50/80 text-cyan-800 dark:border-cyan-900/60 dark:bg-cyan-950/50 dark:text-cyan-200',
    'success' => 'border-emerald-200/60 bg-emerald-50/80 text-emerald-800 dark:border-emerald-900/60 dark:bg-emerald-950/50 dark:text-emerald-200',
    'warning' => 'border-amber-200/60 bg-amber-50/80 text-amber-800 dark:border-amber-900/60 dark:bg-amber-950/50 dark:text-amber-200',
];
$icons = [
    'info' => '<svg class="h-4 w-4 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>',
    'success' => '<svg class="h-4 w-4 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>',
    'warning' => '<svg class="h-4 w-4 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4.5c-.77-.833-2.694-.833-3.464 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>',
];
$style = $map[$type] ?? $map['info'];
$icon = $icons[$type] ?? $icons['info'];
?>
<div class="flex items-start gap-3 rounded-xl border px-4 py-3 text-sm <?= $style ?>" role="status">
    <?= $icon ?>
    <span><?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?></span>
</div>