<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($title ?? 'App') ?> — <?= e(config('app.name')) ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50 dark:bg-slate-900 text-slate-800 dark:text-slate-200 min-h-screen">
    <header class="bg-white dark:bg-slate-800 border-b border-slate-200 dark:border-slate-700">
        <div class="max-w-4xl mx-auto px-4 py-4 flex items-center justify-between">
            <a href="/" class="text-xl font-bold"><?= e(config('app.name')) ?></a>
            <nav class="flex gap-4">
                <a href="/" class="hover:text-blue-600">Home</a>
                <a href="/users" class="hover:text-blue-600">Users</a>
            </nav>
        </div>
    </header>
    <main class="max-w-4xl mx-auto px-4 py-8">
        {{ slot }}
    </main>
</body>
</html>
