import { createI18n } from 'vue-i18n';
import es from './locales/es.json';
import en from './locales/en.json';

const savedLocale = localStorage.getItem('kromerce_locale') || 'es';

const i18n = createI18n({
  legacy: false,
  locale: savedLocale,
  fallbackLocale: 'es',
  messages: {
    es,
    en
  },
  globalInjection: true
});

export default i18n;
