<script setup>
import { computed, ref } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import BusinessLayout from '@/Layouts/BusinessLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

// Import business dashboard components
import BusinessWelcome from '@/modules/dashboard/components/BusinessWelcome.vue';
import BusinessStats from '@/modules/dashboard/components/BusinessStats.vue';
import BusinessCurrencyStatus from '@/modules/dashboard/components/BusinessCurrencyStatus.vue';
import BusinessTabNavigation from '@/modules/dashboard/components/BusinessTabNavigation.vue';

// Import tab components
import OverviewTab from '@/modules/dashboard/components/tabs/OverviewTab.vue';
import ProductsTab from '@/modules/dashboard/components/tabs/ProductsTab.vue';
import OrdersTab from '@/modules/dashboard/components/tabs/OrdersTab.vue';
import AnalyticsTab from '@/modules/dashboard/components/tabs/AnalyticsTab.vue';
import MarketingTab from '@/modules/dashboard/components/tabs/MarketingTab.vue';
import SettingsTab from '@/modules/dashboard/components/tabs/SettingsTab.vue';
import ComingSoonTab from '@/modules/dashboard/components/tabs/ComingSoonTab.vue';

const page = usePage();
const user = computed(() => page.props.auth.user);
const currentTenant = computed(() => page.props.current_tenant);
const { t } = useI18n();
const isDarkMode = computed(() => page.props.auth.user?.dark_mode || false);

const dashboardData = computed(() => page.props.dashboard_data || {});
const activeTab = ref('overview');
const searchQuery = ref('');
const selectedPeriod = ref('30days');

const formatPrice = (price, currency) => {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: currency,
    }).format(price);
};

const tabOptions = computed(() => [
    { key: 'overview', label: t('dashboard.tab_overview') },
    { key: 'products', label: t('dashboard.tab_products') },
    { key: 'orders', label: t('dashboard.tab_orders') },
    { key: 'analytics', label: t('dashboard.nav_analytics') },
    { key: 'marketing', label: t('dashboard.marketing_tools') },
    { key: 'settings', label: t('dashboard.nav_settings') }
]);
</script>

<template>
    <Head title="Dashboard" />
    
    <BusinessLayout>
        <div class="space-y-6">
            <!-- Welcome Section -->
            <BusinessWelcome 
                :user="user" 
                :tenant="currentTenant" 
            />

            <!-- Stats Overview -->
            <BusinessStats 
                :stats="dashboardData.stats || {}"
                :period="selectedPeriod"
                @period-changed="selectedPeriod = $event"
            />

            <!-- Tab Navigation -->
            <BusinessTabNavigation
                :active-tab="activeTab"
                :tab-options="tabOptions"
                @tab-changed="activeTab = $event"
            >
                <!-- Overview Tab -->
                <OverviewTab v-if="activeTab === 'overview'" :dashboard-data="dashboardData" />

                <!-- Products Tab -->
                <ProductsTab v-if="activeTab === 'products'" />

                <!-- Orders Tab -->
                <OrdersTab v-if="activeTab === 'orders'" :dashboard-data="dashboardData" />

                <!-- Analytics Tab -->
                <AnalyticsTab 
                    v-if="activeTab === 'analytics'" 
                    :dashboard-data="dashboardData" 
                    :selected-period="selectedPeriod" 
                />
                <BusinessCurrencyStatus 
                    v-if="activeTab === 'analytics'"
                    :currencies="dashboardData.currencies || []"
                />

                <!-- Marketing Tab -->
                <MarketingTab v-if="activeTab === 'marketing'" />

                <!-- Settings Tab -->
                <SettingsTab v-if="activeTab === 'settings'" />

                <!-- Fallback for any other tabs -->
                <ComingSoonTab 
                    v-if="!['overview', 'products', 'orders', 'analytics', 'marketing', 'settings'].includes(activeTab)"
                    :tab-label="tabOptions.find(tab => tab.key === activeTab)?.label || 'This feature'"
                />
            </BusinessTabNavigation>
        </div>
    </BusinessLayout>
</template>
