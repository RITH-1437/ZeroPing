<?php
require_once __DIR__ . '/../components/component.php';
$pageTitle = $title ?? 'ZeroPing Framework';
$activePage = $active ?? '';
?>
<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8') ?></title>
    <meta name="description" content="ZeroPing Framework official website and documentation.">
    <link rel="icon" type="image/png" href="/assets/images/logo.png">
    <script>
        window.tailwind = window.tailwind || {};
        window.tailwind.config = { darkMode: 'class' };
    </script>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .fade-up { animation: fadeUp .6s ease-out both; }
        @keyframes fadeUp { from { opacity: 0; transform: translateY(12px);} to { opacity: 1; transform: translateY(0);} }
        html.theme-transition,
        html.theme-transition * {
            transition: background-color 250ms ease, color 250ms ease, border-color 250ms ease, fill 250ms ease, stroke 250ms ease;
        }
    </style>
    <script>
        (function () {
            const saved = localStorage.getItem('zp-theme');
            const dark = saved ? saved === 'dark' : window.matchMedia('(prefers-color-scheme: dark)').matches;
            if (dark) {
                document.documentElement.classList.add('dark');
                document.body.classList.add('dark');
            }
        })();
    </script>
</head>
<body class="min-h-screen bg-slate-50 text-slate-900 dark:bg-slate-950 dark:text-slate-100 antialiased">
<?php render_component('navbar', ['active' => $activePage]); ?>
<main class="pt-24 pb-20">
    <?= $content ?>
</main>
<?php render_component('footer'); ?>
<script>
    const root = document.documentElement;
    const body = document.body;
    const isDarkMode = root.classList.contains('dark') || body.classList.contains('dark');

    document.querySelectorAll('[data-theme-toggle]').forEach((btn) => {
        btn.setAttribute('aria-label', isDarkMode ? 'Switch to light mode' : 'Switch to dark mode');
    });

    document.querySelectorAll('[data-theme-toggle]').forEach((btn) => {
        btn.addEventListener('click', () => {
            root.classList.add('theme-transition');
            body.classList.add('theme-transition');
            const dark = root.classList.toggle('dark');
            body.classList.toggle('dark', dark);
            localStorage.setItem('zp-theme', dark ? 'dark' : 'light');
            document.querySelectorAll('[data-theme-toggle]').forEach((b) => b.setAttribute('aria-label', dark ? 'Switch to light mode' : 'Switch to dark mode'));
            setTimeout(() => {
                root.classList.remove('theme-transition');
                body.classList.remove('theme-transition');
            }, 250);
        });
    });

    document.querySelectorAll('[data-mobile-toggle]').forEach((btn) => {
        btn.addEventListener('click', () => {
            const menu = document.querySelector('[data-mobile-menu]');
            if (!menu) return;

            const expanded = btn.getAttribute('aria-expanded') === 'true';
            btn.setAttribute('aria-expanded', expanded ? 'false' : 'true');
            btn.setAttribute('aria-label', expanded ? 'Open navigation menu' : 'Close navigation menu');
            menu.classList.toggle('hidden', expanded);

            const openIcon = btn.querySelector('[data-menu-open]');
            const closeIcon = btn.querySelector('[data-menu-close]');
            if (openIcon && closeIcon) {
                openIcon.classList.toggle('hidden', !expanded);
                closeIcon.classList.toggle('hidden', expanded);
            }
        });
    });

    document.querySelectorAll('.copy-code-btn').forEach((btn) => {
        btn.addEventListener('click', async () => {
            const target = document.getElementById(btn.getAttribute('data-copy-target'));
            if (!target) return;
            try {
                await navigator.clipboard.writeText(target.innerText);
                const old = btn.textContent;
                btn.textContent = 'Copied';
                setTimeout(() => btn.textContent = old, 1200);
            } catch (e) {
                btn.textContent = 'Failed';
                setTimeout(() => btn.textContent = 'Copy', 1200);
            }
        });
    });

    const docSearch = document.getElementById('doc-search');
    if (docSearch) {
        docSearch.addEventListener('input', () => {
            const query = docSearch.value.toLowerCase().trim();
            document.querySelectorAll('[data-doc-link]').forEach((item) => {
                const text = item.textContent.toLowerCase();
                item.style.display = text.includes(query) ? '' : 'none';
            });
        });
    }
</script>
</body>
</html>
