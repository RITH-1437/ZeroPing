<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($title ?? 'Dashboard') ?> — <?= e(config('app.name')) ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50 dark:bg-slate-900 text-slate-800 dark:text-slate-200 min-h-screen">
    <div class="flex min-h-screen">
        <aside class="w-64 bg-white dark:bg-slate-800 border-r border-slate-200 dark:border-slate-700 p-6 hidden md:block">
            <a href="/" class="text-xl font-bold block mb-8"><?= e(config('app.name')) ?></a>
            <nav class="space-y-2">
                <a href="/dashboard" class="block px-3 py-2 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700">Dashboard</a>
                <a href="/users" class="block px-3 py-2 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700">Users</a>
            </nav>
        </aside>
        <main class="flex-1 px-8 py-10">
            {{ slot }}
        </main>
    </div>
</body>
</html>
