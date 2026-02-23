import '../css/app.css';
import './bootstrap';

import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { createApp, h } from 'vue';
import { ZiggyVue } from 'ziggy-js';

// Import i18n configuration
import { createI18n } from 'vue-i18n';
import enMessages from './i18n/locales/en.json';
import esMessages from './i18n/locales/es.json';

// Create i18n instance
const i18n = createI18n({
    legacy: false,
    locale: 'en',
    fallbackLocale: 'en',
    messages: {
        en: enMessages,
        es: esMessages,
    },
});

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
        const currentLocale = props.initialPage?.props?.currentLocale || 'en';
        const translations = props.initialPage?.props?.translations || {};
        
        // Configure i18n with backend data
        i18n.global.locale.value = currentLocale;
        
        // Set messages for current locale
        if (translations[currentLocale]) {
            i18n.global.setLocaleMessage(currentLocale, translations[currentLocale]);
        }
        
        // Set fallback messages
        if (translations.en) {
            i18n.global.setLocaleMessage('en', translations.en);
        }
        
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
