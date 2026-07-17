<?php
$previous = $previous ?? null;
$next = $next ?? null;
$hasAny = $previous || $next;
if (!$hasAny) {
    return;
}
?>
<nav class="mt-10 border-t border-zp-border/50 pt-6 grid sm:grid-cols-2 gap-4" aria-label="Docs pagination">
    <div>
        <?php if ($previous): ?>
            <a href="/docs/<?= htmlspecialchars($previous['slug'], ENT_QUOTES, 'UTF-8') ?>" class="group relative flex items-center gap-3 overflow-hidden rounded-xl border border-zp-border bg-zp-surface/40 px-4 py-3.5 hover:border-emerald-500/40 hover:bg-emerald-500/5 hover:shadow-lg hover:shadow-emerald-500/10 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500/50 transition-all duration-200">
                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg border border-zp-border bg-zp-surface text-zp-muted group-hover:border-emerald-500/40 group-hover:text-emerald-400 group-hover:bg-emerald-500/10 transition-all duration-200">
                    <svg class="h-4 w-4 -translate-x-0.5 group-hover:-translate-x-1 transition-transform duration-200" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="m15 19-7-7 7-7"/></svg>
                </span>
                <span class="min-w-0 text-left">
                    <span class="mt-0.5 block truncate font-semibold text-zp-ink group-hover:text-emerald-400 transition-colors"><?= htmlspecialchars($previous['title'], ENT_QUOTES, 'UTF-8') ?></span>
                </span>
            </a>
        <?php endif; ?>
    </div>
    <div class="sm:text-right">
        <?php if ($next): ?>
            <a href="/docs/<?= htmlspecialchars($next['slug'], ENT_QUOTES, 'UTF-8') ?>" class="group relative flex items-center justify-end gap-3 overflow-hidden rounded-xl border border-zp-border bg-zp-surface/40 px-4 py-3.5 hover:border-emerald-500/40 hover:bg-emerald-500/5 hover:shadow-lg hover:shadow-emerald-500/10 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500/50 transition-all duration-200">
                <span class="min-w-0 text-right">
                    <span class="mt-0.5 block truncate font-semibold text-zp-ink group-hover:text-emerald-400 transition-colors"><?= htmlspecialchars($next['title'], ENT_QUOTES, 'UTF-8') ?></span>
                </span>
                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg border border-zp-border bg-zp-surface text-zp-muted group-hover:border-emerald-500/40 group-hover:text-emerald-400 group-hover:bg-emerald-500/10 transition-all duration-200">
                    <svg class="h-4 w-4 translate-x-0.5 group-hover:translate-x-1 transition-transform duration-200" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="m9 5 7 7-7 7"/></svg>
                </span>
            </a>
        <?php endif; ?>
    </div>
</nav>
