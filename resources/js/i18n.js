import { createI18n } from 'vue-i18n';

// Import translation files
import enMessages from './locales/en.json';
import esMessages from './locales/es.json';

// Create i18n instance
const i18n = createI18n({
    legacy: false,
    locale: 'en', // default locale
    fallbackLocale: 'en',
    messages: {
        en: enMessages,
        es: esMessages,
    },
});

export { i18n };
