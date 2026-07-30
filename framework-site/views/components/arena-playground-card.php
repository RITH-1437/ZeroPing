<?php
/** @var array<string, string> $tool */
?>
<article class="arena-tool-card <?= ($tool['preview'] ?? '') === 'benchmark' ? 'arena-tool-card--featured' : '' ?>">
    <div class="arena-tool-heading">
        <span class="arena-icon arena-icon--small" aria-hidden="true"><svg><use href="#arena-icon-<?= htmlspecialchars($tool['icon'], ENT_QUOTES, 'UTF-8') ?>"></use></svg></span>
        <span class="arena-status arena-status--<?= htmlspecialchars($tool['tone'], ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($tool['status'], ENT_QUOTES, 'UTF-8') ?></span>
    </div>
    <h3><?= htmlspecialchars($tool['title'], ENT_QUOTES, 'UTF-8') ?></h3>
    <p><?= htmlspecialchars($tool['description'], ENT_QUOTES, 'UTF-8') ?></p>
    <div class="arena-tool-preview arena-tool-preview--<?= htmlspecialchars($tool['preview'], ENT_QUOTES, 'UTF-8') ?>"<?= ($tool['preview'] ?? '') === 'benchmark' ? '' : ' aria-hidden="true"' ?>>
        <?php if (($tool['preview'] ?? '') === 'benchmark'): ?>
            <div class="benchmark-readout" role="status" aria-live="polite"><span data-benchmark-value>0.82</span><small>ms</small></div>
            <div class="benchmark-sparkline"><i></i><i></i><i></i><i></i><i></i><i></i><i></i><i></i><i></i></div>
            <button type="button" data-run-benchmark><svg aria-hidden="true"><use href="#arena-icon-play"></use></svg><span>Run local simulation</span></button>
        <?php elseif (($tool['preview'] ?? '') === 'bars'): ?>
            <div class="preview-bars"><i style="--bar:92%"></i><i style="--bar:72%"></i><i style="--bar:58%"></i><i style="--bar:43%"></i></div>
        <?php elseif (($tool['preview'] ?? '') === 'memory'): ?>
            <div class="preview-ring"><span>12<small>MB</small></span></div><div class="preview-legend"><i></i><i></i><i></i></div>
        <?php elseif (($tool['preview'] ?? '') === 'routing'): ?>
            <div class="preview-route"><span>GET</span><code>/projects/{id}</code><b>0.8 ms</b></div>
        <?php elseif (($tool['preview'] ?? '') === 'orm'): ?>
            <div class="preview-query"><span>SELECT</span><i></i><i></i><i></i></div>
        <?php elseif (($tool['preview'] ?? '') === 'http'): ?>
            <div class="preview-http"><span>200</span><i></i><i></i><i></i></div>
        <?php elseif (($tool['preview'] ?? '') === 'component'): ?>
            <div class="preview-component"><i></i><span></span><span></span></div>
        <?php elseif (($tool['preview'] ?? '') === 'ai'): ?>
            <div class="preview-ai"><span></span><i></i><i></i></div>
        <?php else: ?>
            <div class="preview-code"><i></i><i></i><i></i><i></i></div>
        <?php endif; ?>
    </div>
</article>
