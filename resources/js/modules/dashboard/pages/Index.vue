<script setup>
import { computed, ref } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import BusinessLayout from '@/Layouts/BusinessLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

// Import business dashboard components
import BusinessWelcome from '@/modules/dashboard/components/BusinessWelcome.vue';
import BusinessStats from '@/modules/dashboard/components/BusinessStats.vue';
import BusinessQuickActions from '@/modules/dashboard/components/BusinessQuickActions.vue';
import TopProducts from '@/modules/dashboard/components/TopProducts.vue';
import RecentOrders from '@/modules/dashboard/components/RecentOrders.vue';
import BusinessCurrencyStatus from '@/modules/dashboard/components/BusinessCurrencyStatus.vue';
import BusinessAnalytics from '@/modules/dashboard/components/BusinessAnalytics.vue';

const page = usePage();
const user = computed(() => page.props.auth.user);
const currentTenant = computed(() => page.props.current_tenant);
const { t } = useI18n();

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
            <div class="bg-white rounded-lg shadow">
                <div class="border-b border-gray-200">
                    <nav class="flex space-x-8 px-6" aria-label="Tabs">
                        <button
                            v-for="tab in tabOptions"
                            :key="tab.key"
                            @click="activeTab = tab.key"
                            class="py-4 px-1 border-b-2 font-medium text-sm transition-colors"
                            :class="activeTab === tab.key
                                ? 'border-blue-500 text-blue-600'
                                : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                        >
                            {{ tab.label }}
                        </button>
                    </nav>
                </div>

                <!-- Tab Content -->
                <div class="p-6">
                    <!-- Overview Tab -->
                    <div v-if="activeTab === 'overview'" class="space-y-6">
                        <BusinessQuickActions />
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                            <TopProducts :products="dashboardData.topProducts || []" />
                            <RecentOrders :orders="dashboardData.recentOrders || []" />
                        </div>
                    </div>

                    <!-- Products Tab -->
                    <div v-if="activeTab === 'products'" class="space-y-6">
                        <div class="flex justify-between items-center">
                            <h3 class="text-lg font-medium text-gray-900">Product Management</h3>
                            <div class="flex space-x-3">
                                <Link
                                    href="/test-products"
                                    class="inline-flex items-center px-4 py-2 bg-gray-900 text-white rounded-lg hover:bg-gray-800 transition-colors"
                                >
                                    Manage Products
                                </Link>
                                <Link
                                    href="/test-products-create"
                                    class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors"
                                >
                                    Add New Product
                                </Link>
                            </div>
                        </div>
                        
                        <div class="bg-gray-50 rounded-lg p-8 text-center">
                            <p class="text-gray-600">Click "Manage Products" to view your product catalog</p>
                        </div>
                    </div>

                    <!-- Other tabs (placeholder) -->
                    <div v-else-if="activeTab !== 'overview' && activeTab !== 'products'" class="text-center py-12">
                        <p class="text-gray-500">{{ tabOptions.find(tab => tab.key === activeTab)?.label }} coming soon...</p>
                    </div>
                </div>
            </div>

            <!-- Currency Status -->
            <BusinessCurrencyStatus 
                :currencies="dashboardData.currencies || []"
            />

            <!-- Analytics Overview -->
            <BusinessAnalytics 
                :data="dashboardData.analytics || {}"
                :period="selectedPeriod"
            />
        </div>
    </BusinessLayout>
</template>
