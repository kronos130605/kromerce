<script setup>
import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';

// Import business dashboard components
import BusinessWelcome from '@/components/dashboard/BusinessWelcome.vue';
import BusinessStats from '@/components/dashboard/BusinessStats.vue';
import BusinessQuickActions from '@/components/dashboard/BusinessQuickActions.vue';
import TopProducts from '@/components/dashboard/TopProducts.vue';
import RecentOrders from '@/components/dashboard/RecentOrders.vue';
import BusinessCurrencyStatus from '@/components/dashboard/BusinessCurrencyStatus.vue';
import AttentionRequired from '@/components/dashboard/AttentionRequired.vue';
import ActivityFeed from '@/components/dashboard/ActivityFeed.vue';
import BusinessAnalytics from '@/components/dashboard/BusinessAnalytics.vue';

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

        <!-- Attention Required -->
        <AttentionRequired :alerts="dashboardData.alerts || []" />

        <!-- Activity Feed -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <ActivityFeed :activities="dashboardData.activities || []" />

            <!-- Currency Status -->
            <div>
                <BusinessCurrencyStatus :currency-status="dashboardData.currencyStatus || {}" />
            </div>
        </div>

        <!-- Analytics Overview -->
        <BusinessAnalytics :chart-data="dashboardData.chartData || {}" class="mt-8" />

        <!-- Main Content Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Top Products -->
            <div class="lg:col-span-2">
                <TopProducts :products="dashboardData.topProducts || []" />
            </div>

            <!-- Recent Orders -->
            <div class="lg:col-span-1">
                <RecentOrders :orders="dashboardData.recentOrders || []" />
            </div>
        </div>
    </div>
</template>
