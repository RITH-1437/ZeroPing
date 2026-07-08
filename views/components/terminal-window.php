<?php
$title = $title ?? 'Terminal';
$lines = $lines ?? [];
?>
<section class="rounded-2xl border border-slate-300 dark:border-slate-800 overflow-hidden bg-slate-950 text-slate-100 shadow-lg">
    <div class="flex items-center justify-between px-4 py-3 border-b border-slate-800 bg-slate-900/80">
        <div class="flex items-center gap-2" aria-hidden="true">
            <span class="h-3 w-3 rounded-full bg-red-400"></span>
            <span class="h-3 w-3 rounded-full bg-yellow-400"></span>
            <span class="h-3 w-3 rounded-full bg-emerald-400"></span>
        </div>
        <p class="text-xs text-slate-300"><?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?></p>
    </div>
    <div class="px-4 py-5 text-sm leading-7 font-mono overflow-x-auto">
        <?php foreach ($lines as $line): ?>
            <div><?= htmlspecialchars($line, ENT_QUOTES, 'UTF-8') ?></div>
        <?php endforeach; ?>
    </div>
</section>
