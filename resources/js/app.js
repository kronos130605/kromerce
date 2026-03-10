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
    resolve: (name) => {
        // Check if name already has .vue extension
        const hasVueExtension = name.endsWith('.vue');
        const baseName = hasVueExtension ? name.slice(0, -4) : name;
        
        // Handle full module paths (already includes modules/structure)
        if (baseName.startsWith('modules/')) {
            const modulePath = `./${baseName}.vue`;
            return resolvePageComponent(
                modulePath,
                import.meta.glob('./modules/**/*.vue'),
            );
        }
        
        // For marketing pages, use the full path structure
        if (baseName === 'Kromerce') {
            return resolvePageComponent(
                './modules/marketing/pages/Kromerce.vue',
                import.meta.glob('./modules/**/*.vue'),
            );
        }
        
        // Handle dashboard routes with specific mapping
        if (baseName.startsWith('Dashboard/')) {
            const dashboardPage = baseName.replace('Dashboard/', '');
            const modulePath = `./modules/dashboard/pages/${dashboardPage}.vue`;
            
            return resolvePageComponent(
                modulePath,
                import.meta.glob('./modules/**/*.vue'),
            );
        }
        
        // Handle dashboard page names without Dashboard/ prefix
        if (baseName.startsWith('Dashboard') || baseName.includes('Dashboard')) {
            // Handle names like 'DashboardCustomer', 'DashboardBusiness', 'Index', etc.
            const modulePath = `./modules/dashboard/pages/${baseName}.vue`;
            
            return resolvePageComponent(
                modulePath,
                import.meta.glob('./modules/**/*.vue'),
            );
        }
        
        // Handle auth routes (special case for Laravel Breeze structure)
        if (baseName.startsWith('Auth/')) {
            const authPage = baseName.replace('Auth/', '');
            const modulePath = `./modules/auth/pages/${authPage}.vue`;
            
            return resolvePageComponent(
                modulePath,
                import.meta.glob('./modules/**/*.vue'),
            );
        }
        
        // Handle Business routes (special case)
        if (baseName.startsWith('Business/')) {
            const businessPage = baseName.replace('Business/', '');
            const pagePath = `./Pages/Business/${businessPage}.vue`;
            
            return resolvePageComponent(
                pagePath,
                import.meta.glob('./Pages/**/*.vue'),
            );
        }
        
        // Handle Business Index (main SPA page)
        if (baseName === 'Business/Index') {
            const pagePath = `./Pages/Business/Index.vue`;
            
            return resolvePageComponent(
                pagePath,
                import.meta.glob('./Pages/**/*.vue'),
            );
        }
        
        // Handle other module routes (Profile/, Products/, etc.)
        if (baseName.includes('/')) {
            const [module, page] = baseName.split('/');
            const modulePath = `./modules/${module.toLowerCase()}/pages/${page}.vue`;
            
            return resolvePageComponent(
                modulePath,
                import.meta.glob('./modules/**/*.vue'),
            );
        }
        
        // Try to resolve from modules first, then fallback to Pages
        const modulePath = `./modules/${baseName}.vue`;
        const pagePath = `./Pages/${baseName}.vue`;
        
        return resolvePageComponent(
            modulePath,
            import.meta.glob('./modules/**/*.vue'),
        ) || resolvePageComponent(
            pagePath,
            import.meta.glob('./Pages/**/*.vue'),
        );
    },
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
