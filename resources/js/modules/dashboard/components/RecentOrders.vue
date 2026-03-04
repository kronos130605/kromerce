<script setup>
import { ref, computed } from 'vue';
import { useI18n } from 'vue-i18n';

const { t } = useI18n();

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

const formatDate = (dateString) => {
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
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
        
        <!-- Orders List -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700 p-6">
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
                <a href="#" class="flex items-center justify-center w-full py-2 text-sm font-medium text-blue-600 hover:text-blue-700 bg-blue-50 dark:bg-blue-900/20 rounded-lg hover:bg-blue-100 dark:hover:bg-blue-900/30 transition-colors">
                    {{ t('dashboard.view_all_orders') }}
                    <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </a>
            </div>
        </div>
    </div>
</template>
