<script setup>
import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import { useTranslations } from '@/composables/useTranslations';
import BusinessLayout from '@/layouts/BusinessLayout.vue';
import DataTable from '@/components/shared/DataTable.vue';
import ConfirmationModal from '@/components/shared/ConfirmationModal.vue';
import { useOrderManager } from '../composables/useOrderManager.js';

const { t } = useI18n();
const page = usePage();

// Load business translations (includes orders)
useTranslations(['orders', 'dashboard_nav']);

const props = defineProps({
    orders: Object,
    filters: Object,
    statistics: Object,
});

const {
    selectedOrder,
    isViewOpen,
    isConfirmModalOpen,
    confirmModalConfig,
    loading,
    openView,
    closeView,
    closeConfirmModal,
    confirmCancel,
    updateStatus,
    updatePaymentStatus,
    getStatusColor,
    getPaymentStatusColor,
} = useOrderManager({
    initialOrders: props.orders?.data || [],
    initialFilters: props.filters,
});

// Statistics cards
const statsCards = computed(() => [
    {
        title: t('orders.statistics.total'),
        value: props.statistics?.total_orders || 0,
        icon: 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2',
        color: 'blue'
    },
    {
        title: t('orders.statistics.pending'),
        value: props.statistics?.pending_orders || 0,
        icon: 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z',
        color: 'yellow'
    },
    {
        title: t('orders.statistics.processing'),
        value: props.statistics?.processing_orders || 0,
        icon: 'M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15',
        color: 'purple'
    },
    {
        title: t('orders.statistics.delivered'),
        value: props.statistics?.delivered_orders || 0,
        icon: 'M5 13l4 4L19 7',
        color: 'green'
    },
]);

// Table columns configuration
const tableColumns = [
    {
        key: 'order_number',
        label: t('orders.fields.order'),
        type: 'text',
    },
    {
        key: 'customer',
        label: t('orders.fields.customer'),
        type: 'custom',
    },
    {
        key: 'total_amount',
        label: t('orders.fields.total'),
        type: 'currency',
        currencyKey: 'currency',
    },
    {
        key: 'status',
        label: t('orders.fields.status'),
        type: 'badge',
        badgeColors: {
            pending: 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
            processing: 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
            shipped: 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200',
            delivered: 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
            cancelled: 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
        },
    },
    {
        key: 'payment_status',
        label: t('orders.fields.payment'),
        type: 'badge',
        badgeColors: {
            pending: 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
            paid: 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
            failed: 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
            refunded: 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
        },
    },
    {
        key: 'created_at',
        label: t('orders.fields.date'),
        type: 'date',
    },
];

// Table actions configuration
const tableActions = [
    { key: 'view', color: 'blue' },
    { key: 'cancel', color: 'red', show: (item) => item.status !== 'cancelled' && item.status !== 'delivered' },
];

// Handle table actions
const handleTableAction = ({ action, item }) => {
    if (action === 'view') openView(item);
    else if (action === 'cancel') confirmCancel(item);
};

const formatCurrency = (amount, currency = 'USD') => {
    return new Intl.NumberFormat('en-US', { style: 'currency', currency }).format(amount);
};
</script>

<template>
    <BusinessLayout>
        <div class="space-y-6">
            <!-- Header -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h1 class="text-xl font-bold text-gray-900 dark:text-white">{{ t('orders.title') }}</h1>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">{{ t('orders.subtitle') }}</p>
                </div>
            </div>

            <!-- Statistics -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                <div v-for="stat in statsCards" :key="stat.title" class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4">
                    <div class="flex items-center">
                        <div :class="`p-2 bg-${stat.color}-100 dark:bg-${stat.color}-900/30 rounded-lg`">
                            <svg class="w-5 h-5" :class="`text-${stat.color}-600 dark:text-${stat.color}-400`" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="stat.icon" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-xs text-gray-600 dark:text-gray-400">{{ stat.title }}</p>
                            <p class="text-base font-bold text-gray-900 dark:text-white">{{ stat.value }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Orders Table -->
            <DataTable
                :data="orders.data"
                :columns="tableColumns"
                :actions="tableActions"
                @action="handleTableAction"
                :empty-title="t('orders.empty.title')"
                :empty-description="t('orders.empty.description')"
                empty-icon="📦"
            >
                <template #cell-customer="{ item }">
                    <div>
                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ item.customer?.name || '-' }}</p>
                        <p class="text-xs text-gray-500">{{ item.customer?.email || '' }}</p>
                    </div>
                </template>
            </DataTable>
        </div>

        <!-- Order View Modal -->
        <div v-if="isViewOpen" class="fixed inset-0 z-50 overflow-hidden">
            <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" @click="closeView" />
            <div class="absolute inset-y-0 right-0 w-full max-w-2xl bg-white dark:bg-gray-900 shadow-2xl flex flex-col" @click.stop>
                <header class="flex items-center justify-between px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
                        {{ t('orders.view.title') }} #{{ selectedOrder?.order_number }}
                    </h2>
                    <button @click="closeView" class="p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </header>

                <div v-if="selectedOrder" class="flex-1 overflow-y-auto p-6 space-y-6">
                    <!-- Order Info -->
                    <div class="grid grid-cols-2 gap-4">
                        <div class="p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">
                            <p class="text-gray-500 dark:text-gray-400">{{ t('orders.fields.status') }}</p>
                            <span :class="['px-2 py-1 rounded-full text-xs font-medium', getStatusColor(selectedOrder.status)]">
                                {{ selectedOrder.status }}
                            </span>
                        </div>
                        <div class="p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">
                            <p class="text-gray-500 dark:text-gray-400">{{ t('orders.fields.payment') }}</p>
                            <span :class="['px-2 py-1 rounded-full text-xs font-medium', getPaymentStatusColor(selectedOrder.payment_status)]">
                                {{ selectedOrder.payment_status }}
                            </span>
                        </div>
                    </div>

                    <!-- Total -->
                    <div class="text-center p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ t('orders.fields.total') }}</p>
                        <p class="text-3xl font-bold text-gray-900 dark:text-white">
                            {{ formatCurrency(selectedOrder.total_amount, selectedOrder.currency) }}
                        </p>
                    </div>

                    <!-- Customer -->
                    <div v-if="selectedOrder.customer" class="p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">{{ t('orders.fields.customer') }}</p>
                        <p class="font-medium text-gray-900 dark:text-white">{{ selectedOrder.customer.name }}</p>
                        <p class="text-sm text-gray-500">{{ selectedOrder.customer.email }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Confirmation Modal -->
        <ConfirmationModal
            :show="isConfirmModalOpen"
            :title="confirmModalConfig.title"
            :message="confirmModalConfig.message"
            :danger="confirmModalConfig.danger"
            :loading="loading"
            @confirm="confirmModalConfig.onConfirm"
            @cancel="closeConfirmModal"
        />
    </BusinessLayout>
</template>

<style scoped>
.btn {
    @apply inline-flex items-center px-4 py-2 rounded-lg text-sm font-medium transition-colors;
}
.btn-primary {
    @apply bg-blue-600 text-white hover:bg-blue-700;
}
</style>
