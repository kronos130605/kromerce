<script setup>
import { ref, computed } from 'vue';
import { useI18n } from 'vue-i18n';
import { Link, router } from '@inertiajs/vue3';

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

const recentOrders = ref([
    {
        id: 'ORD-2024-001',
        customer: 'John Doe',
        customerEmail: 'john@example.com',
        date: '2024-01-20',
        status: 'processing',
        total: 299.99,
        items: 3,
        paymentMethod: 'credit_card',
        shippingAddress: '123 Main St, New York, NY'
    },
    {
        id: 'ORD-2024-002',
        customer: 'Jane Smith',
        customerEmail: 'jane@example.com',
        date: '2024-01-20',
        status: 'shipped',
        total: 189.99,
        items: 2,
        paymentMethod: 'paypal',
        shippingAddress: '456 Oak Ave, Los Angeles, CA'
    },
    {
        id: 'ORD-2024-003',
        customer: 'Bob Johnson',
        customerEmail: 'bob@example.com',
        date: '2024-01-19',
        status: 'delivered',
        total: 449.99,
        items: 1,
        paymentMethod: 'stripe',
        shippingAddress: '789 Pine Rd, Chicago, IL'
    },
    {
        id: 'ORD-2024-004',
        customer: 'Alice Brown',
        customerEmail: 'alice@example.com',
        date: '2024-01-19',
        status: 'pending',
        total: 156.50,
        items: 4,
        paymentMethod: 'credit_card',
        shippingAddress: '321 Elm St, Houston, TX'
    },
    {
        id: 'ORD-2024-005',
        customer: 'Charlie Wilson',
        customerEmail: 'charlie@example.com',
        date: '2024-01-18',
        status: 'cancelled',
        total: 89.99,
        items: 2,
        paymentMethod: 'paypal',
        shippingAddress: '654 Maple Dr, Phoenix, AZ'
    }
]);

const getStatusColor = (status) => {
    const colors = {
        'pending': 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
        'processing': 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
        'shipped': 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200',
        'delivered': 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
        'cancelled': 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200'
    };
    return colors[status] || 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200';
};

const getPaymentIcon = (method) => {
    const icons = {
        'credit_card': '💳',
        'paypal': '🅿️',
        'stripe': '🎯',
        'bank_transfer': '🏦'
    };
    return icons[method] || '💳';
};

const skeletonArray = Array(5).fill(null);

