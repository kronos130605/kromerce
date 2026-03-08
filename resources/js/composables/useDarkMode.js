import { ref, onMounted, onUnmounted, nextTick } from 'vue'

export function useDarkMode() {
    const isDark = ref(false)

    const checkDarkMode = () => {
        const hasDarkClass = document.documentElement.classList.contains('dark')
        if (isDark.value !== hasDarkClass) {
            isDark.value = hasDarkClass
        }
    }

    const setDarkMode = (enabled) => {
        if (enabled) {
            document.documentElement.classList.add('dark')
        } else {
            document.documentElement.classList.remove('dark')
        }
        // Force update reactive value
        isDark.value = enabled
    }

    const toggleDarkMode = () => {
        const newDarkValue = !isDark.value
        setDarkMode(newDarkValue)
        
        // Save to localStorage
        try {
            localStorage.setItem('kromerce_theme', newDarkValue ? 'dark' : 'light')
        } catch {
            // ignore
        }
        
        return newDarkValue
    }

    const initializeDarkMode = () => {
        // Check localStorage first, then system preference
        let shouldBeDark = false
        
        try {
            const stored = localStorage.getItem('kromerce_theme')
            if (stored === 'dark' || stored === 'light') {
                shouldBeDark = stored === 'dark'
            } else {
                // Use system preference
                shouldBeDark = window.matchMedia('(prefers-color-scheme: dark)').matches
            }
        } catch {
            // Fallback to system preference
            shouldBeDark = window.matchMedia('(prefers-color-scheme: dark)').matches
        }
        
        setDarkMode(shouldBeDark)
    }

    // Initialize immediately if DOM is ready
    if (typeof window !== 'undefined' && document.documentElement) {
        initializeDarkMode()
    }

    onMounted(() => {
        // Ensure initialization after mount
        nextTick(() => {
            initializeDarkMode()
        })
        
        const observer = new MutationObserver(() => {
            checkDarkMode()
        })
        
        observer.observe(document.documentElement, {
            attributes: true,
            attributeFilter: ['class']
        })
        
        // Listen for system theme changes
        const mediaQuery = window.matchMedia('(prefers-color-scheme: dark)')
        const handleSystemThemeChange = (e) => {
            // Only apply system theme if user hasn't set a preference
            try {
                const stored = localStorage.getItem('kromerce_theme')
                if (!stored) {
                    setDarkMode(e.matches)
                }
            } catch {
                // ignore
            }
        }
        
        mediaQuery.addEventListener('change', handleSystemThemeChange)
        
        onUnmounted(() => {
            observer.disconnect()
            mediaQuery.removeEventListener('change', handleSystemThemeChange)
        })
    })

    return { isDark, toggleDarkMode, setDarkMode }
}
