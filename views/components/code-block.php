<?php
$title = $title ?? 'Code';
$code = $code ?? '';
$codeId = $codeId ?? ('code-' . md5($title . $code));
$language = $language ?? '';
$langClass = $language ? 'language-' . strtolower($language) : '';
?>
<section class="rounded-2xl border border-slate-700/50 overflow-hidden bg-slate-950 shadow-lg shadow-black/10 group">
    <div class="flex items-center justify-between gap-2 px-4 py-2.5 border-b border-slate-800 bg-slate-900/80">
        <div class="flex items-center gap-3 min-w-0">
            <div class="flex items-center gap-1.5" aria-hidden="true">
                <span class="h-2 w-2 rounded-full bg-red-500/80"></span>
                <span class="h-2 w-2 rounded-full bg-yellow-500/80"></span>
                <span class="h-2 w-2 rounded-full bg-emerald-500/80"></span>
            </div>
            <?php if ($language): ?>
                <span class="shrink-0 rounded-md bg-slate-800 px-2 py-0.5 text-[10px] font-medium text-slate-400 uppercase tracking-wider"><?= htmlspecialchars($language, ENT_QUOTES, 'UTF-8') ?></span>
            <?php endif; ?>
            <p class="text-xs text-slate-500 truncate"><?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?></p>
        </div>
        <button type="button" class="copy-code-btn shrink-0 text-xs px-2.5 py-1 rounded-md border border-slate-700/50 bg-white/5 text-slate-400 hover:text-slate-100 hover:bg-white/10 focus-ring transition-all duration-200" data-copy-target="<?= htmlspecialchars($codeId, ENT_QUOTES, 'UTF-8') ?>">
            <svg class="h-3.5 w-3.5 inline -mt-0.5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
            Copy
        </button>
    </div>
    <pre class="p-4 overflow-x-auto text-sm leading-relaxed <?= $langClass ? 'line-numbers' : '' ?>"><code id="<?= htmlspecialchars($codeId, ENT_QUOTES, 'UTF-8') ?>" class="<?= $langClass ?>"><?= htmlspecialchars($code, ENT_QUOTES, 'UTF-8') ?></code></pre>
</section>