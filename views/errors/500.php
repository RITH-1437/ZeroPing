<section class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-20 text-center relative" data-animate>
    <div class="absolute inset-0 -z-10 overflow-hidden">
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[30rem] h-[30rem] bg-red-500/10 dark:bg-red-400/5 rounded-full blur-3xl"></div>
    </div>
    <h1 class="text-8xl sm:text-9xl font-extrabold tracking-tight text-transparent bg-clip-text bg-gradient-to-br from-red-600 via-rose-600 to-red-600 dark:from-red-400 dark:via-rose-400 dark:to-red-400">500</h1>
    <h2 class="mt-4 text-2xl sm:text-3xl font-bold text-slate-900 dark:text-slate-100">Something went wrong</h2>
    <p class="mt-3 text-slate-600 dark:text-slate-400 max-w-md mx-auto"><?= e($message ?? 'An unexpected error occurred while handling your request.') ?></p>

    <?php if (!empty($debug) && !empty($trace)): ?>
        <div class="mt-8 mx-auto max-w-2xl text-left">
            <details class="rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-900/50">
                <summary class="cursor-pointer px-4 py-3 text-sm font-medium text-slate-700 dark:text-slate-300">Show debug trace</summary>
                <pre class="overflow-x-auto px-4 pb-4 text-xs leading-relaxed text-slate-600 dark:text-slate-400"><code><?= e(print_r($trace, true)) ?></code></pre>
            </details>
        </div>
    <?php endif; ?>

    <a href="/" class="mt-8 inline-flex items-center justify-center rounded-xl bg-gradient-to-r from-red-600 via-rose-600 to-red-600 bg-[length:200%_200%] px-5 py-2.5 text-sm font-medium text-white shadow-lg shadow-red-600/20 dark:shadow-red-500/10 hover:shadow-xl hover:shadow-red-600/30 dark:hover:shadow-red-500/20 hover:brightness-110 focus-ring transition-all duration-300">Go Back Home</a>
</section>
