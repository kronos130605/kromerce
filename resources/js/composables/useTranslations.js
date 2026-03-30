import { onMounted, watch } from 'vue';
import { useI18n } from 'vue-i18n';
import {
    loadAuthTranslations,
    loadBusinessTranslations,
    loadStorefrontTranslations,
    loadProductTranslations,
    loadTranslationModules
} from '@/i18n.js';

/**
 * Composable to load translations for specific views
 * Usage in components:
 *
 * import { useTranslations } from '@/composables/useTranslations';
 *
 * // In setup()
 * useTranslations('storefront'); // or 'auth', 'business', 'products', 'dashboard'
 *
 * // Or load custom modules
 * useTranslations(['common', 'storefront', 'products']);
 */
export function useTranslations(modules) {
    const { locale } = useI18n();

    const loadTranslations = async () => {
        // If modules is a string (preset), load that preset
        if (typeof modules === 'string') {
            switch (modules) {
                case 'auth':
                    await loadAuthTranslations();
                    break;
                case 'business':
                    await loadBusinessTranslations();
                    break;
                case 'storefront':
                    await loadStorefrontTranslations();
                    break;
                case 'products':
                    await loadProductTranslations();
                    break;
                case 'dashboard':
                    // Load dashboard translations
                    await loadTranslationModules(locale.value, ['common', 'dashboard', 'errors']);
                    break;
                default:
                    // If it's a string but not a preset, treat it as a single module
                    await loadTranslationModules(locale.value, [modules]);
            }
        }
        // If modules is an array, load those specific modules
        else if (Array.isArray(modules)) {
            await loadTranslationModules(locale.value, modules);
        }
    };

    onMounted(loadTranslations);

    // Watch for locale changes and reload translations
    watch(locale, (newLocale, oldLocale) => {
        if (newLocale !== oldLocale) {
            console.log(`🌍 Locale changed from ${oldLocale} to ${newLocale}, reloading translations...`);
            loadTranslations();
        }
    });

    return {
        locale
    };
}
