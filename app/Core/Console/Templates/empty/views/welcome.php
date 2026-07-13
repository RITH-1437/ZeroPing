<?php
$version = class_exists(\App\Core\Application\App::class) ? \App\Core\Application\App::VERSION : '1.x';
$env = config('app.env', 'local');
?>
<div class="relative min-h-screen flex flex-col">
    <!-- Top bar -->
    <header class="w-full">
        <div class="max-w-5xl mx-auto px-6 py-5 flex items-center justify-between">
            <div class="flex items-center gap-2 font-semibold tracking-tight">
                <span class="text-blue-500 text-xl">⚡</span>
                <span><?= e(config('app.name')) ?></span>
            </div>
            <div class="flex items-center gap-4 text-sm">
                <a href="https://github.com/RITH-1437/ZeroPing" class="text-slate-500 hover:text-blue-500 transition-colors">GitHub</a>
                <a href="https://github.com/RITH-1437/ZeroPing/tree/main/docs" class="text-slate-500 hover:text-blue-500 transition-colors">Docs</a>
                <button onclick="toggleTheme()" aria-label="Toggle dark mode"
                    class="w-9 h-9 grid place-items-center rounded-lg border border-slate-200 dark:border-slate-800 hover:border-blue-500 transition-colors">
                    <span class="dark:hidden">🌙</span>
                    <span class="hidden dark:inline">☀️</span>
                </button>
            </div>
        </div>
    </header>

    <!-- Hero -->
    <main class="flex-1">
        <div class="max-w-5xl mx-auto px-6 pt-10 pb-16">
            <div class="text-center">
                <pre class="hidden sm:block text-[9px] md:text-[11px] leading-[1.15] text-blue-500/70 dark:text-blue-400/60 select-none font-mono mb-8 mx-auto w-fit">
 ███████╗███████╗██████╗  ██████╗ ██████╗ ██╗███╗   ██╗ ██████╗
 ╚══███╔╝██╔════╝██╔══██╗██╔═══██╗██╔══██╗██║████╗  ██║██╔════╝
   ███╔╝ █████╗  ██████╔╝██║   ██║██████╔╝██║██╔██╗ ██║██║  ███╗
  ███╔╝  ██╔══╝  ██╔══██╗██║   ██║██╔═══╝ ██║██║╚██╗██║██║   ██║
 ███████╗███████╗██║  ██║╚██████╔╝██║     ██║██║ ╚████║╚██████╔╝
 ╚══════╝╚══════╝╚═╝  ╚═╝ ╚═════╝ ╚═╝     ╚═╝╚═╝  ╚═══╝ ╚═════╝
                </pre>

                <h1 class="text-4xl sm:text-5xl font-bold tracking-tight mb-4">
                    <?= e(config('app.name')) ?>
                </h1>
                <p class="text-slate-500 dark:text-slate-400 text-base sm:text-lg max-w-xl mx-auto mb-8">
                    Your application is ready. Start building something great with the ZeroPing PHP framework.
                </p>

                <!-- Status pills -->
                <div class="flex flex-wrap items-center justify-center gap-2 sm:gap-3 mb-12">
                    <span class="px-3 py-1 rounded-full text-xs font-medium bg-blue-500/10 text-blue-500 border border-blue-500/20">
                        ZeroPing v<?= e($version) ?>
                    </span>
                    <span class="px-3 py-1 rounded-full text-xs font-medium bg-slate-500/10 text-slate-500 border border-slate-500/20">
                        PHP <?= e(PHP_VERSION) ?>
                    </span>
                    <span class="px-3 py-1 rounded-full text-xs font-medium bg-emerald-500/10 text-emerald-500 border border-emerald-500/20">
                        <?= e(ucfirst((string) $env)) ?>
                    </span>
                </div>
            </div>

            <!-- Cards -->
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <a href="https://github.com/RITH-1437/ZeroPing/tree/main/docs"
                   class="group p-5 rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 hover:border-blue-500 hover:shadow-lg transition-all">
                    <div class="text-2xl mb-3">📚</div>
                    <h3 class="font-semibold mb-1 group-hover:text-blue-500 transition-colors">Documentation</h3>
                    <p class="text-sm text-slate-500 dark:text-slate-400">Guides, references and tutorials.</p>
                </a>
                <a href="https://github.com/RITH-1437/ZeroPing"
                   class="group p-5 rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 hover:border-blue-500 hover:shadow-lg transition-all">
                    <div class="text-2xl mb-3">🐙</div>
                    <h3 class="font-semibold mb-1 group-hover:text-blue-500 transition-colors">GitHub</h3>
                    <p class="text-sm text-slate-500 dark:text-slate-400">Source code, issues and releases.</p>
                </a>
                <div class="p-5 rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900">
                    <div class="text-2xl mb-3">⌨️</div>
                    <h3 class="font-semibold mb-1">CLI Commands</h3>
                    <p class="text-sm text-slate-500 dark:text-slate-400">Run <code class="text-blue-500">php zero help</code> to explore.</p>
                </div>
                <div class="p-5 rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900">
                    <div class="text-2xl mb-3">🚀</div>
                    <h3 class="font-semibold mb-1">Quick Start</h3>
                    <p class="text-sm text-slate-500 dark:text-slate-400">Edit <code class="text-blue-500">app/Controllers</code> to begin.</p>
                </div>
            </div>

            <!-- Quick start terminal -->
            <div class="mt-8 rounded-xl overflow-hidden border border-slate-200 dark:border-slate-800 bg-slate-900 dark:bg-black/40">
                <div class="flex items-center gap-1.5 px-4 py-3 border-b border-slate-800">
                    <span class="w-3 h-3 rounded-full bg-red-500/80"></span>
                    <span class="w-3 h-3 rounded-full bg-yellow-500/80"></span>
                    <span class="w-3 h-3 rounded-full bg-green-500/80"></span>
                    <span class="ml-3 text-xs text-slate-500">terminal</span>
                </div>
                <pre class="p-4 text-sm text-slate-300 font-mono overflow-x-auto"><span class="text-green-400">$</span> php zero serve       <span class="text-slate-600"># start the dev server</span>
<span class="text-green-400">$</span> php zero install     <span class="text-slate-600"># interactive setup wizard</span>
<span class="text-green-400">$</span> php zero doctor      <span class="text-slate-600"># verify your environment</span>
<span class="text-green-400">$</span> php zero make:controller PostController</pre>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="border-t border-slate-200 dark:border-slate-800">
        <div class="max-w-5xl mx-auto px-6 py-6 text-center text-sm text-slate-500">
            Built with <span class="text-blue-500 font-medium">ZeroPing</span>
            · PHP <?= e(PHP_VERSION) ?>
            · <?= e(ucfirst((string) $env)) ?>
        </div>
    </footer>
</div>
