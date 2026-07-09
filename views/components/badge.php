<?php $label = $label ?? 'Badge'; ?>
<span class="inline-flex items-center gap-1.5 rounded-full border border-blue-200/60 bg-blue-50/80 px-3 py-1 text-xs font-medium text-blue-700 dark:border-blue-900/60 dark:bg-blue-950/50 dark:text-blue-300">
    <span class="h-1.5 w-1.5 rounded-full bg-blue-500 dark:bg-blue-400 animate-pulse-glow"></span>
    <?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8') ?>
</span>