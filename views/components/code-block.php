<?php
$title = $title ?? 'Code';
$code = $code ?? '';
$codeId = $codeId ?? ('code-' . md5($title . $code));
?>
<section class="rounded-2xl border border-slate-300 dark:border-slate-800 overflow-hidden bg-slate-950 shadow-lg">
    <div class="flex items-center justify-between px-4 py-3 border-b border-slate-800 bg-slate-900/80">
        <p class="text-xs text-slate-300"><?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?></p>
        <button type="button" class="copy-code-btn text-xs px-2 py-1 rounded bg-white/10 text-slate-100 hover:bg-white/20 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-400" data-copy-target="<?= htmlspecialchars($codeId, ENT_QUOTES, 'UTF-8') ?>">Copy</button>
    </div>
    <pre class="p-4 overflow-x-auto text-sm text-slate-100"><code id="<?= htmlspecialchars($codeId, ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($code, ENT_QUOTES, 'UTF-8') ?></code></pre>
</section>
