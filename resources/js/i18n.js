import { createI18n } from 'vue-i18n';

// Get saved locale from localStorage
const savedLocale = localStorage.getItem('kromerce_locale') || 'es';

// Create i18n instance with lazy loading support
const i18n = createI18n({
    legacy: false,
    locale: savedLocale,
    fallbackLocale: 'es',
    messages: {
        es: {},
        en: {}
    },
    globalInjection: true,
    // Enable missing handler for debugging
    missingWarn: false,
    fallbackWarn: false
});

// Lazy load translation modules
const loadedModules = {
    es: new Set(),
    en: new Set()
};

/**
 * Load a translation module dynamically
 * @param {string} locale - 'es' or 'en'
 * @param {string} module - Module name (e.g., 'common', 'auth', 'storefront')
 */
export async function loadTranslationModule(locale, module) {
    // Check if already loaded
    if (loadedModules[locale].has(module)) {
        return;
    }

    try {
        let messages;
        
        // Dynamic import based on locale and module
        if (locale === 'es') {
            messages = await import(`./i18n/locales/es/${module}.json`);
        } else if (locale === 'en') {
            // Try to load English, fallback to Spanish if not available
            try {
                messages = await import(`./i18n/locales/en/${module}.json`);
            } catch (error) {
                console.warn(`English translation for ${module} not found, using Spanish fallback`);
                messages = await import(`./i18n/locales/es/${module}.json`);
            }
        }

        // Merge the loaded module into existing messages
        const currentMessages = i18n.global.messages.value[locale] || {};
        
        // If module has nested structure (like storefront), preserve it
        // Otherwise merge at root level
        if (module === 'storefront' || module === 'orders') {
            i18n.global.setLocaleMessage(locale, {
                ...currentMessages,
                [module]: messages.default
            });
        } else {
            i18n.global.setLocaleMessage(locale, {
                ...currentMessages,
                ...messages.default
            });
        }

        loadedModules[locale].add(module);
        console.log(`✅ Loaded ${locale}/${module} translations`);
    } catch (error) {
        console.error(`Failed to load translation module ${locale}/${module}:`, error);
    }
}

/**
 * Load multiple translation modules at once
 * @param {string} locale - 'es' or 'en'
 * @param {string[]} modules - Array of module names
 */
export async function loadTranslationModules(locale, modules) {
    await Promise.all(modules.map(module => loadTranslationModule(locale, module)));
}

/**
 * Preload common translations (always needed)
 */
export async function preloadCommonTranslations() {
    const locale = i18n.global.locale.value;
    await loadTranslationModules(locale, ['common', 'errors']);
}

/**
 * Load translations for authentication pages
 */
export async function loadAuthTranslations() {
    const locale = i18n.global.locale.value;
    await loadTranslationModules(locale, ['common', 'auth', 'errors']);
}

/**
 * Load translations for business dashboard
 */
export async function loadBusinessTranslations() {
    const locale = i18n.global.locale.value;
    await loadTranslationModules(locale, ['common', 'business', 'dashboard', 'products', 'orders', 'errors']);
}

/**
 * Load translations for storefront
 */
export async function loadStorefrontTranslations() {
    const locale = i18n.global.locale.value;
    await loadTranslationModules(locale, ['common', 'storefront', 'errors']);
}

/**
 * Load translations for product management
 */
export async function loadProductTranslations() {
    const locale = i18n.global.locale.value;
    await loadTranslationModules(locale, ['common', 'products', 'errors']);
}

// Preload common translations on init
preloadCommonTranslations();

export { i18n };
