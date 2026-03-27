import { ref } from 'vue';
import { router } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';

/**
 * Composable para gestión de órdenes con DataTable
 *
 * @param {Object} options - Configuración inicial
 * @param {Array} options.initialOrders - Órdenes iniciales
 * @param {Object} options.initialFilters - Filtros iniciales
 * @returns {Object} Estado y métodos para gestión de órdenes
 */
export function useOrderManager(options = {}) {
    const { t } = useI18n();
    
    // Estado de la lista
    const orders = ref(options.initialOrders || []);
    const filters = ref(options.initialFilters || {});
    const loading = ref(false);
    const selectedOrder = ref(null);

    // Estado de modals
    const isViewOpen = ref(false);
    const isConfirmModalOpen = ref(false);
    const confirmModalConfig = ref({
        title: '',
        message: '',
        danger: false,
        onConfirm: null,
    });

    // Estado de visor de orden
    const openView = (order) => {
        selectedOrder.value = order;
        isViewOpen.value = true;
    };

    const closeView = () => {
        isViewOpen.value = false;
        selectedOrder.value = null;
    };

    // Acciones de confirmación
    const confirmAction = (config) => {
        confirmModalConfig.value = {
            title: config.title,
            message: config.message,
            danger: config.danger || false,
            onConfirm: config.onConfirm,
        };
        isConfirmModalOpen.value = true;
    };

    const closeConfirmModal = () => {
        isConfirmModalOpen.value = false;
        confirmModalConfig.value = {
            title: '',
            message: '',
            danger: false,
            onConfirm: null,
        };
    };

    // Acciones sobre órdenes
    const confirmCancel = (order) => {
        confirmAction({
            title: t('orders.messages.cancel_title'),
            message: t('orders.messages.cancel_confirmation', { number: order.order_number }),
            danger: true,
            onConfirm: () => executeCancel(order),
        });
    };

    const executeCancel = (order) => {
        loading.value = true;
        closeConfirmModal();
        
        router.delete(`/orders/${order.id}`, {
            preserveScroll: true,
            onSuccess: () => {
                refreshOrders();
            },
            onError: (errors) => {
                console.error('Cancel failed:', errors);
                alert(t('orders.messages.cancel_failed'));
            },
            onFinish: () => {
                loading.value = false;
            },
        });
    };

    const updateStatus = (order, newStatus) => {
        loading.value = true;
        
        router.put(`/orders/${order.id}/status`, { status: newStatus }, {
            preserveScroll: true,
            onSuccess: () => {
                refreshOrders();
            },
            onError: (errors) => {
                console.error('Status update failed:', errors);
                alert(t('orders.messages.status_update_failed'));
            },
            onFinish: () => {
                loading.value = false;
            },
        });
    };

    const updatePaymentStatus = (order, paymentStatus) => {
        loading.value = true;
        
        router.put(`/orders/${order.id}/payment`, { payment_status: paymentStatus }, {
            preserveScroll: true,
            onSuccess: () => {
                refreshOrders();
            },
            onError: (errors) => {
                console.error('Payment status update failed:', errors);
                alert(t('orders.messages.payment_update_failed'));
            },
            onFinish: () => {
                loading.value = false;
            },
        });
    };

    const refreshOrders = () => {
        router.reload({ only: ['orders'] });
    };

    // Helpers para badges de estado
    const getStatusColor = (status) => ({
        pending: 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
        processing: 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
        shipped: 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200',
        delivered: 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
        cancelled: 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
    }[status] || 'bg-gray-100 text-gray-800');

    const getPaymentStatusColor = (status) => ({
        pending: 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
        paid: 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
        failed: 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
        refunded: 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
    }[status] || 'bg-gray-100 text-gray-800');

    return {
        // Estado
        orders,
        filters,
        loading,
        selectedOrder,
        isViewOpen,
        isConfirmModalOpen,
        confirmModalConfig,

        // Acciones
        openView,
        closeView,
        confirmAction,
        closeConfirmModal,
        confirmCancel,
        updateStatus,
        updatePaymentStatus,
        refreshOrders,

        // Helpers
        getStatusColor,
        getPaymentStatusColor,
    };
}
