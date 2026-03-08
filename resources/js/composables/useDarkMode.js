import { ref, onMounted, onUnmounted } from 'vue'

export function useDarkMode() {
    const isDark = ref(false)

    const checkDarkMode = () => {
        isDark.value = document.documentElement.classList.contains('dark')
    }

    onMounted(() => {
        checkDarkMode()
        
        const observer = new MutationObserver(checkDarkMode)
        observer.observe(document.documentElement, {
            attributes: true,
            attributeFilter: ['class']
        })
        
        onUnmounted(() => {
            observer.disconnect()
        })
    })

    return { isDark }
}
