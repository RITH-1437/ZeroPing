<?php
$title = $title ?? 'Build Modern PHP Applications Faster.';
$subtitle = $subtitle ?? 'ZeroPing is a modern PHP framework built for speed, clean architecture, and exceptional developer experience.';
?>
<section class="relative overflow-hidden min-h-[90vh] flex items-center" data-animate>
  <div class="absolute inset-0 -z-10">
    <div class="absolute top-0 left-1/2 -translate-x-1/2 w-[80rem] h-[40rem] bg-gradient-to-b from-cyan-500/8 via-emerald-500/5 to-transparent blur-3xl"></div>
    <div class="absolute -top-48 -right-48 w-[36rem] h-[36rem] bg-cyan-500/5 rounded-full blur-3xl animate-float-slow"></div>
    <div class="absolute -bottom-48 -left-48 w-[36rem] h-[36rem] bg-emerald-500/5 rounded-full blur-3xl animate-float" style="animation-delay: -4s;"></div>
  </div>
  <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-24 sm:py-32 lg:py-36 w-full">
    <div class="lg:grid lg:grid-cols-2 lg:gap-16 lg:items-center">
      <div class="max-w-3xl">
        <div class="inline-flex items-center gap-2 rounded-full border border-cyan-500/20 bg-cyan-500/5 px-4 py-1.5 text-sm text-cyan-400">
          <span class="h-1.5 w-1.5 rounded-full bg-cyan-400 animate-pulse-glow"></span>
          v1.2.0 — Modern PHP Framework
        </div>
        <h1 class="mt-6 font-display text-5xl sm:text-6xl lg:text-7xl xl:text-8xl font-extrabold tracking-tight leading-[1.05] text-zp-white">
          <?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?>
        </h1>
        <p class="mt-6 text-lg sm:text-xl text-zp-muted leading-relaxed max-w-xl"><?= htmlspecialchars($subtitle, ENT_QUOTES, 'UTF-8') ?></p>
        <div class="mt-10 flex flex-wrap gap-4">
          <a href="/installation" class="group relative inline-flex items-center gap-2 rounded-2xl bg-gradient-to-r from-cyan-500 to-emerald-500 px-6 py-3.5 text-sm font-semibold text-zp-bg shadow-lg shadow-cyan-500/20 hover:shadow-xl hover:shadow-cyan-500/30 hover:scale-[1.02] active:scale-[0.98] transition-all duration-200 focus-ring">
            Get Started
            <svg class="h-4 w-4 transition-transform group-hover:translate-x-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
          </a>
          <a href="/docs/introduction" class="inline-flex items-center gap-2 rounded-2xl border border-zp-border bg-zp-surface/50 px-6 py-3.5 text-sm font-semibold text-zp-white hover:bg-zp-surface hover:border-cyan-500/30 transition-all duration-200 focus-ring">
            Documentation
          </a>
          <a href="https://github.com/rith-1437/zero-ping" target="_blank" rel="noopener" class="inline-flex items-center gap-2 rounded-2xl border border-zp-border px-6 py-3.5 text-sm font-medium text-zp-muted hover:text-zp-white hover:border-zp-border transition-all duration-200 focus-ring">
            <svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor"><path d="M12 .5C5.7.5.5 5.7.5 12c0 5.1 3.3 9.4 7.9 10.9.6.1.8-.2.8-.5v-2c-3.2.7-3.9-1.4-3.9-1.4-.5-1.3-1.3-1.7-1.3-1.7-1-.7.1-.7.1-.7 1.2.1 1.8 1.2 1.8 1.2 1 1.8 2.7 1.3 3.4 1 .1-.8.4-1.3.7-1.6-2.6-.3-5.3-1.3-5.3-5.8 0-1.3.5-2.3 1.2-3.1-.1-.3-.5-1.5.1-3.1 0 0 1-.3 3.3 1.2a11.5 11.5 0 0 1 6 0C17.3 4.7 18.3 5 18.3 5c.6 1.6.2 2.8.1 3.1.8.8 1.2 1.8 1.2 3.1 0 4.5-2.7 5.5-5.3 5.8.4.4.8 1.1.8 2.2v3.3c0 .3.2.6.8.5A11.5 11.5 0 0 0 23.5 12C23.5 5.7 18.3.5 12 .5z"/></svg>
            GitHub
          </a>
        </div>
      </div>

      <div class="hidden lg:block mt-16 lg:mt-0 space-y-6">
        <!-- Mascot -->
        <div class="flex justify-center">
          <img src="/assets/images/mascot.svg" alt="ZeroPing Mascot" class="w-32 h-32 opacity-80" />
        </div>

        <!-- Animated Terminal -->
        <div class="rounded-2xl border border-zp-border overflow-hidden bg-zp-surface/80 backdrop-blur-sm shadow-2xl shadow-cyan-500/5">
          <div class="flex items-center gap-2 px-4 py-3 border-b border-zp-border bg-zp-surface/50">
            <span class="h-2.5 w-2.5 rounded-full bg-red-500/70"></span>
            <span class="h-2.5 w-2.5 rounded-full bg-yellow-500/70"></span>
            <span class="h-2.5 w-2.5 rounded-full bg-emerald-500/70"></span>
            <span class="ml-2 text-xs text-zp-muted font-mono">terminal — zsh</span>
          </div>
          <div class="p-5 font-mono text-sm leading-relaxed" id="terminal-lines">
            <div class="text-cyan-400">$ <span class="text-zp-white">composer create-project rith-1437/zero-ping blog</span></div>
            <div class="text-zp-muted mt-1 typing-line" style="animation-delay: 0.5s;">Installing dependencies...</div>
            <div class="text-emerald-400 mt-1 typing-line" style="animation-delay: 2s;">✔  Routing system configured</div>
            <div class="text-emerald-400 mt-1 typing-line" style="animation-delay: 3s;">✔  ORM ready</div>
            <div class="text-emerald-400 mt-1 typing-line" style="animation-delay: 4s;">✔  CLI initialized</div>
            <div class="text-emerald-400 mt-1 typing-line" style="animation-delay: 5s;">✔  Project ready</div>
            <div class="text-zp-white mt-3 typing-line" style="animation-delay: 6s;">
              <span class="text-cyan-400">➜</span>  Blog app created successfully
            </div>
            <div class="text-zp-muted mt-1 typing-line" style="animation-delay: 6.5s;">
              <span class="text-cyan-400">➜</span>  Run <span class="text-emerald-400">php zero serve</span> to start
            </div>
            <div class="mt-3 inline-flex items-center gap-1.5 text-zp-muted">
              <span class="h-3 w-1.5 bg-cyan-400 animate-blink"></span>
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
