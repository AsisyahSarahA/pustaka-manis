import './bootstrap';

import Alpine from 'alpinejs';
import focus from '@alpinejs/focus';
import { Html5Qrcode } from 'html5-qrcode';
import Chart from 'chart.js/auto';

window.Alpine = Alpine;
window.Html5Qrcode = Html5Qrcode;
window.Chart = Chart;

Alpine.plugin(focus);

// ==== Manajemen Tema (dark / light) ====
function applyTheme(theme) {
    const root = document.documentElement;
    if (theme === 'light') {
        root.classList.add('light');
    } else {
        root.classList.remove('light');
    }
}

function themeFromStorage() {
    try {
        return localStorage.getItem('pustakamanis-theme') || 'dark';
    } catch {
        return 'dark';
    }
}

function persistTheme(theme) {
    try {
        localStorage.setItem('pustakamanis-theme', theme);
    } catch {}
}

applyTheme(themeFromStorage());

Alpine.data('themeToggle', () => ({
    theme: themeFromStorage(),

    init() {
        this.$watch('theme', (value) => {
            applyTheme(value);
            persistTheme(value);
        });
    },

    toggle() {
        this.theme = this.theme === 'light' ? 'dark' : 'light';
    },
}));

// ==== SPA Navigation Engine (PJAX / Seamless Page Swapping) ====
class SpaRouter {
    constructor() {
        this.progressBar = null;
        this.isNavigating = false;
        this.init();
    }

    init() {
        document.addEventListener('DOMContentLoaded', () => {
            this.progressBar = document.getElementById('top-progress-bar');
            this.bindLinkClicks();
            window.addEventListener('popstate', () => this.loadUrl(window.location.href, false));
        });
    }

    bindLinkClicks() {
        document.body.addEventListener('click', (e) => {
            const anchor = e.target.closest('a');
            if (!anchor) return;

            const href = anchor.getAttribute('href');
            const target = anchor.getAttribute('target');
            const download = anchor.hasAttribute('download');

            if (
                !href ||
                href.startsWith('#') ||
                href.startsWith('javascript:') ||
                href.startsWith('mailto:') ||
                href.startsWith('tel:') ||
                target === '_blank' ||
                download ||
                anchor.hasAttribute('data-no-spa')
            ) {
                return;
            }

            try {
                const url = new URL(href, window.location.origin);
                if (url.origin !== window.location.origin) return;

                e.preventDefault();
                if (url.href !== window.location.href) {
                    this.loadUrl(url.href, true);
                }
            } catch (err) {}
        });
    }

    showProgressBar() {
        if (!this.progressBar) this.progressBar = document.getElementById('top-progress-bar');
        if (!this.progressBar) return;
        this.progressBar.style.opacity = '1';
        this.progressBar.style.width = '30%';
    }

    updateProgressBar(percent) {
        if (!this.progressBar) return;
        this.progressBar.style.width = percent + '%';
    }

    hideProgressBar() {
        if (!this.progressBar) return;
        this.progressBar.style.width = '100%';
        setTimeout(() => {
            if (this.progressBar) {
                this.progressBar.style.opacity = '0';
                setTimeout(() => {
                    if (this.progressBar) this.progressBar.style.width = '0%';
                }, 200);
            }
        }, 150);
    }

    async loadUrl(url, pushState = true) {
        if (this.isNavigating) return;
        this.isNavigating = true;
        this.showProgressBar();

        try {
            this.updateProgressBar(60);
            const response = await fetch(url, {
                headers: {
                    'X-PJAX': '1',
                    'X-Requested-With': 'XMLHttpRequest',
                },
            });

            if (!response.ok) {
                window.location.href = url;
                return;
            }

            const html = await response.text();
            this.updateProgressBar(85);

            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');

            const newMain = doc.querySelector('#main-content');
            const currentMain = document.querySelector('#main-content');

            if (newMain && currentMain) {
                currentMain.innerHTML = newMain.innerHTML;
            } else {
                window.location.href = url;
                return;
            }

            const newTitle = doc.querySelector('#page-header-title');
            const currentTitle = document.querySelector('#page-header-title');
            if (newTitle && currentTitle) {
                currentTitle.innerHTML = newTitle.innerHTML;
            }

            document.title = doc.title || document.title;

            const newNav = doc.querySelector('nav');
            const currentNav = document.querySelector('nav');
            if (newNav && currentNav) {
                currentNav.innerHTML = newNav.innerHTML;
            }

            if (pushState) {
                window.history.pushState({}, '', url);
            }

            window.scrollTo({ top: 0, behavior: 'smooth' });

            // Re-execute scripts in replaced content
            const scripts = currentMain.querySelectorAll('script');
            scripts.forEach((oldScript) => {
                const newScript = document.createElement('script');
                Array.from(oldScript.attributes).forEach(attr => newScript.setAttribute(attr.name, attr.value));
                newScript.appendChild(document.createTextNode(oldScript.innerHTML));
                oldScript.parentNode.replaceChild(newScript, oldScript);
            });

            // Dispatch custom event for page updates
            document.dispatchEvent(new CustomEvent('page:loaded'));

            if (window.Alpine) {
                window.Alpine.initTree(currentMain);
            }

        } catch (error) {
            window.location.href = url;
        } finally {
            this.hideProgressBar();
            this.isNavigating = false;
        }
    }
}

new SpaRouter();

Alpine.data('liveTable', (url) => ({
    loading: false,
    error: null,

    init() {
        this.$root.addEventListener('click', (e) => {
            const link = e.target.closest('a[data-page]');
            if (!link) return;
            e.preventDefault();
            this.go(link.getAttribute('href'));
        });
    },

    collectParams() {
        const form = this.$root.querySelector('[data-live-form]');
        const params = new URLSearchParams();

        if (form) {
            const fd = new FormData(form);
            fd.forEach((value, key) => {
                if (String(value).trim() !== '') params.set(key, String(value).trim());
            });
        }

        return params;
    },

    async reload() {
        const params = this.collectParams();
        params.delete('page');
        await this.go(url + '?' + params.toString());
    },

    async go(targetUrl) {
        const separator = targetUrl.includes('?') ? '&' : '?';
        this.loading = true;
        this.error = null;

        try {
            const res = await fetch(targetUrl + separator + 'live=1', {
                headers: { 'X-Live-Table': '1' },
            });

            if (!res.ok) throw new Error('Server error');

            const html = await res.text();
            const container = this.$root.querySelector('[data-live-results]');
            if (container) container.innerHTML = html;
        } catch (err) {
            this.error = 'Gagal memuat data.';
        } finally {
            this.loading = false;
        }
    },
}));

Alpine.start();
