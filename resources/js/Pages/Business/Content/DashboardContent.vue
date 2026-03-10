<script setup>
import { computed, ref } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import { Link } from '@inertiajs/vue3';

// Import business dashboard components
import BusinessWelcome from '@/modules/dashboard/components/BusinessWelcome.vue';
import BusinessStats from '@/modules/dashboard/components/BusinessStats.vue';
import BusinessQuickActions from '@/modules/dashboard/components/BusinessQuickActions.vue';
import TopProducts from '@/modules/dashboard/components/TopProducts.vue';
import RecentOrders from '@/modules/dashboard/components/RecentOrders.vue';
import BusinessCurrencyStatus from '@/modules/dashboard/components/BusinessCurrencyStatus.vue';
import BusinessAnalytics from '@/modules/dashboard/components/BusinessAnalytics.vue';

const page = usePage();
const { t } = useI18n();

// Dashboard data from backend
const dashboardData = computed(() => page.props.dashboard_data || {});

// Tab options
const tabOptions = computed(() => [
    { key: 'overview', label: t('dashboard.tab_overview'), href: '/dashboard' },
    { key: 'products', label: t('dashboard.tab_products'), href: '/products' },
    { key: 'orders', label: t('dashboard.tab_orders'), href: '/orders' },
    { key: 'analytics', label: t('dashboard.nav_analytics'), href: '/analytics' },
    { key: 'marketing', label: t('dashboard.marketing_tools'), href: '/marketing' },
    { key: 'settings', label: t('dashboard.nav_settings'), href: '/settings' }
]);

// Helper functions
const formatPrice = (price, currency) => {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: currency,
    }).format(price);
};
</script>

<template>
    <div class="space-y-8">
        <!-- Business Welcome Section -->
        <BusinessWelcome />

        <!-- Business Stats -->
        <BusinessStats />

        <!-- Enhanced navigation tabs -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md border border-gray-200 dark:border-gray-700 p-2 mb-8 mt-4">
            <div class="flex space-x-2">
                <a v-for="tab in tabOptions" :key="tab.key"
                   :href="tab.href"
                   :class="`flex-1 px-4 py-2 rounded-lg font-medium transition-all text-center ${
                     page.props.activeTab === tab.key
                       ? 'bg-blue-500 text-white shadow-lg'
                       : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700'
                   }`">
                  {{ tab.label }}
                </a>
            </div>
        </div>

        <!-- Business Quick Actions -->
        <BusinessQuickActions />

        <!-- Currency Status -->
        <BusinessCurrencyStatus :currency-status="dashboardData.currencyStatus || {}" />

        <!-- Analytics Overview -->
        <BusinessAnalytics :chart-data="dashboardData.chartData || {}" />

        <!-- Main Content Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Top Products -->
            <div class="lg:col-span-2">
                <TopProducts />
            </div>

            <!-- Recent Orders -->
            <div class="lg:col-span-1">
                <RecentOrders />
            </div>
        </div>
    </div>
</template>
