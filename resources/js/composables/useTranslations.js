import { usePage } from '@inertiajs/vue3'
import { computed } from 'vue'

export function useTranslations() {
    const page = usePage()

    // Use computed to ensure reactivity
    const translations = computed(() => page.props.translations || {})

    function t(key, replacements = {}) {
        if (!key) return ''

        const [file, ...rest] = key.split('.')

        // Access .value for computed, or directly if not
        const trans = translations.value || translations
        let value = rest.reduce((obj, segment) => obj?.[segment], trans[file])

        if (!value) return key // fallback: muestra la key si no existe

        // Handle pluralization: "singular|plural" format
        const count = replacements.count !== undefined ? replacements.count : replacements.n !== undefined ? replacements.n : null
        if (count !== null && value.includes('|')) {
            const parts = value.split('|')
            // If only one replacement provided, use appropriate form
            if (parts.length === 2) {
                value = count === 1 ? parts[0].trim() : parts[1].trim()
            }
        }

        // Reemplazos tipo {name} o :name
        Object.keys(replacements).forEach(k => {
            value = value.replace(new RegExp(`{${k}}`, 'g'), replacements[k])
            value = value.replace(new RegExp(`:${k}`, 'g'), replacements[k])
        })

        return value
    }

    return { t, translations }
}
