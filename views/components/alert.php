<?php
$type = $type ?? 'info';
$message = $message ?? '';
$map = [
    'info' => 'border-blue-200 bg-blue-50 text-blue-800 dark:border-blue-900 dark:bg-blue-950 dark:text-blue-200',
    'success' => 'border-emerald-200 bg-emerald-50 text-emerald-800 dark:border-emerald-900 dark:bg-emerald-950 dark:text-emerald-200',
    'warning' => 'border-amber-200 bg-amber-50 text-amber-800 dark:border-amber-900 dark:bg-amber-950 dark:text-amber-200',
];
$style = $map[$type] ?? $map['info'];
?>
<div class="rounded-xl border px-4 py-3 text-sm <?= $style ?>" role="status">
    <?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?>
</div>
