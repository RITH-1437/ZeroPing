<?php
$previous = $previous ?? null;
$next = $next ?? null;
?>
<nav class="mt-10 border-t border-zp-border/50 pt-6 grid sm:grid-cols-2 gap-3" aria-label="Docs pagination">
    <div>
        <?php if ($previous): ?>
            <a href="/docs/<?= htmlspecialchars($previous['slug'], ENT_QUOTES, 'UTF-8') ?>" class="group block rounded-xl border border-zp-border p-4 hover:bg-zp-surface/50 hover:border-zp-border focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-zp-link transition-all duration-200">
                <p class="text-xs text-zp-muted flex items-center gap-1">
                    <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="m15 19-7-7 7-7"/></svg>
                    Previous
                </p>
                <p class="mt-1.5 font-medium text-zp-ink group-hover:text-zp-link transition-colors"><?= htmlspecialchars($previous['title'], ENT_QUOTES, 'UTF-8') ?></p>
            </a>
        <?php endif; ?>
    </div>
    <div class="sm:text-right">
        <?php if ($next): ?>
            <a href="/docs/<?= htmlspecialchars($next['slug'], ENT_QUOTES, 'UTF-8') ?>" class="group block rounded-xl border border-zp-border p-4 hover:bg-zp-surface/50 hover:border-zp-border focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-zp-link transition-all duration-200">
                <p class="text-xs text-zp-muted flex items-center gap-1 sm:justify-end">
                    Next
                    <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="m9 5 7 7-7 7"/></svg>
                </p>
                <p class="mt-1.5 font-medium text-zp-ink group-hover:text-zp-link transition-colors"><?= htmlspecialchars($next['title'], ENT_QUOTES, 'UTF-8') ?></p>
            </a>
        <?php endif; ?>
    </div>
</nav>