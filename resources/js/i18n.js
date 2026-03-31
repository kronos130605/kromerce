export function detectLocale() {
    return localStorage.getItem('kromerce_locale') || 'es';
}

export function setLocaleCookie(locale) {
    document.cookie = `kromerce_locale=${locale};path=/;max-age=${30 * 24 * 60 * 60};SameSite=Lax`;
    localStorage.setItem('kromerce_locale', locale);
}

