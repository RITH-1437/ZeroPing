<?php
$title = $title ?? 'Build Modern PHP Applications Faster.';
$subtitle = $subtitle ?? 'ZeroPing is a modern PHP framework built for speed, clean architecture, and exceptional developer experience.';
?>
<section class="relative overflow-hidden" data-animate>
  <div class="absolute inset-0 -z-10">
    <div class="absolute top-0 left-1/2 -translate-x-1/2 w-[80rem] h-[40rem] bg-cyan-400/5 blur-3xl"></div>
    <div class="absolute -top-48 -right-48 w-[36rem] h-[36rem] bg-cyan-500/5 rounded-full blur-3xl animate-float-slow"></div>
    <div class="absolute -bottom-48 -left-48 w-[36rem] h-[36rem] bg-emerald-500/5 rounded-full blur-3xl animate-float" style="animation-delay: -4s;"></div>
  </div>
  <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-24 sm:py-32 lg:py-36 w-full">
    <div class="lg:grid lg:grid-cols-2 lg:gap-16 lg:items-center">
      <div class="max-w-3xl">
        <div class="inline-flex items-center gap-2 rounded-full border border-cyan-500/20 bg-cyan-500/5 px-4 py-1.5 text-sm text-zp-link">
          <span class="h-1.5 w-1.5 rounded-full bg-cyan-400 animate-pulse-glow"></span>
          v2.0.1 — Modern PHP Framework
        </div>
        <h1 class="mt-6 font-display text-5xl sm:text-6xl lg:text-7xl xl:text-8xl font-extrabold tracking-tight leading-[1.05] text-zp-ink">
          <?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?>
        </h1>
        <p class="mt-6 text-lg sm:text-xl text-zp-desc leading-relaxed max-w-xl"><?= htmlspecialchars($subtitle, ENT_QUOTES, 'UTF-8') ?></p>
        <div class="mt-10 flex flex-wrap gap-4">
          <a href="/installation" class="group relative inline-flex items-center gap-2 rounded-2xl bg-zp-primary px-6 py-3.5 text-sm font-semibold text-zp-ink shadow-md shadow-zp-primary/20 hover:bg-zp-primary-hover transition-all duration-200 focus-ring">
            Get Started
            <svg class="h-4 w-4 transition-transform group-hover:translate-x-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
          </a>
          <a href="/docs/introduction" class="inline-flex items-center gap-2 rounded-2xl border border-zp-border bg-zp-surface/50 px-6 py-3.5 text-sm font-semibold text-zp-ink hover:bg-zp-surface hover:border-cyan-500/30 transition-all duration-200 focus-ring">
            Documentation
          </a>
          <a href="https://github.com/rith-1437/ZeroPing" target="_blank" rel="noopener" class="inline-flex items-center gap-2 rounded-2xl border border-zp-border px-6 py-3.5 text-sm font-medium text-zp-muted hover:text-zp-ink hover:border-zp-border transition-all duration-200 focus-ring">
            <svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor"><path d="M12 .5C5.7.5.5 5.7.5 12c0 5.1 3.3 9.4 7.9 10.9.6.1.8-.2.8-.5v-2c-3.2.7-3.9-1.4-3.9-1.4-.5-1.3-1.3-1.7-1.3-1.7-1-.7.1-.7.1-.7 1.2.1 1.8 1.2 1.8 1.2 1 1.8 2.7 1.3 3.4 1 .1-.8.4-1.3.7-1.6-2.6-.3-5.3-1.3-5.3-5.8 0-1.3.5-2.3 1.2-3.1-.1-.3-.5-1.5.1-3.1 0 0 1-.3 3.3 1.2a11.5 11.5 0 0 1 6 0C17.3 4.7 18.3 5 18.3 5c.6 1.6.2 2.8.1 3.1.8.8 1.2 1.8 1.2 3.1 0 4.5-2.7 5.5-5.3 5.8.4.4.8 1.1.8 2.2v3.3c0 .3.2.6.8.5A11.5 11.5 0 0 0 23.5 12C23.5 5.7 18.3.5 12 .5z"/></svg>
            GitHub
          </a>
        </div>
      </div>

      <!-- Hero Terminal: Large project-creation window -->
      <div class="hidden lg:block mt-16 lg:mt-0">
        <div class="rounded-2xl overflow-hidden shadow-2xl" style="border:1px solid #1F3B2D;background:#08120D;box-shadow:0 0 40px rgba(34,197,94,0.15),0 0 80px rgba(34,197,94,0.08);">
          <div class="flex items-center justify-between px-4 py-3" style="background:#0E1C15;border-bottom:1px solid #214233;">
            <div class="flex items-center gap-3 min-w-0">
              <div class="flex items-center gap-1.5 shrink-0" aria-hidden="true">
                <span class="h-2.5 w-2.5 rounded-full shadow-sm" style="background:#FF5F56;"></span>
                <span class="h-2.5 w-2.5 rounded-full shadow-sm" style="background:#FFBD2E;"></span>
                <span class="h-2.5 w-2.5 rounded-full shadow-sm" style="background:#27C93F;"></span>
              </div>
              <span class="shrink-0 rounded-full px-2.5 py-0.5 text-[10px] font-semibold uppercase tracking-wider border" style="background:#14532D;color:#BBF7D0;border-color:rgba(34,197,94,0.2);">BASH</span>
              <span class="shrink-0 rounded-md px-2.5 py-0.5 text-[11px] font-medium truncate border" style="background:#1B2A22;color:#D1FAE5;border-color:rgba(42,90,57,0.3);">terminal</span>
            </div>
            <button type="button" class="copy-code-btn shrink-0 text-xs px-3 py-1 rounded-lg" style="background:#163020;color:#F8FAFC;border:1px solid #2F5A43;opacity:1;cursor:pointer;" data-copy-target="hero-terminal">
              <svg style="height:14px;width:14px;display:inline;vertical-align:-2px;margin-right:6px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
              <span class="copy-label">Copy</span>
            </button>
          </div>
          <div class="p-5 font-mono text-sm leading-relaxed" style="line-height:1.9;font-size:14.5px;" id="hero-terminal">
            <div style="color:#F8FAFC;"><span style="color:#22C55E;">$</span> <span style="color:#F8FAFC;">composer create-project rith-1437/zeroping my-app</span></div>
            <div class="typing-line" style="animation-delay:0.4s;color:#6EE7B7;">Creating project...</div>
            <div class="typing-line" style="animation-delay:1.0s;color:#6EE7B7;">Installing dependencies...</div>
            <div class="typing-line" style="animation-delay:1.6s;color:#6EE7B7;">Generating application key...</div>
            <div class="typing-line" style="animation-delay:2.2s;color:#6EE7B7;">Creating .env...</div>
            <div class="typing-line" style="animation-delay:2.8s;color:#6EE7B7;">Running migrations...</div>
            <div class="typing-line" style="animation-delay:3.5s;color:#22C55E;">✔ Project ready</div>
            <div style="color:#F8FAFC;margin-top:8px;"><span style="color:#22C55E;">$</span> <span style="color:#F8FAFC;">cd my-app</span></div>
            <div style="color:#F8FAFC;"><span style="color:#22C55E;">$</span> <span style="color:#F8FAFC;">php zero serve</span></div>
            <div class="typing-line" style="animation-delay:4.3s;color:#4ADE80;">ZeroPing Development Server</div>
            <div class="typing-line" style="animation-delay:4.8s;color:#86EFAC;">➜ http://localhost:1437</div>
            <div class="mt-2 inline-flex items-center gap-1.5">
              <span class="h-3.5 w-1.5 animate-blink" style="background:#22C55E;"></span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<style>
.typing-line {
  opacity: 0;
  animation: typing-fade 0.5s ease-out forwards;
}
@keyframes typing-fade {
  from { opacity: 0; transform: translateY(4px); }
  to { opacity: 1; transform: translateY(0); }
}
</style>
