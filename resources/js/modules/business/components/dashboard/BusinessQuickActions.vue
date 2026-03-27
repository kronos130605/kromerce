<script setup>
import { computed } from 'vue';
import { useI18n } from 'vue-i18n';
import { Link } from '@inertiajs/vue3';

const { t } = useI18n();

// Organized action sections
const createActions = computed(() => [
    {
        id: 'create-product',
        title: t('dashboard.add_product'),
        description: t('dashboard.create_new_product'),
        icon: '➕',
        color: 'from-green-500 to-emerald-600',
        href: '/products?modal=create'
    },
    {
        id: 'create-order',
        title: t('dashboard.add_order'),
        description: t('dashboard.create_new_order'),
        icon: '🛒',
        color: 'from-blue-500 to-indigo-600',
        href: '/orders?modal=create'
    },
    {
        id: 'create-discount',
        title: t('dashboard.add_discount'),
        description: t('dashboard.create_promotion'),
        icon: '🏷️',
        color: 'from-purple-500 to-pink-600',
        href: '/marketing/discounts/create'
    }
]);

const manageActions = computed(() => [
    {
        id: 'manage-products',
        title: t('dashboard.product_catalog'),
        description: t('dashboard.manage_inventory'),
        icon: '📦',
        color: 'from-blue-500 to-emerald-600',
        href: '/products',
        badge: { type: 'success', text: '156 ' + t('dashboard.items') }
    },
    {
        id: 'manage-orders',
        title: t('dashboard.order_management'),
        description: t('dashboard.process_orders'),
        icon: '📋',
        color: 'from-emerald-500 to-teal-600',
        href: '/orders',
        badge: { type: 'warning', text: '23 ' + t('dashboard.pending') }
    },
    {
        id: 'manage-customers',
        title: t('dashboard.customers'),
        description: t('dashboard.manage_customers'),
        icon: '👥',
        color: 'from-amber-500 to-orange-600',
        href: '/customers',
        badge: { type: 'info', text: '1,289 ' + t('dashboard.total') }
    }
]);

const analyzeActions = computed(() => [
    {
        id: 'analytics-sales',
        title: t('dashboard.sales_analytics'),
        description: t('dashboard.view_sales_reports'),
        icon: '📊',
        color: 'from-indigo-500 to-purple-600',
        href: '/analytics/sales'
    },
    {
        id: 'analytics-products',
        title: t('dashboard.product_insights'),
        description: t('dashboard.top_performers'),
        icon: '🏆',
        color: 'from-rose-500 to-pink-600',
        href: '/analytics/products'
    },
    {
        id: 'analytics-marketing',
        title: t('dashboard.marketing_performance'),
        description: t('dashboard.campaign_results'),
        icon: '📈',
        color: 'from-cyan-500 to-blue-600',
        href: '/analytics/marketing'
    }
]);

const getBadgeColor = (type) => {
    const colors = {
        success: 'bg-green-100 dark:bg-green-900 text-green-700 dark:text-green-300',
        warning: 'bg-yellow-100 dark:bg-yellow-900 text-yellow-700 dark:text-yellow-300',
        info: 'bg-blue-100 dark:bg-blue-900 text-blue-700 dark:text-blue-300',
        danger: 'bg-red-100 dark:bg-red-900 text-red-700 dark:text-red-300'
    };
    return colors[type] || 'bg-gray-100 dark:bg-gray-900 text-gray-700 dark:text-gray-300';
};
</script>

<template>
    <div class="space-y-8 mt-8">
        <!-- Create Section -->
        <div>
            <div class="flex items-center space-x-2 mb-4">
                <div class="w-8 h-8 bg-gradient-to-br from-green-500 to-emerald-600 rounded-lg flex items-center justify-center">
                    <span class="text-white text-sm">➕</span>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ t('dashboard.create_new') }}</h3>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <Link v-for="action in createActions" :key="action.id"
                   :href="action.href"
                   class="group relative overflow-hidden rounded-xl border border-gray-200 dark:border-gray-700 p-5 hover:border-gray-300 dark:hover:border-gray-600 hover:shadow-lg transition-all duration-300">
                    <div :class="`absolute inset-0 bg-gradient-to-br ${action.color} opacity-0 group-hover:opacity-5 transition-opacity`"></div>
                    <div class="relative flex items-center space-x-3">
                        <div :class="`w-10 h-10 bg-gradient-to-br ${action.color} rounded-lg flex items-center justify-center shadow-md`">
                            <span class="text-white text-lg">{{ action.icon }}</span>
                        </div>
                        <div>
                            <h4 class="font-medium text-gray-900 dark:text-white group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">
                                {{ action.title }}
                            </h4>
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ action.description }}</p>
                        </div>
                    </div>
                </Link>
            </div>
        </div>

        <!-- Manage Section -->
        <div>
            <div class="flex items-center space-x-2 mb-4">
                <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-emerald-600 rounded-lg flex items-center justify-center">
                    <span class="text-white text-sm">📦</span>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ t('dashboard.manage') }}</h3>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <Link v-for="action in manageActions" :key="action.id"
                   :href="action.href"
                   class="group relative overflow-hidden rounded-xl border border-gray-200 dark:border-gray-700 p-5 hover:border-gray-300 dark:hover:border-gray-600 hover:shadow-lg transition-all duration-300">
                    <div :class="`absolute inset-0 bg-gradient-to-br ${action.color} opacity-0 group-hover:opacity-5 transition-opacity`"></div>
                    <div class="relative flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div :class="`w-10 h-10 bg-gradient-to-br ${action.color} rounded-lg flex items-center justify-center shadow-md`">
                                <span class="text-white text-lg">{{ action.icon }}</span>
                            </div>
                            <div>
                                <h4 class="font-medium text-gray-900 dark:text-white group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">
                                    {{ action.title }}
                                </h4>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ action.description }}</p>
                            </div>
                        </div>
                        <span v-if="action.badge" :class="`px-2 py-1 text-xs font-medium rounded-full ${getBadgeColor(action.badge.type)}`">
                            {{ action.badge.text }}
                        </span>
                    </div>
                </Link>
            </div>
        </div>

        <!-- Analyze Section -->
        <div>
            <div class="flex items-center space-x-2 mb-4">
                <div class="w-8 h-8 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-lg flex items-center justify-center">
                    <span class="text-white text-sm">📊</span>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ t('dashboard.analyze') }}</h3>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <Link v-for="action in analyzeActions" :key="action.id"
                   :href="action.href"
                   class="group relative overflow-hidden rounded-xl border border-gray-200 dark:border-gray-700 p-5 hover:border-gray-300 dark:hover:border-gray-600 hover:shadow-lg transition-all duration-300">
                    <div :class="`absolute inset-0 bg-gradient-to-br ${action.color} opacity-0 group-hover:opacity-5 transition-opacity`"></div>
                    <div class="relative flex items-center space-x-3">
                        <div :class="`w-10 h-10 bg-gradient-to-br ${action.color} rounded-lg flex items-center justify-center shadow-md`">
                            <span class="text-white text-lg">{{ action.icon }}</span>
                        </div>
                        <div>
                            <h4 class="font-medium text-gray-900 dark:text-white group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">
                                {{ action.title }}
                            </h4>
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ action.description }}</p>
                        </div>
                    </div>
                </Link>
            </div>
        </div>
    </div>
</template>
