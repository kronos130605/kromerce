import { ref, computed } from 'vue';
import { useI18n } from 'vue-i18n';

/**
 * Composable para sistema de notificaciones toast
 */
const toasts = ref([]);
let toastId = 0;

export function useToast() {
    const { t } = useI18n();

    /**
     * Show a toast notification
     */
    const show = (message, type = 'success', duration = 3000) => {
        const id = ++toastId;
        const toast = {
            id,
            message,
            type,
            duration,
        };

        toasts.value.push(toast);

        // Auto remove after duration
        setTimeout(() => {
            remove(id);
        }, duration);

        return id;
    };

    /**
     * Show success toast
     */
    const success = (message, duration) => {
        return show(message || t('common.success'), 'success', duration);
    };

    /**
     * Show error toast
     */
    const error = (message, duration) => {
        return show(message || t('common.error'), 'error', duration);
    };

    /**
     * Show warning toast
     */
    const warning = (message, duration) => {
        return show(message || t('common.warning'), 'warning', duration);
    };

    /**
     * Show info toast
     */
    const info = (message, duration) => {
        return show(message || t('common.info'), 'info', duration);
    };

    /**
     * Remove a toast by id
     */
    const remove = (id) => {
        const index = toasts.value.findIndex(t => t.id === id);
        if (index > -1) {
            toasts.value.splice(index, 1);
        }
    };

    /**
     * Clear all toasts
     */
    const clear = () => {
        toasts.value = [];
    };

    return {
        toasts: computed(() => toasts.value),
        show,
        success,
        error,
        warning,
        info,
        remove,
        clear,
    };
}
