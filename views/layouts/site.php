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
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/themes/prism-tomorrow.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/prism.min.js" defer></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/plugins/line-numbers/prism-line-numbers.min.js" defer></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/plugins/normalize-whitespace/prism-normalize-whitespace.min.js" defer></script>
    <script>
        window.tailwind = window.tailwind || {};
        window.tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'system-ui', '-apple-system', 'sans-serif'],
                        mono: ['JetBrains Mono', 'Menlo', 'monospace'],
                    },
                    animation: {
                        'gradient': 'gradient 8s ease infinite',
                        'float': 'float 6s ease-in-out infinite',
                        'float-slow': 'float 8s ease-in-out infinite',
                        'pulse-glow': 'pulse-glow 3s ease-in-out infinite',
                        'blink': 'blink 1s step-end infinite',
                        'fade-in': 'fade-in 0.6s ease-out both',
                        'fade-up': 'fade-up 0.6s ease-out both',
                        'slide-in': 'slide-in 0.4s ease-out',
                        'scale-in': 'scale-in 0.3s ease-out',
                        'fade-in-down': 'fade-in-down 0.5s ease-out both',
                    },
                    keyframes: {
                        gradient: {
                            '0%, 100%': { backgroundPosition: '0% 50%' },
                            '50%': { backgroundPosition: '100% 50%' },
                        },
                        float: {
                            '0%, 100%': { transform: 'translateY(0)' },
                            '50%': { transform: 'translateY(-10px)' },
                        },
                        'pulse-glow': {
                            '0%, 100%': { opacity: '0.4' },
                            '50%': { opacity: '1' },
                        },
                        blink: {
                            '0%, 100%': { opacity: '1' },
                            '50%': { opacity: '0' },
                        },
                        'fade-in': {
                            from: { opacity: '0' },
                            to: { opacity: '1' },
                        },
                        'fade-up': {
                            from: { opacity: '0', transform: 'translateY(20px)' },
                            to: { opacity: '1', transform: 'translateY(0)' },
                        },
                        'slide-in': {
                            from: { opacity: '0', transform: 'translateX(-12px)' },
                            to: { opacity: '1', transform: 'translateX(0)' },
                        },
                        'scale-in': {
                            from: { opacity: '0', transform: 'scale(0.95)' },
                            to: { opacity: '1', transform: 'scale(1)' },
                        },
                        'fade-in-down': {
                            from: { opacity: '0', transform: 'translateY(-8px)' },
                            to: { opacity: '1', transform: 'translateY(0)' },
                        },
                    },
                },
            },
        };
    </script>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        html { font-feature-settings: 'cv02','cv03','cv04','cv11'; }

        ::selection { background-color: rgba(59,130,246,0.25); color: inherit; }

        .gradient-text {
            background: linear-gradient(135deg, #3b82f6, #10b981, #6366f1);
            background-size: 200% 200%;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            animation: gradient 8s ease infinite;
        }

        .bg-grid {
            background-image:
                linear-gradient(rgba(59,130,246,0.03) 1px, transparent 1px),
                linear-gradient(90deg, rgba(59,130,246,0.03) 1px, transparent 1px);
            background-size: 40px 40px;
        }
        .dark .bg-grid {
            background-image:
                linear-gradient(rgba(99,102,241,0.05) 1px, transparent 1px),
                linear-gradient(90deg, rgba(99,102,241,0.05) 1px, transparent 1px);
        }

        .bg-radial-gradient {
            background: radial-gradient(ellipse 80% 50% at 50% -20%, rgba(59,130,246,0.12), transparent);
        }
        .dark .bg-radial-gradient {
            background: radial-gradient(ellipse 80% 50% at 50% -20%, rgba(99,102,241,0.12), transparent);
        }

        html.theme-transition,
        html.theme-transition * {
            transition: background-color 300ms ease, color 300ms ease, border-color 300ms ease, fill 300ms ease, stroke 300ms ease, box-shadow 300ms ease, transform 300ms ease !important;
        }

        [data-theme-icon-sun], [data-theme-icon-moon] {
            transition: transform 0.3s ease, opacity 0.3s ease;
        }
        [data-theme-icon-sun].hidden, [data-theme-icon-moon].hidden {
            transform: scale(0.5) rotate(-90deg);
            opacity: 0;
        }
        [data-theme-icon-sun]:not(.hidden), [data-theme-icon-moon]:not(.hidden) {
            transform: scale(1) rotate(0deg);
            opacity: 1;
        }

        [data-animate] {
            opacity: 0;
            transform: translateY(20px);
            transition: opacity 0.7s cubic-bezier(0.16, 1, 0.3, 1), transform 0.7s cubic-bezier(0.16, 1, 0.3, 1);
        }
        [data-animate].visible {
            opacity: 1;
            transform: translateY(0);
        }

        [data-animate-stagger] > * {
            opacity: 0;
            transform: translateY(16px);
            transition: opacity 0.5s cubic-bezier(0.16, 1, 0.3, 1), transform 0.5s cubic-bezier(0.16, 1, 0.3, 1);
        }
        [data-animate-stagger].visible > *:nth-child(1) { transition-delay: 0ms; }
        [data-animate-stagger].visible > *:nth-child(2) { transition-delay: 80ms; }
        [data-animate-stagger].visible > *:nth-child(3) { transition-delay: 160ms; }
        [data-animate-stagger].visible > *:nth-child(4) { transition-delay: 240ms; }
        [data-animate-stagger].visible > *:nth-child(5) { transition-delay: 320ms; }
        [data-animate-stagger].visible > *:nth-child(6) { transition-delay: 400ms; }
        [data-animate-stagger].visible > *:nth-child(7) { transition-delay: 480ms; }
        [data-animate-stagger].visible > * {
            opacity: 1;
            transform: translateY(0);
        }

        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after {
                animation-duration: 0.01ms !important;
                animation-iteration-count: 1 !important;
                transition-duration: 0.01ms !important;
            }
            [data-animate] { opacity: 1; transform: none; }
            [data-animate-stagger] > * { opacity: 1; transform: none; }
        }

        .nav-link-underline {
            position: relative;
        }
        .nav-link-underline::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            width: 0;
            height: 2px;
            background: linear-gradient(90deg, #3b82f6, #6366f1);
            transition: width 0.3s ease, left 0.3s ease;
            border-radius: 1px;
        }
        .nav-link-underline:hover::after,
        .nav-link-underline:focus-visible::after {
            width: 80%;
            left: 10%;
        }
        .nav-link-underline.active::after {
            width: 80%;
            left: 10%;
        }

        .focus-ring {
            outline: none;
        }
        .focus-ring:focus-visible {
            outline: 2px solid #3b82f6;
            outline-offset: 2px;
            border-radius: 8px;
        }

        kbd {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0.125rem 0.375rem;
            font-size: 0.75rem;
            font-family: inherit;
            background: rgba(59,130,246,0.1);
            border: 1px solid rgba(59,130,246,0.2);
            border-radius: 4px;
            color: #3b82f6;
        }
        .dark kbd {
            background: rgba(59,130,246,0.15);
            border-color: rgba(59,130,246,0.3);
            color: #60a5fa;
        }

        .scrollbar-stable { scrollbar-gutter: stable; }
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: rgba(59,130,246,0.2); border-radius: 3px; }
        ::-webkit-scrollbar-thumb:hover { background: rgba(59,130,246,0.4); }
        .dark ::-webkit-scrollbar-thumb { background: rgba(99,102,241,0.2); }
        .dark ::-webkit-scrollbar-thumb:hover { background: rgba(99,102,241,0.4); }

        .search-modal-overlay {
            background: rgba(0,0,0,0.3);
            backdrop-filter: blur(4px);
        }

        .btn-ripple {
            position: relative;
            overflow: hidden;
        }
        .btn-ripple .ripple-effect {
            position: absolute;
            border-radius: 50%;
            background: rgba(255,255,255,0.35);
            transform: scale(0);
            animation: ripple-anim 0.6s ease-out;
            pointer-events: none;
        }
        @keyframes ripple-anim {
            to { transform: scale(4); opacity: 0; }
        }

        .animate-scale-in {
            animation: scale-in 0.25s cubic-bezier(0.16, 1, 0.3, 1) both;
        }
        @keyframes scale-in {
            from { opacity: 0; transform: scale(0.92) translateY(-8px); }
            to   { opacity: 1; transform: scale(1) translateY(0); }
        }

        .page-fade-in {
            animation: page-fade-in 0.4s ease-out both;
        }
        @keyframes page-fade-in {
            from { opacity: 0; transform: translateY(4px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .skeleton {
            background: linear-gradient(90deg, rgba(59,130,246,0.06) 25%, rgba(59,130,246,0.12) 50%, rgba(59,130,246,0.06) 75%);
            background-size: 200% 100%;
            animation: skeleton-shimmer 1.5s ease-in-out infinite;
            border-radius: 8px;
        }
        .dark .skeleton {
            background: linear-gradient(90deg, rgba(99,102,241,0.06) 25%, rgba(99,102,241,0.12) 50%, rgba(99,102,241,0.06) 75%);
            background-size: 200% 100%;
        }
        @keyframes skeleton-shimmer {
            0% { background-position: 200% 0; }
            100% { background-position: -200% 0; }
        }

        .header-hidden { transform: translateY(-100%); }
        header { transition: transform 0.3s ease, box-shadow 0.3s ease; }

        .prose pre[class*="language-"] {
            margin: 0;
            border-radius: 0;
            background: transparent;
        }
        .prose code[class*="language-"] {
            font-size: 0.8125rem;
        }

        @media (max-width: 639px) {
            .prose { font-size: 0.9375rem; }
        }
    </style>
    <script>
        (function () {
            const saved = localStorage.getItem('zp-theme');
            const prefersDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
            const isDark = saved ? saved === 'dark' : prefersDark;
            if (isDark) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        })();
    </script>
</head>
<body class="min-h-screen bg-white text-slate-900 dark:bg-slate-950 dark:text-slate-100 antialiased scrollbar-stable">
    <div class="fixed inset-0 -z-10 bg-radial-gradient"></div>
    <div class="fixed inset-0 -z-10 bg-grid"></div>

    <?php render_component('navbar', ['active' => $activePage]); ?>

    <main class="pt-20 pb-20 page-fade-in">
        <?= $content ?>
    </main>

    <?php render_component('footer'); ?>

    <div id="search-modal" class="fixed inset-0 z-[100] hidden items-start justify-center pt-[15vh]" role="dialog" aria-modal="true" aria-label="Search documentation">
        <div class="search-modal-overlay absolute inset-0" data-search-close></div>
        <div class="relative w-full max-w-lg mx-4 animate-scale-in">
            <div class="rounded-2xl border border-slate-200/70 dark:border-slate-800/70 bg-white dark:bg-slate-900 shadow-2xl shadow-black/10 dark:shadow-black/30 overflow-hidden">
                <div class="flex items-center gap-3 px-4 py-3 border-b border-slate-200 dark:border-slate-800">
                    <svg class="h-4 w-4 shrink-0 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="m21 21-6-6m2-5a7 7 0 1 1-14 0 7 7 0 0 1 14 0z"/></svg>
                    <input id="search-modal-input" type="text" placeholder="Search documentation..." class="flex-1 bg-transparent text-sm text-slate-900 dark:text-slate-100 placeholder:text-slate-400 focus:outline-none" autocomplete="off" autocorrect="off" spellcheck="false">
                    <kbd class="text-[10px]">ESC</kbd>
                </div>
                <div id="search-modal-results" class="max-h-80 overflow-y-auto p-2 text-sm text-slate-500 dark:text-slate-400">
                    <p class="p-3 text-center">Type to search documentation...</p>
                </div>
            </div>
        </div>
    </div>

    <script>
        const root = document.documentElement;

        function updateThemeUI(isDark){
            document.querySelectorAll('[data-theme-toggle]').forEach((btn) => {
                btn.setAttribute('aria-label', isDark ? 'Switch to light mode' : 'Switch to dark mode');
                const sun = btn.querySelector('[data-theme-icon-sun]');
                const moon = btn.querySelector('[data-theme-icon-moon]');
                if (sun) sun.classList.toggle('hidden', isDark);
                if (moon) moon.classList.toggle('hidden', !isDark);
            });
        }

        const initialDark = root.classList.contains('dark');
        updateThemeUI(initialDark);

        document.querySelectorAll('[data-theme-toggle]').forEach((btn) => {
            btn.addEventListener('click', () => {
                root.classList.add('theme-transition');
                const dark = root.classList.toggle('dark');
                localStorage.setItem('zp-theme', dark ? 'dark' : 'light');
                updateThemeUI(dark);
                setTimeout(() => root.classList.remove('theme-transition'), 300);
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

        // Copy code (improved)
        document.querySelectorAll('.copy-code-btn').forEach((btn) => {
            btn.addEventListener('click', async () => {
                const target = document.getElementById(btn.getAttribute('data-copy-target'));
                if (!target) return;
                try {
                    await navigator.clipboard.writeText(target.innerText);
                    const oldHTML = btn.innerHTML;
                    btn.innerHTML = '<svg class="h-3.5 w-3.5 inline -mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg> Copied';
                    btn.classList.add('bg-emerald-500/20', 'text-emerald-300', 'border-emerald-500/30');
                    setTimeout(() => {
                        btn.innerHTML = oldHTML;
                        btn.classList.remove('bg-emerald-500/20', 'text-emerald-300', 'border-emerald-500/30');
                    }, 1500);
                } catch (e) {
                    btn.textContent = 'Failed';
                    setTimeout(() => btn.textContent = 'Copy', 1200);
                }
            });
        });

        // Doc search (sidebar)
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

        // Search modal (Ctrl+K)
        const searchModal = document.getElementById('search-modal');
        const searchInput = document.getElementById('search-modal-input');
        const searchResults = document.getElementById('search-modal-results');

        function openSearch() {
            if (!searchModal) return;
            searchModal.classList.remove('hidden');
            searchModal.classList.add('flex');
            setTimeout(() => searchInput?.focus(), 100);
            document.body.style.overflow = 'hidden';
        }

        function closeSearch() {
            if (!searchModal) return;
            searchModal.classList.add('hidden');
            searchModal.classList.remove('flex');
            document.body.style.overflow = '';
        }

        document.addEventListener('keydown', (e) => {
            if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
                e.preventDefault();
                searchModal?.classList.contains('hidden') ? openSearch() : closeSearch();
            }
            if (e.key === 'Escape') closeSearch();
        });

        searchModal?.querySelector('[data-search-close]')?.addEventListener('click', closeSearch);

        if (searchInput) {
            const docItems = document.querySelectorAll('[data-doc-link]');
            searchInput.addEventListener('input', () => {
                const query = searchInput.value.toLowerCase().trim();
                if (!query) {
                    searchResults.innerHTML = '<p class="p-3 text-center text-slate-400">Type to search documentation...</p>';
                    return;
                }
                let results = '';
                let count = 0;
                document.querySelectorAll('[data-doc-link] a').forEach((link) => {
                    const text = link.textContent.toLowerCase();
                    if (text.includes(query)) {
                        const href = link.getAttribute('href');
                        const label = link.textContent;
                        const idx = label.toLowerCase().indexOf(query);
                        let display = '';
                        if (idx >= 0) {
                            display = label.slice(0, idx) + '<mark class="bg-blue-500/20 text-blue-600 dark:text-blue-400 rounded px-0.5">' + label.slice(idx, idx + query.length) + '</mark>' + label.slice(idx + query.length);
                        } else {
                            display = label;
                        }
                        results += '<a href="' + href + '" class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors" data-search-close>' +
                            '<svg class="h-4 w-4 shrink-0 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>' +
                            '<span class="text-sm text-slate-700 dark:text-slate-300">' + display + '</span>' +
                            '</a>';
                        count++;
                    }
                });
                searchResults.innerHTML = count > 0
                    ? results
                    : '<p class="p-3 text-center text-slate-400">No results found for &quot;' + query + '&quot;</p>';
            });
        }

        document.addEventListener('click', (e) => {
            if (e.target.closest('[data-search-close]')) closeSearch();
        });

        // IntersectionObserver for scroll animations
        const animateObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('visible');
                }
            });
        }, { threshold: 0.1, rootMargin: '0px 0px -40px 0px' });

        document.querySelectorAll('[data-animate]').forEach(el => animateObserver.observe(el));

        // Stagger animation observer
        const staggerObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('visible');
                }
            });
        }, { threshold: 0.1, rootMargin: '0px 0px -40px 0px' });

        document.querySelectorAll('[data-animate-stagger]').forEach(el => staggerObserver.observe(el));

        // Navbar hide/show on scroll + glass effect
        const header = document.querySelector('header');
        if (header) {
            let lastScroll = 0;
            let ticking = false;

            window.addEventListener('scroll', () => {
                if (!ticking) {
                    requestAnimationFrame(() => {
                        const currentScroll = window.pageYOffset;

                        header.classList.toggle('shadow-sm', currentScroll > 10);

                        if (currentScroll > 80) {
                            if (currentScroll > lastScroll && currentScroll > 120) {
                                header.classList.add('header-hidden');
                            } else {
                                header.classList.remove('header-hidden');
                            }
                        } else {
                            header.classList.remove('header-hidden');
                        }

                        lastScroll = currentScroll;
                        ticking = false;
                    });
                    ticking = true;
                }
            });
        }

        // Ripple effect on buttons
        document.querySelectorAll('.btn-ripple').forEach(btn => {
            btn.addEventListener('click', function (e) {
                const rect = this.getBoundingClientRect();
                const x = e.clientX - rect.left;
                const y = e.clientY - rect.top;
                const size = Math.max(rect.width, rect.height);
                const ripple = document.createElement('span');
                ripple.className = 'ripple-effect';
                ripple.style.width = ripple.style.height = size + 'px';
                ripple.style.left = x - size / 2 + 'px';
                ripple.style.top = y - size / 2 + 'px';
                this.appendChild(ripple);
                ripple.addEventListener('animationend', () => ripple.remove());
            });
        });

        // Prism re-highlight for dynamic content
        document.addEventListener('DOMContentLoaded', () => {
            if (typeof Prism !== 'undefined') {
                Prism.highlightAllUnder(document.querySelector('main'));
            }
        });
    </script>
</body>
</html>