<?php
$items = $items ?? [];
?>
<nav aria-label="Breadcrumb">
    <ol class="flex flex-wrap items-center gap-2 text-sm text-slate-500 dark:text-slate-400">
        <?php foreach ($items as $index => $item): ?>
            <li class="inline-flex items-center gap-2">
                <?php if (!empty($item['href'])): ?>
                    <a href="<?= htmlspecialchars($item['href'], ENT_QUOTES, 'UTF-8') ?>" class="rounded px-1 hover:text-blue-600 dark:hover:text-blue-400 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 transition-colors">
                        <?= htmlspecialchars($item['label'], ENT_QUOTES, 'UTF-8') ?>
                    </a>
                <?php else: ?>
                    <span class="text-slate-900 dark:text-slate-100 font-medium"><?= htmlspecialchars($item['label'], ENT_QUOTES, 'UTF-8') ?></span>
                <?php endif; ?>
                <?php if ($index < count($items) - 1): ?>
                    <svg class="h-3.5 w-3.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="m9 5 7 7-7 7"/></svg>
                <?php endif; ?>
            </li>
        <?php endforeach; ?>
    </ol>
</nav>