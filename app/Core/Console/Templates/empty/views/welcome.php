<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($title) ?> — <?= e(config('app.name')) ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-900 text-white min-h-screen flex items-center justify-center">
    <div class="text-center">
        <h1 class="text-5xl font-bold mb-4"><?= e(config('app.name')) ?></h1>
        <p class="text-slate-400 text-lg">Running on ZeroPing Framework v<?= e($frameworkVersion) ?></p>
    </div>
</body>
</html>
