<?php
$title = $title ?? 'Terminal';
$lines = $lines ?? [];
?>
<section class="rounded-2xl border border-slate-700/50 overflow-hidden bg-slate-950 text-slate-100 shadow-lg shadow-black/10" data-terminal>
    <div class="flex items-center justify-between px-4 py-2.5 border-b border-slate-800 bg-zp-surface">
        <div class="flex items-center gap-2" aria-hidden="true">
            <span class="h-2.5 w-2.5 rounded-full bg-red-500/80"></span>
            <span class="h-2.5 w-2.5 rounded-full bg-yellow-500/80"></span>
            <span class="h-2.5 w-2.5 rounded-full bg-emerald-500/80"></span>
        </div>
        <p class="text-xs text-zp-muted"><?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?></p>
        <div class="w-14"></div>
    </div>
    <div class="px-4 py-5 text-sm leading-7 font-mono overflow-x-auto">
        <?php $idx = 0; foreach ($lines as $line): $idx++; ?>
            <div data-terminal-line="<?= $idx ?>" class="text-slate-200 opacity-0"><?= htmlspecialchars($line, ENT_QUOTES, 'UTF-8') ?></div>
        <?php endforeach; ?>
        <span data-terminal-cursor class="inline-block h-4 w-2 bg-slate-200 animate-blink align-middle ml-0.5"></span>
    </div>
</section>
<script>
    (function() {
        const terminal = document.querySelector('[data-terminal]');
        if (!terminal) return;
        const lines = terminal.querySelectorAll('[data-terminal-line]');
        const cursor = terminal.querySelector('[data-terminal-cursor]');
        if (!lines.length) return;

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    observer.unobserve(entry.target);
                    lines.forEach((line, i) => {
                        setTimeout(() => {
                            line.classList.remove('opacity-0');
                            line.classList.add('transition-opacity', 'duration-300');
                        }, (i + 1) * 300);
                    });
                    if (cursor) {
                        setTimeout(() => { cursor.style.display = 'none'; }, (lines.length + 1) * 300 + 500);
                    }
                }
            });
        }, { threshold: 0.3 });
        observer.observe(terminal);
    })();
</script>