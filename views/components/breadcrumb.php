<?php
$items = $items ?? [];
?>
<nav aria-label="Breadcrumb">
    <ol class="flex flex-wrap items-center gap-2 text-sm text-slate-600 dark:text-slate-400">
        <?php foreach ($items as $index => $item): ?>
            <li class="inline-flex items-center gap-2">
                <?php if (!empty($item['href'])): ?>
                    <a href="<?= htmlspecialchars($item['href'], ENT_QUOTES, 'UTF-8') ?>" class="rounded px-1 hover:text-blue-600 dark:hover:text-blue-400 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500">
                        <?= htmlspecialchars($item['label'], ENT_QUOTES, 'UTF-8') ?>
                    </a>
                <?php else: ?>
                    <span class="text-slate-900 dark:text-slate-100"><?= htmlspecialchars($item['label'], ENT_QUOTES, 'UTF-8') ?></span>
                <?php endif; ?>
                <?php if ($index < count($items) - 1): ?>
                    <span aria-hidden="true">/</span>
                <?php endif; ?>
            </li>
        <?php endforeach; ?>
    </ol>
</nav>
