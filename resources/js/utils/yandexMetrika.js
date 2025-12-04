const METRIKA_ID = 105676095;
const METRIKA_SRC = `https://mc.yandex.ru/metrika/tag.js?id=${METRIKA_ID}`;

function isBrowser() {
    return typeof window !== 'undefined' && typeof document !== 'undefined';
}

function isAdminPath(pathname) {
    if (!pathname) {
        return false;
    }

    return pathname === '/admin' || pathname.startsWith('/admin/');
}

export function initYandexMetrika() {
    if (!isBrowser()) {
        return;
    }

    if (window.ym && typeof window.ym === 'function') {
        return;
    }

    (function (m, e, t, r, i, k, a) {
        m[i] = m[i] || function () {
            (m[i].a = m[i].a || []).push(arguments);
        };
        m[i].l = 1 * new Date();
        for (let j = 0; j < e.scripts.length; j++) {
            if (e.scripts[j].src === r) {
                return;
            }
        }
        k = e.createElement(t);
        a = e.getElementsByTagName(t)[0];
        k.async = 1;
        k.src = r;
        a.parentNode.insertBefore(k, a);
    })(window, document, 'script', METRIKA_SRC, 'ym');

    window.ym(METRIKA_ID, 'init', {
        ssr: true,
        webvisor: true,
        clickmap: true,
        ecommerce: 'dataLayer',
        accurateTrackBounce: true,
        trackLinks: true,
    });
}

export function trackPageView(url) {
    if (!isBrowser() || !window.ym || typeof window.ym !== 'function') {
        return;
    }

    if (!url || isAdminPath(new URL(url, window.location.origin).pathname)) {
        return;
    }

    window.ym(METRIKA_ID, 'hit', url);
}

export function handleMetrikaOnNavigation() {
    if (!isBrowser()) {
        return;
    }

    const url = window.location.pathname + window.location.search + window.location.hash;

    if (isAdminPath(window.location.pathname)) {
        return;
    }

    initYandexMetrika();
    trackPageView(url);
}
