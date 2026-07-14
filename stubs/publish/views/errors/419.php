<section class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-20 text-center relative" data-animate>
    <div class="absolute inset-0 -z-10 overflow-hidden">
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[30rem] h-[30rem] bg-amber-500/10 dark:bg-amber-400/5 rounded-full blur-3xl"></div>
    </div>
    <h1 class="text-8xl sm:text-9xl font-extrabold tracking-tight text-transparent bg-clip-text bg-gradient-to-br from-amber-500 via-yellow-500 to-amber-500 dark:from-amber-400 dark:via-yellow-400 dark:to-amber-400">419</h1>
    <h2 class="mt-4 text-2xl sm:text-3xl font-bold text-slate-900 dark:text-slate-100">Page Expired</h2>
    <p class="mt-3 text-slate-600 dark:text-slate-400 max-w-md mx-auto"><?= e($message ?? 'Your session has expired. Please refresh and try again.') ?></p>

    <?php if (!empty($debug)): ?>
        <div class="mt-8 mx-auto max-w-2xl text-left">
            <div class="rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-900/50 px-4 py-3 text-xs text-slate-500 dark:text-slate-400 font-mono">
                <div><span class="text-slate-400">Request:</span> <?= e($requestMethod) ?> <?= e($requestUrl) ?></div>
                <div><span class="text-slate-400">Environment:</span> <?= e($environment) ?></div>
                <?php if ($exception !== ''): ?><div><span class="text-slate-400">Exception:</span> <?= e($exception) ?></div><?php endif; ?>
            </div>
        </div>
    <?php endif; ?>

    <a href="/" class="mt-8 inline-flex items-center justify-center rounded-xl bg-gradient-to-r from-amber-500 via-yellow-500 to-amber-500 bg-[length:200%_200%] px-5 py-2.5 text-sm font-medium text-white shadow-lg shadow-amber-500/20 dark:shadow-amber-500/10 hover:shadow-xl hover:shadow-amber-500/30 dark:hover:shadow-amber-500/20 hover:brightness-110 focus-ring transition-all duration-300">Go Back Home</a>
</section>
