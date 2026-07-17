<?php
$title = $title ?? 'Code';
$code = $code ?? '';
$codeId = $codeId ?? ('code-' . md5($title . $code));
$language = $language ?? '';
$width = $width ?? '';
$widthStyle = $width === 'half' ? 'width:50%;max-width:50%;' : '';
$langClass = $language ? 'language-' . strtolower($language) : '';
$langDisplay = $language ? strtoupper(htmlspecialchars($language, ENT_QUOTES, 'UTF-8')) : '';
$langBadge = $langDisplay
    ? '<span class="shrink-0 rounded-full px-2.5 py-0.5 text-[10px] font-semibold uppercase tracking-wider border" style="background:#14532D;color:#BBF7D0;border-color:rgba(34,197,94,0.2);">' . $langDisplay . '</span>'
    : '';
$titleBadge = ($title && $title !== 'Code')
    ? '<span class="shrink-0 rounded-md px-2.5 py-0.5 text-[11px] font-medium truncate border" style="background:#1B2A22;color:#D1FAE5;border-color:rgba(42,90,57,0.3);">' . htmlspecialchars($title, ENT_QUOTES, 'UTF-8') . '</span>'
    : '';
?>
<section class="code-block mt-6 rounded-2xl overflow-hidden shadow-xl" style="opacity:1;filter:none;backdrop-filter:none;background:#08120D;border:1px solid #1F3B2D;<?= $widthStyle ?>">
    <div class="flex items-center justify-between gap-2 px-4 py-3 rounded-t-2xl" style="background:#0E1C15;border-bottom:1px solid #214233;opacity:1;">
        <div class="flex items-center gap-3 min-w-0" style="opacity:1;">
            <div class="flex items-center gap-1.5 shrink-0" aria-hidden="true" style="opacity:1;">
                <span class="h-2.5 w-2.5 rounded-full shadow-sm" style="background:#FF5F56;opacity:1;"></span>
                <span class="h-2.5 w-2.5 rounded-full shadow-sm" style="background:#FFBD2E;opacity:1;"></span>
                <span class="h-2.5 w-2.5 rounded-full shadow-sm" style="background:#27C93F;opacity:1;"></span>
            </div>
            <?= $langBadge ?>
            <?= $titleBadge ?>
        </div>
        <button type="button" class="copy-code-btn shrink-0 text-xs px-3 py-1 rounded-lg" style="background:#163020;color:#F8FAFC;border:1px solid #2F5A43;opacity:1;cursor:pointer;" data-copy-target="<?= htmlspecialchars($codeId, ENT_QUOTES, 'UTF-8') ?>">
            <svg style="height:14px;width:14px;display:inline;vertical-align:-2px;margin-right:6px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
            <span class="copy-label">Copy</span>
        </button>
    </div>
    <?php if (trim($code) === ''): ?>
        <div style="padding:24px;text-align:center;color:#6EE7B7;font-style:italic;opacity:1;">Example coming soon...</div>
    <?php else: ?>
        <pre class="overflow-x-auto" style="margin:0;border:0;border-radius:0;padding:24px;background:transparent;opacity:1;line-height:1.8;font-family:'JetBrains Mono','Fira Code','Consolas',monospace;font-size:15px;"><code id="<?= htmlspecialchars($codeId, ENT_QUOTES, 'UTF-8') ?>" class="<?= $langClass ?>" style="opacity:1;font-weight:500;color:#F8FAFC;"><?= htmlspecialchars($code, ENT_QUOTES, 'UTF-8') ?></code></pre>
    <?php endif; ?>
</section>

<script>
(function() {
    document.querySelectorAll('.copy-code-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var target = document.getElementById(this.getAttribute('data-copy-target'));
            if (!target) return;
            var self = this;
            var text = target.innerText;
            navigator.clipboard.writeText(text).then(function() {
                var label = self.querySelector('.copy-label');
                if (label) {
                    var orig = label.textContent;
                    label.textContent = '✓ Copied';
                    self.style.background = '#22C55E';
                    self.style.borderColor = '#22C55E';
                    self.style.color = '#08120D';
                    setTimeout(function() {
                        label.textContent = orig;
                        self.style.background = '#163020';
                        self.style.borderColor = '#2F5A43';
                        self.style.color = '#F8FAFC';
                    }, 2000);
                }
            }).catch(function() {});
        });
    });
})();
</script>
