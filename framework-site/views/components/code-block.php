<?php
$title = $title ?? 'Code';
$code = $code ?? '';
$codeId = $codeId ?? ('code-' . md5($title . $code));
$language = $language ?? '';
$langClass = $language ? 'language-' . strtolower($language) : '';
?>
<section class="code-block mt-5 overflow-hidden rounded-2xl border border-[#1f3b2d] bg-[#08120d]" aria-label="<?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?> code example">
    <div class="flex items-center justify-between gap-3 border-b border-[#214233] bg-[#0e1c15] px-4 py-3">
        <div class="flex min-w-0 items-center gap-2.5">
            <span class="flex shrink-0 gap-1.5" aria-hidden="true"><i class="h-2.5 w-2.5 rounded-full bg-[#ff5f56]"></i><i class="h-2.5 w-2.5 rounded-full bg-[#ffbd2e]"></i><i class="h-2.5 w-2.5 rounded-full bg-[#27c93f]"></i></span>
            <?php if ($language): ?><span class="rounded-full border border-emerald-500/20 bg-emerald-900/60 px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider text-emerald-200"><?= htmlspecialchars($language, ENT_QUOTES, 'UTF-8') ?></span><?php endif; ?>
            <?php if ($title && $title !== 'Code'): ?><span class="truncate rounded-md border border-[#2a5a39] bg-[#1b2a22] px-2 py-0.5 font-mono text-[11px] text-emerald-100"><?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?></span><?php endif; ?>
        </div>
        <button type="button" class="copy-code-btn shrink-0 rounded-lg border border-[#2f5a43] bg-[#163020] px-2.5 py-1 text-xs font-semibold text-slate-100 transition hover:bg-[#20432e]" data-copy-target="<?= htmlspecialchars($codeId, ENT_QUOTES, 'UTF-8') ?>" aria-label="Copy <?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?> code"><span class="copy-label">Copy</span></button>
    </div>
    <?php if (trim($code) === ''): ?>
        <p class="p-6 text-center text-sm italic text-emerald-200">Example coming soon.</p>
    <?php else: ?>
        <pre class="m-0 overflow-x-auto p-5 text-[13px] leading-7 sm:p-6 sm:text-sm"><code id="<?= htmlspecialchars($codeId, ENT_QUOTES, 'UTF-8') ?>" class="<?= htmlspecialchars($langClass, ENT_QUOTES, 'UTF-8') ?> font-mono text-slate-100"><?= htmlspecialchars($code, ENT_QUOTES, 'UTF-8') ?></code></pre>
    <?php endif; ?>
</section>