const formatDate = (dateString) => {
    if (!dateString) return '';
    const date = new Date(dateString);
    return date.toLocaleDateString('es-ES', { month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' });
};
</script>

<template>
    <div class="space-y-6 mt-8">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-2xl font-bold text-gray-900 dark:text-white">{{ t('dashboard.recent_orders') }}</h3>
                <p class="text-gray-600 dark:text-gray-300 mt-1">{{ t('dashboard.latest_customer_orders') }}</p>
            </div>
            <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
        </div>

        <!-- Loading State -->
        <div v-if="loading" class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700 p-6">
            <div class="space-y-4">
                <div v-for="(n, index) in skeletonArray" :key="index" 
                     class="flex items-start space-x-3 p-4 rounded-lg animate-pulse">
                    <div class="w-10 h-10 bg-gray-200 dark:bg-gray-700 rounded-xl flex-shrink-0"></div>
                    <div class="flex-1 space-y-2">
                        <div class="w-32 h-4 bg-gray-200 dark:bg-gray-700 rounded"></div>
                        <div class="w-48 h-3 bg-gray-200 dark:bg-gray-700 rounded"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Empty State -->
        <div v-else-if="recentOrders.length === 0" class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700 p-12 text-center">
            <div class="w-20 h-20 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-4">
                <span class="text-4xl">📭</span>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">{{ t('dashboard.no_orders_yet') }}</h3>
            <p class="text-gray-500 dark:text-gray-400 mb-6 max-w-sm mx-auto">{{ t('dashboard.orders_appear_here') }}</p>
            <Link href="/orders" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors">
                <span class="mr-2">🛒</span>
                {{ t('dashboard.view_orders') }}
            </Link>
        </div>

        <!-- Orders List -->
        <div v-else class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700 p-6">
            <div class="space-y-4 max-h-96 overflow-y-auto">
                <div v-for="order in recentOrders" :key="order.id"
                     class="group relative">
                    <div class="flex items-start space-x-3 p-4 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                        <!-- Order Icon -->
                        <div class="w-10 h-10 bg-gradient-to-br from-blue-100 to-emerald-100 dark:from-blue-900 dark:to-emerald-900 rounded-xl flex items-center justify-center flex-shrink-0 group-hover:scale-110 transition-transform">
                            <span class="text-lg">📦</span>
                        </div>

                        <!-- Order Details -->
                        <div class="flex-1 min-w-0">
                            <!-- Order ID and Customer -->
                            <div class="flex items-center justify-between mb-2">
                                <div>
                                    <p class="text-sm font-medium text-gray-900 dark:text-white">{{ order.id }}</p>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">{{ order.customer }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-500">{{ order.customerEmail }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="font-semibold text-gray-900 dark:text-white">${{ order.total }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ order.items }} {{ t('dashboard.items') }}</p>
                                </div>
                            </div>

                            <!-- Order Meta -->
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-3 text-xs text-gray-500 dark:text-gray-400">
                                    <span>{{ formatDate(order.date) }}</span>
                                    <span>•</span>
                                    <span class="flex items-center">
                                        {{ getPaymentIcon(order.paymentMethod) }}
                                        <span class="ml-1">{{ order.paymentMethod.replace('_', ' ') }}</span>
                                    </span>
                                </div>
                                <span :class="`px-2 py-1 text-xs font-medium rounded-full ${getStatusColor(order.status)}`">
                                    {{ t(`dashboard.status_${order.status}`) }}
                                </span>
                            </div>

                            <!-- Shipping Address (truncated) -->
                            <div class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                                📍 {{ order.shippingAddress.length > 30 ? order.shippingAddress.substring(0, 30) + '...' : order.shippingAddress }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- View All Link -->
            <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                <Link href="/orders" class="flex items-center justify-center w-full py-2 text-sm font-medium text-blue-600 hover:text-blue-700 bg-blue-50 dark:bg-blue-900/20 rounded-lg hover:bg-blue-100 dark:hover:bg-blue-900/30 transition-colors">
                    {{ t('dashboard.view_all_orders') }}
                    <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </Link>
            </div>
        </div>

        <!-- Order Detail Drawer -->
        <div v-if="isDrawerOpen" class="fixed inset-0 z-50 overflow-hidden">
            <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" @click="closeDrawer"/>
            <div class="absolute inset-y-0 right-0 w-full max-w-md bg-white dark:bg-gray-900 shadow-2xl flex flex-col transform transition-transform duration-300"
                 :class="isDrawerOpen ? 'translate-x-0' : 'translate-x-full'">
                <!-- Drawer Header -->
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ selectedOrder?.id }}</h3>
                        <p class="text-sm text-gray-500">{{ formatDate(selectedOrder?.date) }}</p>
                    </div>
                    <button @click="closeDrawer" class="p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <!-- Drawer Content -->
                <div v-if="selectedOrder" class="flex-1 overflow-y-auto p-6 space-y-6">
                    <!-- Customer Info -->
                    <div class="flex items-center space-x-3">
                        <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-emerald-600 rounded-full flex items-center justify-center text-white text-xl">
                            {{ selectedOrder.customer.charAt(0) }}
                        </div>
                        <div>
                            <p class="font-medium text-gray-900 dark:text-white">{{ selectedOrder.customer }}</p>
                            <p class="text-sm text-gray-500">{{ selectedOrder.customerEmail }}</p>
                        </div>
                    </div>

                    <!-- Status Update -->
                    <div>
                        <p class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ t('dashboard.update_status') }}</p>
                        <div class="flex flex-wrap gap-2">
                            <button v-for="status in availableStatuses" :key="status.value"
                                    @click="updateStatus(selectedOrder, status.value)"
                                    :disabled="updatingStatus === selectedOrder.id || selectedOrder.status === status.value"
                                    :class="`px-3 py-2 rounded-lg text-sm font-medium border transition-all ${
                                        selectedOrder.status === status.value
                                            ? 'bg-blue-100 text-blue-800 border-blue-300 dark:bg-blue-900 dark:text-blue-200'
                                            : 'bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700'
                                    } ${updatingStatus === selectedOrder.id ? 'opacity-50 cursor-not-allowed' : ''}`">
                                <span class="mr-1">{{ status.icon }}</span>
                                {{ status.label }}
                            </button>
                        </div>
                    </div>

                    <!-- Order Summary -->
                    <div class="bg-gray-50 dark:bg-gray-800 rounded-xl p-4 space-y-3">
                        <div class="flex justify-between">
                            <span class="text-gray-600 dark:text-gray-400">{{ t('dashboard.subtotal') }}</span>
                            <span class="font-medium">${{ selectedOrder.total }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600 dark:text-gray-400">{{ t('dashboard.items') }}</span>
                            <span class="font-medium">{{ selectedOrder.items }}</span>
                        </div>
                        <div class="flex justify-between border-t border-gray-200 dark:border-gray-700 pt-3">
                            <span class="font-semibold text-gray-900 dark:text-white">{{ t('dashboard.total') }}</span>
                            <span class="font-bold text-lg text-gray-900 dark:text-white">${{ selectedOrder.total }}</span>
                        </div>
                    </div>

                    <!-- Payment Method -->
                    <div class="flex items-center space-x-2">
                        <span>{{ getPaymentIcon(selectedOrder.paymentMethod) }}</span>
                        <span class="text-gray-700 dark:text-gray-300 capitalize">{{ selectedOrder.paymentMethod.replace('_', ' ') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
