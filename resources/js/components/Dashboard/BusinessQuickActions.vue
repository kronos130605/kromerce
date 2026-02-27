<script setup>
import { computed } from 'vue';
import { useI18n } from 'vue-i18n';

const { t } = useI18n();

const quickActions = computed(() => [
    {
        id: 1,
        title: t('dashboard.product_catalog'),
        description: t('dashboard.manage_inventory'),
        icon: '📦',
        color: 'from-blue-500 to-emerald-600',
        href: '#products',
        badge: { type: 'success', text: '156 ' + t('dashboard.items') }
    },
    {
        id: 2,
        title: t('dashboard.order_management'),
        description: t('dashboard.process_orders'),
        icon: '🛒',
        color: 'from-emerald-500 to-teal-600',
        href: '#orders',
        badge: { type: 'warning', text: '23 ' + t('dashboard.pending') }
    },
    {
        id: 3,
        title: t('dashboard.marketing_tools'),
        description: t('dashboard.create_campaigns'),
        icon: '📈',
        color: 'from-indigo-500 to-purple-600',
        href: '#marketing',
        badge: { type: 'info', text: '3 ' + t('dashboard.active') }
    },
    {
        id: 4,
        title: t('dashboard.business_insights'),
        description: t('dashboard.view_analytics'),
        icon: '💡',
        color: 'from-amber-500 to-orange-600',
        href: '#insights',
        badge: null
    },
    {
        id: 5,
        title: t('dashboard.customer_service'),
        description: t('dashboard.manage_support'),
        icon: '💬',
        color: 'from-rose-500 to-pink-600',
        href: '#support',
        badge: { type: 'danger', text: '8 ' + t('dashboard.urgent') }
    },
    {
        id: 6,
        title: t('dashboard.financial_overview'),
        description: t('dashboard.track_revenue'),
        icon: '💰',
        color: 'from-green-500 to-emerald-600',
        href: '#finance',
        badge: null
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
    <div class="space-y-6 mt-8">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-2xl font-bold text-gray-900 dark:text-white">{{ t('dashboard.quick_actions') }}</h3>
                <p class="text-gray-600 dark:text-gray-300 mt-1">{{ t('dashboard.manage_business_efficiently') }}</p>
            </div>
            <div class="w-2 h-2 bg-blue-500 rounded-full animate-pulse"></div>
        </div>
        
        <!-- Actions Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <a v-for="action in quickActions" :key="action.id"
               :href="action.href"
               class="group relative overflow-hidden rounded-xl border border-gray-200 dark:border-gray-700 p-6 hover:border-gray-300 dark:hover:border-gray-600 hover:shadow-lg transition-all duration-300">
                <!-- Background gradient on hover -->
                <div :class="`absolute inset-0 bg-gradient-to-br ${action.color} opacity-0 group-hover:opacity-5 transition-opacity`"></div>
                
                <div class="relative flex items-start space-x-4">
                    <!-- Icon -->
                    <div :class="`w-12 h-12 bg-gradient-to-br ${action.color} rounded-xl flex items-center justify-center flex-shrink-0 shadow-md`">
                        <span class="text-white text-xl">{{ action.icon }}</span>
                    </div>
                    
                    <!-- Content -->
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between mb-1">
                            <h4 class="text-base font-semibold text-gray-900 dark:text-white group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">
                                {{ action.title }}
                            </h4>
                            <svg class="w-5 h-5 text-gray-400 dark:text-gray-500 group-hover:text-gray-600 dark:group-hover:text-gray-400 group-hover:translate-x-1 transition-all flex-shrink-0" 
                                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">{{ action.description }}</p>
                        
                        <!-- Badge -->
                        <div v-if="action.badge" class="mt-2">
                            <span :class="`inline-flex items-center px-2 py-1 text-xs font-medium rounded-full ${getBadgeColor(action.badge.type)}`">
                                {{ action.badge.text }}
                            </span>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    </div>
</template>
