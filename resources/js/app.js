import '../css/app.css';
import './bootstrap';

import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { createApp, h } from 'vue';
import { ZiggyVue } from 'ziggy-js';
import i18n from './i18n';

// PWA Service Worker Registration - manejado por VitePWA
if ('serviceWorker' in navigator) {
    window.addEventListener('load', () => {
        // VitePWA se encarga del registro automáticamente
        console.log('PWA: Service Worker registration handled by VitePWA');
    });
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
        // Set locale from backend
        const currentLocale = props.initialPage.props.currentLocale || 'en';
        const translations = props.initialPage.props.translations || {};
        
        i18n.global.locale = currentLocale;
        i18n.global.setLocaleMessage(currentLocale, translations[currentLocale] || {});
        
        return createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue)
            .use(i18n)
            .mount(el);
    },
    progress: {
        color: '#4B5563',
    },
});
