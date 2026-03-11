import { createI18n } from 'vue-i18n';

// Import translation files from the modular structure
import commonEs from './i18n/locales/es/common.json';
import authEs from './i18n/locales/es/auth.json';
import dashboardEs from './i18n/locales/es/dashboard.json';
import productsEs from './i18n/locales/es/products.json';
import businessEs from './i18n/locales/es/business.json';
import errorsEs from './i18n/locales/es/errors.json';

import commonEn from './i18n/locales/en/common.json';
import authEn from './i18n/locales/en/auth.json';
import dashboardEn from './i18n/locales/en/dashboard.json';
import productsEn from './i18n/locales/en/products.json';
import businessEn from './i18n/locales/en/business.json';
import errorsEn from './i18n/locales/en/errors.json';

// Merge translations for each locale
const mergeTranslations = (common, auth, dashboard, products, business, errors) => ({
  ...common,
  ...auth,
  ...dashboard,
  ...products,
  ...business,
  ...errors
});

// Create i18n instance
const i18n = createI18n({
    legacy: false,
    locale: 'es', // default locale (matching the existing setup)
    fallbackLocale: 'es',
    messages: {
        es: mergeTranslations(commonEs, authEs, dashboardEs, productsEs, businessEs, errorsEs),
        en: mergeTranslations(commonEn, authEn, dashboardEn, productsEn, businessEn, errorsEn)
    },
    globalInjection: true
});

export { i18n };
