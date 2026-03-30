/**
 * i18n Utilities
 * 
 * Nota: Con la nueva arquitectura backend-driven, las traducciones
 * vienen vía Inertia props y se consumen con useTranslations().
 * 
 * Este archivo mantiene compatibilidad legacy y exporta utilidades.
 */

// Detectar locale del navegador/localStorage
export function detectLocale() {
    return localStorage.getItem('kromerce_locale') || 'es';
}

// Setear locale en cookie para backend
export function setLocaleCookie(locale) {
    document.cookie = `kromerce_locale=${locale};path=/;max-age=${30 * 24 * 60 * 60};SameSite=Lax`;
    localStorage.setItem('kromerce_locale', locale);
}

// Exportar función t simple para uso global (opcional)
export function t(key, replacements = {}) {
    if (!key) return '';
    
    // En la nueva arquitectura, esto se maneja via useTranslations()
    // Esta función es para compatibilidad legacy
    console.warn('t() global llamado - usar useTranslations() composable en su lugar');
    return key;
}

