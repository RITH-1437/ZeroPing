(() => {
    'use strict';

    const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    const copyText = async (text) => {
        if (navigator.clipboard && window.isSecureContext) {
            await navigator.clipboard.writeText(text);
            return;
        }

        const area = document.createElement('textarea');
        area.value = text;
        area.setAttribute('readonly', '');
        area.style.position = 'fixed';
        area.style.opacity = '0';
        document.body.appendChild(area);
        area.select();
        document.execCommand('copy');
        area.remove();
    };

    document.querySelectorAll('[data-arena-copy]').forEach((button) => {
        button.addEventListener('click', async () => {
            const label = button.querySelector('span');
            const original = label ? label.textContent : '';
            try {
                await copyText(button.getAttribute('data-arena-copy') || '');
                button.classList.add('is-copied');
                if (label) label.textContent = 'Copied';
            } catch (_) {
                if (label) label.textContent = 'Try again';
            }
            window.setTimeout(() => {
                button.classList.remove('is-copied');
                if (label) label.textContent = original;
            }, 1500);
        });
    });

    const formatCounter = (value, format) => {
        if (format === 'version') return '2.0.1';
        if (format === 'plus') return `${Math.round(value)}+`;
        return Math.round(value).toLocaleString();
    };

    const animateCounter = (element) => {
        const target = Number(element.getAttribute('data-counter') || 0);
        const format = element.getAttribute('data-format') || 'number';
        if (format === 'version' || reducedMotion) {
            element.textContent = formatCounter(target, format);
            return;
        }
        const start = performance.now();
        const duration = 900;
        const frame = (time) => {
            const progress = Math.min((time - start) / duration, 1);
            const eased = 1 - Math.pow(1 - progress, 3);
            element.textContent = formatCounter(target * eased, format);
            if (progress < 1) requestAnimationFrame(frame);
        };
        requestAnimationFrame(frame);
    };

    const counters = document.querySelectorAll('[data-counter]');
    const revealItems = document.querySelectorAll('[data-arena-reveal]');

    if ('IntersectionObserver' in window && !reducedMotion) {
        const counterObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach((entry) => {
                if (!entry.isIntersecting) return;
                animateCounter(entry.target);
                observer.unobserve(entry.target);
            });
        }, { threshold: 0.55 });
        counters.forEach((counter) => counterObserver.observe(counter));

        const revealObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach((entry) => {
                if (!entry.isIntersecting) return;
                entry.target.classList.add('is-visible');
                observer.unobserve(entry.target);
            });
        }, { threshold: 0.08, rootMargin: '0px 0px -35px' });
        revealItems.forEach((item) => revealObserver.observe(item));
    } else {
        counters.forEach(animateCounter);
        revealItems.forEach((item) => item.classList.add('is-visible'));
    }

    const tabs = Array.from(document.querySelectorAll('[data-command-tab]'));
    const panels = Array.from(document.querySelectorAll('[data-command-panel]'));

    const activateTab = (tab, focus = false) => {
        const id = tab.getAttribute('data-command-tab');
        tabs.forEach((item) => {
            const selected = item === tab;
            item.setAttribute('aria-selected', selected ? 'true' : 'false');
            item.setAttribute('tabindex', selected ? '0' : '-1');
        });
        panels.forEach((panel) => {
            panel.hidden = panel.getAttribute('data-command-panel') !== id;
        });
        if (focus) tab.focus();
    };

    tabs.forEach((tab, index) => {
        tab.addEventListener('click', () => activateTab(tab));
        tab.addEventListener('keydown', (event) => {
            if (!['ArrowLeft', 'ArrowRight', 'Home', 'End'].includes(event.key)) return;
            event.preventDefault();
            let next = index;
            if (event.key === 'ArrowRight') next = (index + 1) % tabs.length;
            if (event.key === 'ArrowLeft') next = (index - 1 + tabs.length) % tabs.length;
            if (event.key === 'Home') next = 0;
            if (event.key === 'End') next = tabs.length - 1;
            activateTab(tabs[next], true);
        });
    });

    document.querySelectorAll('[data-run-benchmark]').forEach((button) => {
        button.addEventListener('click', () => {
            const preview = button.closest('.arena-tool-preview');
            const output = preview ? preview.querySelector('[data-benchmark-value]') : null;
            if (!preview || !output || button.disabled) return;
            button.disabled = true;
            preview.classList.add('is-running');
            const label = button.querySelector('span');
            if (label) label.textContent = 'Running simulation…';
            window.setTimeout(() => {
                const simulated = (0.72 + Math.random() * 0.18).toFixed(2);
                output.textContent = simulated;
                preview.classList.remove('is-running');
                button.disabled = false;
                if (label) label.textContent = 'Run again';
            }, reducedMotion ? 50 : 850);
        });
    });
})();
