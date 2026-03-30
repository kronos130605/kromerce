import { usePage } from '@inertiajs/vue3'

export function useTranslations() {
    const page = usePage()

    const translations = page.props.translations || {}

    function t(key, replacements = {}) {
        if (!key) return ''

        const [file, ...rest] = key.split('.')

        let value = rest.reduce((obj, segment) => obj?.[segment], translations[file])

        if (!value) return key // fallback: muestra la key si no existe

        // Reemplazos tipo {name}
        Object.keys(replacements).forEach(k => {
            value = value.replace(`:${k}`, replacements[k])
        })

        return value
    }

    return { t, translations }
}
