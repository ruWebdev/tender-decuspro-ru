import '../css/app.css';
import './bootstrap';

// Tabler Core
import '@tabler/core/dist/js/tabler';
import '@tabler/core/dist/css/tabler.min.css';

import { createInertiaApp, router } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { createApp, h, onMounted } from 'vue';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';

// Tabler Icons
import * as TablerIcons from '@tabler/icons-vue';

// Initialize Tabler
function initializeTabler() {
    if (window.tabler) {
        window.tabler.init();
    }
}

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) =>
        resolvePageComponent(
            `./Pages/${name}.vue`,
            import.meta.glob('./Pages/**/*.vue'),
        ),
    setup({ el, App, props, plugin }) {
        const app = createApp({
            render: () => h(App, props),
            setup() {
                onMounted(() => {
                    initializeTabler();
                });
            }
        });

        // Register all Tabler Icons globally
        Object.entries(TablerIcons).forEach(([name, component]) => {
            if (name[0] === 'I' && name[1] === 'c' && name[2] === 'o' && name[3] === 'n') {
                app.component(name, component);
            }
        });

        return app
            .use(plugin)
            .use(ZiggyVue)
            .mount(el);
    },
    progress: {
        color: '#4B5563',
    },
});

// Переинициализация UI после навигации Inertia
router.on('navigate', () => {
    if (document.readyState === 'complete') {
        try { initializeTabler(); } catch { }
    } else {
        window.addEventListener('load', () => {
            try { initializeTabler(); } catch { }
        }, { once: true });
    }
});
