<?php
/** @var array<string, string> $item */
$href = $item['href'] ?? '#';
?>
<a class="arena-ecosystem-card" href="<?= htmlspecialchars($href, ENT_QUOTES, 'UTF-8') ?>">
    <span class="arena-icon" aria-hidden="true"><svg><use href="#arena-icon-<?= htmlspecialchars($item['icon'], ENT_QUOTES, 'UTF-8') ?>"></use></svg></span>
    <span class="arena-card-copy">
        <span class="arena-card-title-row">
            <strong><?= htmlspecialchars($item['title'], ENT_QUOTES, 'UTF-8') ?></strong>
            <span class="arena-status arena-status--<?= htmlspecialchars($item['tone'], ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($item['status'], ENT_QUOTES, 'UTF-8') ?></span>
        </span>
        <span class="arena-card-description"><?= htmlspecialchars($item['description'], ENT_QUOTES, 'UTF-8') ?></span>
    </span>
    <svg class="arena-card-arrow" aria-hidden="true"><use href="#arena-icon-arrow"></use></svg>
</a>
