import { createI18n } from 'vue-i18n';

// Import translation files from the existing structure
import es from './i18n/locales/es.json';
import en from './i18n/locales/en.json';

// Create i18n instance
const i18n = createI18n({
    legacy: false,
    locale: 'es', // default locale (matching the existing setup)
    fallbackLocale: 'es',
    messages: {
        es,
        en
    },
    globalInjection: true
});

export { i18n };
