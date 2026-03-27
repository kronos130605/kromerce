<script setup>
import { ref, computed } from 'vue';
import { useI18n } from 'vue-i18n';
import { Link, router } from '@inertiajs/vue3';
import DashboardList from '@/components/ui/data-display/DashboardList.vue';

const { t } = useI18n();

const props = defineProps({
    orders: {
        type: Array,
        default: () => []
    },
    loading: {
        type: Boolean,
        default: false
    }
});

const selectedOrder = ref(null);
const isDrawerOpen = ref(false);
const updatingStatus = ref(null);

const recentOrders = computed(() => {
    return props.orders.slice(0, 5);
});

const openOrderDrawer = (order) => {
    selectedOrder.value = order;
    isDrawerOpen.value = true;
};

const closeDrawer = () => {
    isDrawerOpen.value = false;
    setTimeout(() => {
        selectedOrder.value = null;
    }, 300);
};

const updateStatus = async (orderId, newStatus) => {
    updatingStatus.value = orderId;
    try {
        await router.patch(`/orders/${orderId}/status`, { status: newStatus }, {
            preserveScroll: true,
            onSuccess: () => {
                if (selectedOrder.value?.id === orderId) {
                    selectedOrder.value.status = newStatus;
                }
            }
        });
    } finally {
        updatingStatus.value = null;
    }
};

const getStatusColor = (status) => {
    const colors = {
        pending: 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/50 dark:text-yellow-200',
        processing: 'bg-blue-100 text-blue-800 dark:bg-blue-900/50 dark:text-blue-200',
        shipped: 'bg-purple-100 text-purple-800 dark:bg-purple-900/50 dark:text-purple-200',
        delivered: 'bg-green-100 text-green-800 dark:bg-green-900/50 dark:text-green-200',
        cancelled: 'bg-red-100 text-red-800 dark:bg-red-900/50 dark:text-red-200'
    };
    return colors[status] || colors.pending;
};

const getPaymentIcon = (method) => {
    const icons = {
        credit_card: '💳',
        cash: '💵',
        transfer: '🏦',
        wallet: '👛'
    };
    return icons[method] || '💳';
};

