<?php
$title = $title ?? 'The calm, capable PHP framework.';
$subtitle = $subtitle ?? 'ZeroPing gives you an expressive foundation for web applications—routing, ORM, queues, validation, and a CLI that keeps the feedback loop fast.';
?>
<section class="relative overflow-hidden" data-animate>
    <div class="mx-auto grid max-w-7xl items-center gap-12 px-4 py-16 sm:px-6 sm:py-24 lg:grid-cols-[1.02fr_.98fr] lg:gap-16 lg:px-8 lg:py-28">
        <div class="max-w-3xl">
            <div class="inline-flex items-center gap-2 rounded-full border border-teal-500/20 bg-teal-500/10 px-3 py-1.5 text-xs font-semibold text-zp-link">
                <span class="h-1.5 w-1.5 rounded-full bg-emerald-400 shadow-[0_0_0_4px_rgba(52,211,153,.15)]" aria-hidden="true"></span>
                ZeroPing <?= htmlspecialchars('v' . \App\Core\Application\App::VERSION, ENT_QUOTES, 'UTF-8') ?> is ready for your next project
            </div>
            <h1 class="mt-6 font-display text-5xl font-extrabold leading-[1.02] tracking-[-.055em] text-zp-ink sm:text-6xl lg:text-7xl"><?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?></h1>
            <p class="mt-6 max-w-xl text-lg leading-8 text-zp-desc sm:text-xl"><?= htmlspecialchars($subtitle, ENT_QUOTES, 'UTF-8') ?></p>
            <div class="mt-8 flex flex-wrap gap-3">
                <a href="/installation" class="btn-ripple inline-flex items-center gap-2 rounded-xl bg-zp-primary px-5 py-3 text-sm font-bold text-zp-ink shadow-lg shadow-teal-500/20 transition hover:-translate-y-0.5 hover:bg-zp-primary-hover focus-ring">Start building <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="m13 7 5 5m0 0-5 5m5-5H6"/></svg></a>
                <a href="/docs/introduction" class="inline-flex items-center gap-2 rounded-xl border border-zp-border bg-zp-surface/70 px-5 py-3 text-sm font-bold text-zp-ink transition hover:border-teal-500/30 hover:bg-teal-500/5 focus-ring">Read the docs</a>
            </div>
            <dl class="mt-10 grid max-w-xl grid-cols-3 gap-4 border-t border-zp-border pt-5">
                <div><dt class="text-xs text-zp-muted">PHP</dt><dd class="mt-1 text-sm font-bold text-zp-ink">8.1+</dd></div>
                <div><dt class="text-xs text-zp-muted">License</dt><dd class="mt-1 text-sm font-bold text-zp-ink">MIT</dd></div>
                <div><dt class="text-xs text-zp-muted">Setup</dt><dd class="mt-1 text-sm font-bold text-zp-ink">Under 3 min</dd></div>
            </dl>
        </div>

        <div class="relative">
            <div class="absolute -inset-5 -z-10 rounded-[2rem] bg-gradient-to-br from-teal-400/20 via-cyan-400/5 to-transparent blur-2xl" aria-hidden="true"></div>
            <div class="overflow-hidden rounded-2xl border border-[#214233] bg-[#08120d] shadow-2xl shadow-teal-950/25">
                <div class="flex items-center justify-between border-b border-[#214233] bg-[#0e1c15] px-4 py-3">
                    <div class="flex items-center gap-2.5"><span class="flex gap-1.5" aria-hidden="true"><i class="h-2.5 w-2.5 rounded-full bg-[#ff5f56]"></i><i class="h-2.5 w-2.5 rounded-full bg-[#ffbd2e]"></i><i class="h-2.5 w-2.5 rounded-full bg-[#27c93f]"></i></span><span class="rounded-md border border-emerald-500/20 bg-emerald-900/60 px-2 py-0.5 font-mono text-[10px] font-bold uppercase tracking-wider text-emerald-200">Terminal</span></div>
                    <button type="button" class="copy-code-btn rounded-lg border border-[#2f5a43] bg-[#163020] px-2.5 py-1 text-xs font-semibold text-slate-100 transition hover:bg-[#20432e]" data-copy-target="hero-terminal" aria-label="Copy installation commands"><span class="copy-label">Copy</span></button>
                </div>
                <pre class="m-0 overflow-x-auto p-5 text-[13px] leading-7 sm:p-6 sm:text-sm"><code id="hero-terminal" class="font-mono"><span class="text-emerald-400">$</span> <span class="text-slate-100">php zero new my-app</span>
<span class="text-emerald-200">  Creating a fresh ZeroPing application…</span>
<span class="text-emerald-200">  Configuring SQLite and application key…</span>
<span class="text-emerald-400">  ✓ Project ready.</span>

<span class="text-emerald-400">$</span> <span class="text-slate-100">cd my-app && php zero serve</span>
<span class="text-emerald-200">  ZeroPing development server started</span>
<span class="text-cyan-200">  → http://localhost:1437</span></code></pre>
                <div class="border-t border-[#214233] bg-[#0e1c15] px-5 py-3 text-xs text-emerald-100/75"><span class="font-semibold text-emerald-300">Next:</span> open the generated route and make it yours.</div>
            </div>
        </div>
    </div>
</section>
