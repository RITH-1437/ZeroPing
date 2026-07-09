<?php
$icon = $icon ?? '';
$title = $title ?? 'Feature';
$description = $description ?? '';
?>
<article class="group relative rounded-2xl border border-slate-200/70 dark:border-slate-800/70 bg-white/80 dark:bg-slate-900/80 p-6 shadow-sm hover:shadow-xl hover:shadow-blue-500/10 dark:hover:shadow-blue-500/10 hover:-translate-y-1.5 transition-all duration-300" data-animate>
    <div class="absolute inset-0 rounded-2xl bg-gradient-to-br from-blue-500/[0.02] to-indigo-500/[0.02] dark:from-blue-500/[0.04] dark:to-indigo-500/[0.04] opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
    <div class="absolute -inset-px rounded-2xl border border-transparent group-hover:border-blue-300/40 dark:group-hover:border-blue-700/40 transition-colors duration-300 pointer-events-none"></div>
    <div class="relative">
        <?php if ($icon): ?>
            <div class="inline-flex items-center justify-center h-12 w-12 rounded-xl bg-gradient-to-br from-blue-50 to-indigo-50 dark:from-blue-950/60 dark:to-indigo-950/60 ring-1 ring-blue-200/50 dark:ring-blue-800/50 transition-all duration-300 group-hover:scale-110 group-hover:ring-blue-300/70 dark:group-hover:ring-blue-700/70 group-hover:shadow-lg group-hover:shadow-blue-500/20 dark:group-hover:shadow-blue-500/10" aria-hidden="true">
                <?= $icon ?>
            </div>
        <?php endif; ?>
        <h3 class="mt-4 text-lg font-semibold text-slate-900 dark:text-slate-100 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors duration-300"><?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?></h3>
        <p class="mt-2 text-sm text-slate-600 dark:text-slate-400 leading-relaxed"><?= htmlspecialchars($description, ENT_QUOTES, 'UTF-8') ?></p>
    </div>
</article>