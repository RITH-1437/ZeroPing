<?php $label = $label ?? 'Badge'; ?>
<span class="inline-flex items-center gap-1.5 rounded-full border border-cyan-500/20 bg-cyan-500/5 px-3 py-1 text-xs font-medium text-zp-link">
    <span class="h-1.5 w-1.5 rounded-full bg-zp-link animate-pulse-glow"></span>
    <?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8') ?>
</span>
