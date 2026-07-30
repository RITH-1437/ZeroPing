<?php
/** @var array<string, string> $package */
$install = 'composer require zeroping/' . $package['slug'];
?>
<article class="arena-package-card">
    <div class="arena-package-heading">
        <span class="arena-icon arena-icon--small" aria-hidden="true"><svg><use href="#arena-icon-<?= htmlspecialchars($package['icon'], ENT_QUOTES, 'UTF-8') ?>"></use></svg></span>
        <div>
            <h3><?= htmlspecialchars($package['name'], ENT_QUOTES, 'UTF-8') ?></h3>
            <p>zeroping/<?= htmlspecialchars($package['slug'], ENT_QUOTES, 'UTF-8') ?></p>
        </div>
        <span class="arena-status arena-status--<?= htmlspecialchars($package['tone'], ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($package['status'], ENT_QUOTES, 'UTF-8') ?></span>
    </div>
    <p class="arena-package-description"><?= htmlspecialchars($package['description'], ENT_QUOTES, 'UTF-8') ?></p>
    <div class="arena-package-meta"><span><?= htmlspecialchars($package['version'], ENT_QUOTES, 'UTF-8') ?></span><span>MIT</span><span>PHP 8.1+</span></div>
    <div class="arena-install-command">
        <code><?= htmlspecialchars($install, ENT_QUOTES, 'UTF-8') ?></code>
        <button type="button" data-arena-copy="<?= htmlspecialchars($install, ENT_QUOTES, 'UTF-8') ?>" aria-label="Copy install command for <?= htmlspecialchars($package['name'], ENT_QUOTES, 'UTF-8') ?>">
            <svg aria-hidden="true"><use href="#arena-icon-copy"></use></svg><span>Copy</span>
        </button>
    </div>
    <div class="arena-package-links">
        <a href="<?= htmlspecialchars($package['docs'], ENT_QUOTES, 'UTF-8') ?>">Documentation <span aria-hidden="true">→</span></a>
        <a href="<?= htmlspecialchars($package['github'], ENT_QUOTES, 'UTF-8') ?>" target="_blank" rel="noopener noreferrer">GitHub <span class="sr-only">(opens in a new tab)</span><span aria-hidden="true">↗</span></a>
    </div>
</article>
