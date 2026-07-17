<?php $items = $items ?? []; ?>
<?php if (!empty($items)): ?>
<nav aria-label="Breadcrumb">
    <ol class="flex flex-wrap items-center gap-2 text-sm text-zp-muted">
        <?php foreach ($items as $idx => $item):
            $isLast = $idx === count($items) - 1; ?>
            <?php if ($idx > 0): ?>
                <svg class="h-3.5 w-3.5 text-zp-muted/50" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="m9 5 7 7-7 7"/></svg>
            <?php endif; ?>
            <?php if (!empty($item['href']) && !$isLast): ?>
                <a href="<?= htmlspecialchars($item['href'], ENT_QUOTES, 'UTF-8') ?>" class="rounded px-1 hover:text-zp-link focus-ring transition-colors"><?= htmlspecialchars($item['label'], ENT_QUOTES, 'UTF-8') ?></a>
            <?php else: ?>
                <span class="text-zp-ink font-medium"><?= htmlspecialchars($item['label'], ENT_QUOTES, 'UTF-8') ?></span>
            <?php endif; ?>
        <?php endforeach; ?>
    </ol>
</nav>
<?php endif; ?>
