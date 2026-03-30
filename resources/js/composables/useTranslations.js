import { onMounted } from 'vue';
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
 * useTranslations('storefront'); // or 'auth', 'business', 'products'
 * 
 * // Or load custom modules
 * useTranslations(['common', 'storefront', 'products']);
 */
export function useTranslations(modules) {
    const { locale } = useI18n();

    onMounted(async () => {
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
                default:
                    console.warn(`Unknown translation preset: ${modules}`);
            }
        } 
        // If modules is an array, load those specific modules
        else if (Array.isArray(modules)) {
            await loadTranslationModules(locale.value, modules);
        }
    });

    return {
        locale
    };
}
