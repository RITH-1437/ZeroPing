<?php
$previous = $previous ?? null;
$next = $next ?? null;
?>
<nav class="mt-10 border-t border-slate-200 dark:border-slate-800 pt-6 grid sm:grid-cols-2 gap-3" aria-label="Docs pagination">
    <div>
        <?php if ($previous): ?>
            <a href="/docs/<?= htmlspecialchars($previous['slug'], ENT_QUOTES, 'UTF-8') ?>" class="block rounded-xl border border-slate-200 dark:border-slate-800 p-4 hover:bg-slate-100 dark:hover:bg-slate-900 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500">
                <p class="text-xs text-slate-500">Previous</p>
                <p class="mt-1 font-medium"><?= htmlspecialchars($previous['title'], ENT_QUOTES, 'UTF-8') ?></p>
            </a>
        <?php endif; ?>
    </div>
    <div class="sm:text-right">
        <?php if ($next): ?>
            <a href="/docs/<?= htmlspecialchars($next['slug'], ENT_QUOTES, 'UTF-8') ?>" class="block rounded-xl border border-slate-200 dark:border-slate-800 p-4 hover:bg-slate-100 dark:hover:bg-slate-900 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500">
                <p class="text-xs text-slate-500">Next</p>
                <p class="mt-1 font-medium"><?= htmlspecialchars($next['title'], ENT_QUOTES, 'UTF-8') ?></p>
            </a>
        <?php endif; ?>
    </div>
</nav>