const formatDate = (dateString) => {
    if (!dateString) return '';
    const date = new Date(dateString);
    return date.toLocaleDateString('es-ES', { month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' });
};
</script>

<template>
    <div class="relative">
        <DashboardList
            :items="recentOrders"
            :loading="loading"
            :title="t('dashboard.recent_orders')"
            :description="t('dashboard.latest_orders_from_customers')"
            viewAllLink="/orders"
            :viewAllText="t('dashboard.view_all')"
            emptyIcon="📭"
            :emptyTitle="t('dashboard.no_orders_yet')"
            :emptyDescription="t('dashboard.orders_appear_here')"
            emptyActionLink="/orders"
            :emptyActionText="t('dashboard.view_orders')"
            emptyActionIcon="🛒"
            :skeletonRows="3"
            @itemClick="openOrderDrawer">

            <template #item="{ item: order }">
                <div class="flex items-start space-x-3">
                    <!-- Order Icon -->
                    <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/50 rounded-xl flex items-center justify-center flex-shrink-0">
                        <span class="text-lg">📦</span>
                    </div>

                    <!-- Order Info -->
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center space-x-2">
                            <h4 class="font-medium text-gray-900 dark:text-white truncate">
                                {{ t('dashboard.order') }} #{{ order.id }}
                            </h4>
                            <span :class="`px-2 py-0.5 rounded-full text-xs font-medium ${getStatusColor(order.status)}`">
                                {{ t(`orders.status.${order.status}`) }}
                            </span>
                            <span v-if="order.isPriority" class="px-2 py-0.5 bg-red-100 dark:bg-red-900/50 text-red-700 dark:text-red-300 rounded-full text-xs font-medium">
                                {{ t('dashboard.priority') }}
                            </span>
                        </div>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                            {{ order.customer }} • {{ order.items }} {{ t('dashboard.items') }}
                        </p>
                        <div class="flex items-center space-x-3 text-sm text-gray-500 dark:text-gray-400 mt-2">
                            <span>{{ formatDate(order.date) }}</span>
                            <span>•</span>
                            <span class="flex items-center space-x-1">
                                <span>{{ getPaymentIcon(order.paymentMethod) }}</span>
                                <span>{{ t(`payment.methods.${order.paymentMethod}`) }}</span>
                            </span>
                        </div>
                    </div>

                    <!-- Amount -->
                    <div class="text-right">
                        <p class="font-semibold text-gray-900 dark:text-white">${{ order.total.toLocaleString() }}</p>
                        <p v-if="order.paymentStatus === 'paid'" class="text-xs text-green-600 dark:text-green-400">
                            {{ t('orders.paid') }}
                        </p>
                        <p v-else class="text-xs text-yellow-600 dark:text-yellow-400">
                            {{ t('orders.pending_payment') }}
                        </p>
                    </div>
                </div>
            </template>
        </DashboardList>

        <!-- Order Detail Drawer -->
        <transition
            enter-active-class="transform transition ease-out duration-300"
            enter-from-class="translate-x-full"
            enter-to-class="translate-x-0"
            leave-active-class="transform transition ease-in duration-200"
            leave-from-class="translate-x-0"
            leave-to-class="translate-x-full">
            <div v-if="isDrawerOpen && selectedOrder"
                 class="fixed inset-y-0 right-0 w-full md:w-96 bg-white dark:bg-gray-800 shadow-2xl border-l border-gray-200 dark:border-gray-700 z-50 overflow-y-auto">

                <!-- Drawer Header -->
                <div class="sticky top-0 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 p-6 flex items-center justify-between">
                    <div>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white">
                            {{ t('dashboard.order') }} #{{ selectedOrder.id }}
                        </h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                            {{ formatDate(selectedOrder.date) }}
                        </p>
                    </div>
                    <button @click="closeDrawer"
                            class="p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <!-- Drawer Content -->
                <div class="p-6 space-y-6">
                    <!-- Status -->
                    <div>
                        <label class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2 block">
                            {{ t('orders.status.label') }}
                        </label>
                        <select :value="selectedOrder.status"
                                @change="updateStatus(selectedOrder.id, $event.target.value)"
                                :disabled="updatingStatus === selectedOrder.id"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                            <option value="pending">{{ t('orders.status.pending') }}</option>
                            <option value="processing">{{ t('orders.status.processing') }}</option>
                            <option value="shipped">{{ t('orders.status.shipped') }}</option>
                            <option value="delivered">{{ t('orders.status.delivered') }}</option>
                            <option value="cancelled">{{ t('orders.status.cancelled') }}</option>
                        </select>
                        <span v-if="updatingStatus === selectedOrder.id" class="text-sm text-blue-600 dark:text-blue-400 mt-1 block">
                            {{ t('common.updating') }}...
                        </span>
                    </div>

                    <!-- Customer -->
                    <div>
                        <label class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2 block">
                            {{ t('orders.customer') }}
                        </label>
                        <p class="text-gray-900 dark:text-white">{{ selectedOrder.customer }}</p>
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ selectedOrder.email }}</p>
                    </div>

                    <!-- Items -->
                    <div>
                        <label class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2 block">
                            {{ t('orders.items') }}
                        </label>
                        <div class="space-y-2">
                            <div v-for="item in selectedOrder.orderItems" :key="item.id"
                                 class="flex justify-between py-2 border-b border-gray-100 dark:border-gray-700 last:border-0">
                                <div>
                                    <p class="text-gray-900 dark:text-white">{{ item.name }}</p>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">x{{ item.quantity }}</p>
                                </div>
                                <p class="text-gray-900 dark:text-white">${{ item.subtotal.toLocaleString() }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Total -->
                    <div class="pt-4 border-t border-gray-200 dark:border-gray-700">
                        <div class="flex justify-between items-center">
                            <span class="text-lg font-semibold text-gray-900 dark:text-white">{{ t('orders.total') }}</span>
                            <span class="text-2xl font-bold text-blue-600 dark:text-blue-400">
                                ${{ selectedOrder.total.toLocaleString() }}
                            </span>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex space-x-3 pt-4">
                        <Link :href="`/orders/${selectedOrder.id}`"
                              class="flex-1 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-center transition-colors">
                            {{ t('orders.view_details') }}
                        </Link>
                        <button v-if="selectedOrder.status !== 'cancelled'"
                                @click="updateStatus(selectedOrder.id, 'cancelled')"
                                :disabled="updatingStatus === selectedOrder.id"
                                class="px-4 py-2 border border-red-300 dark:border-red-700 text-red-700 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/50 rounded-lg transition-colors">
                            {{ t('orders.cancel') }}
                        </button>
                    </div>
                </div>
            </div>
        </transition>

        <!-- Drawer Backdrop -->
        <transition
            enter-active-class="transition-opacity ease-out duration-300"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="transition-opacity ease-in duration-200"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0">
            <div v-if="isDrawerOpen"
                 @click="closeDrawer"
                 class="fixed inset-0 bg-black/50 z-40 backdrop-blur-sm">
            </div>
        </transition>
    </div>
</template>
