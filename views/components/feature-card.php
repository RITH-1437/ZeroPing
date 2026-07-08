<?php
$icon = $icon ?? '⚡';
$title = $title ?? 'Feature';
$description = $description ?? '';
?>
<article class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white/95 dark:bg-slate-900/85 p-6 shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all duration-200">
    <div class="text-2xl" aria-hidden="true"><?= htmlspecialchars($icon, ENT_QUOTES, 'UTF-8') ?></div>
    <h3 class="mt-4 text-xl font-semibold text-slate-900 dark:text-white"><?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?></h3>
    <p class="mt-2 text-slate-600 dark:text-slate-300 leading-7"><?= htmlspecialchars($description, ENT_QUOTES, 'UTF-8') ?></p>
</article>
