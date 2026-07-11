<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ project_name }} — ZeroPing</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-900 text-white min-h-screen flex flex-col items-center justify-center px-6">
    <div class="text-center">
        <pre class="text-[8px] sm:text-[10px] leading-[1.1] text-blue-400/60 select-none font-mono tracking-tight mb-8">
███████╗███████╗██████╗  ██████╗ ██████╗ ██╗███╗   ██╗ ██████╗
╚══███╔╝██╔════╝██╔══██╗██╔═══██╗██╔══██╗██║████╗  ██║██╔════╝
  ███╔╝ █████╗  ██████╔╝██║   ██║██████╔╝██║██╔██╗ ██║██║  ███╗
 ███╔╝  ██╔══╝  ██╔══██╗██║   ██║██╔═══╝ ██║██║╚██╗██║██║   ██║
███████╗███████╗██║  ██║╚██████╔╝██║     ██║██║ ╚████║╚██████╔╝
╚══════╝╚══════╝╚═╝  ╚═╝ ╚═════╝ ╚═╝     ╚═╝╚═╝  ╚═══╝ ╚═════╝
        </pre>
        <h1 class="text-4xl sm:text-5xl font-bold mb-3">{{ project_name }}</h1>
        <p class="text-slate-400 text-base sm:text-lg mb-8">{{ project_description }}</p>
        <div class="flex items-center justify-center gap-2 text-sm text-slate-500">
            <span>Built with</span>
            <span class="px-2 py-0.5 rounded-md bg-slate-800 text-blue-400 font-medium text-xs">ZeroPing</span>
        </div>
        <div class="mt-10 flex items-center justify-center gap-2 text-xs text-slate-600">
            <span>PHP {{ php_version }}</span>
            <span class="text-slate-700">|</span>
            <span>v1.0.0</span>
        </div>
    </div>
</body>
</html>
