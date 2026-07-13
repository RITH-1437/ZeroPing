<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($title ?? 'Welcome') ?> — <?= e(config('app.name')) ?></title>
    <meta name="description" content="A ZeroPing application.">
    <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 32 32'><text y='26' font-size='26'>⚡</text></svg>">
    <script>
        (function () {
            var stored = localStorage.getItem('zp-theme');
            if (stored === 'light') {
                document.documentElement.classList.remove('dark');
            }
        })();
    </script>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>tailwind.config = { darkMode: 'class' };</script>
</head>
<body class="min-h-screen bg-slate-50 text-slate-800 dark:bg-slate-950 dark:text-slate-200 antialiased transition-colors">
    {{ slot }}
    <script>
        function toggleTheme() {
            var el = document.documentElement;
            el.classList.toggle('dark');
            localStorage.setItem('zp-theme', el.classList.contains('dark') ? 'dark' : 'light');
        }
    </script>
</body>
</html>
