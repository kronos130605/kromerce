<script setup>
import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';

const page = usePage();
const user = computed(() => page.props.auth.user);
const currentTenant = computed(() => page.props.current_tenant);
const { t } = useI18n();

// Business stats
const businessStats = computed(() => ({
    revenue: 125430,
    orders: 3421,
    products: 156,
    customers: 1289,
    growth: 23.5,
    conversionRate: 3.2,
    avgOrderValue: 36.67
}));
</script>

<template>
    <div class="bg-gradient-to-br from-blue-600 to-emerald-600 dark:from-blue-800 dark:to-emerald-800 rounded-2xl p-8 text-white shadow-xl">
        <div class="flex items-center justify-between">
            <div class="flex-1">
                <div class="flex items-center space-x-3 mb-4">
                    <div class="w-3 h-3 bg-green-400 rounded-full animate-pulse"></div>
                    <h1 class="text-4xl font-bold">
                        {{ t('dashboard.business_welcome') }}, {{ user.name }}! 🚀
                    </h1>
                </div>
                <p class="text-blue-100 text-lg mb-6">
                    {{ t('dashboard.business_subtitle') }}
                </p>
                
                <!-- Business Info -->
                <div v-if="currentTenant" class="flex items-center space-x-4">
                    <div class="bg-white/20 backdrop-blur-sm rounded-xl px-4 py-2">
                        <span class="text-blue-100 text-sm">{{ t('dashboard.tenant') }}:</span>
                        <span class="font-semibold ml-2">{{ currentTenant.name }}</span>
                    </div>
                    <div class="bg-white/20 backdrop-blur-sm rounded-xl px-4 py-2">
                        <span class="text-blue-100 text-sm">{{ t('dashboard.store') }}:</span>
                        <span class="font-mono ml-2">{{ currentTenant.slug }}.kromerce.test</span>
                    </div>
                </div>
            </div>
            
            <!-- Key Metrics -->
            <div class="text-right">
                <div class="bg-white/20 backdrop-blur-sm rounded-xl p-6">
                    <div class="text-sm text-blue-100 mb-2">{{ t('dashboard.monthly_revenue') }}</div>
                    <div class="text-3xl font-bold">${{ businessStats.revenue.toLocaleString() }}</div>
                    <div class="text-xs text-emerald-200 mt-2 flex items-center justify-end space-x-1">
                        <span>↑</span>
                        <span>{{ businessStats.growth }}% {{ t('dashboard.growth') }}</span>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Quick Stats -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-6">
            <div class="bg-white/10 backdrop-blur-sm rounded-lg p-4 text-center border border-white/20">
                <div class="text-2xl font-bold">{{ businessStats.orders.toLocaleString() }}</div>
                <div class="text-xs text-blue-100 dark:text-blue-300 mt-1">{{ t('dashboard.orders') }}</div>
            </div>
            <div class="bg-white/10 backdrop-blur-sm rounded-lg p-4 text-center border border-white/20">
                <div class="text-2xl font-bold">{{ businessStats.products }}</div>
                <div class="text-xs text-blue-100 dark:text-blue-300 mt-1">{{ t('dashboard.products') }}</div>
            </div>
            <div class="bg-white/10 backdrop-blur-sm rounded-lg p-4 text-center border border-white/20">
                <div class="text-2xl font-bold">{{ businessStats.customers.toLocaleString() }}</div>
                <div class="text-xs text-blue-100 dark:text-blue-300 mt-1">{{ t('dashboard.customers') }}</div>
            </div>
            <div class="bg-white/10 backdrop-blur-sm rounded-lg p-4 text-center border border-white/20">
                <div class="text-2xl font-bold">{{ businessStats.conversionRate }}%</div>
                <div class="text-xs text-blue-100 dark:text-blue-300 mt-1">{{ t('dashboard.conversion_rate') }}</div>
            </div>
        </div>
    </div>
</template>
