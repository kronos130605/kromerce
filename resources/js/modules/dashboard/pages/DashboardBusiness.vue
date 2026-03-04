<script setup>
import { computed, ref } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import CustomerLayout from '@/Layouts/CustomerLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

// Import business dashboard components
import BusinessWelcome from '@/modules/dashboard/components/BusinessWelcome.vue';
import BusinessStats from '@/modules/dashboard/components/BusinessStats.vue';
import BusinessQuickActions from '@/modules/dashboard/components/BusinessQuickActions.vue';
import TopProducts from '@/modules/dashboard/components/TopProducts.vue';
import RecentOrders from '@/modules/dashboard/components/RecentOrders.vue';

// Import new dashboard components
import BusinessCurrencyStatus from '@/modules/dashboard/components/BusinessCurrencyStatus.vue';
import BusinessAnalytics from '@/modules/dashboard/components/BusinessAnalytics.vue';

const page = usePage();
const user = computed(() => page.props.auth.user);
const currentTenant = computed(() => page.props.current_tenant);
const { t } = useI18n();

// Dashboard data from backend
const dashboardData = computed(() => page.props.dashboard_data || {});

// State management
const activeTab = ref('overview');
const searchQuery = ref('');
const selectedPeriod = ref('30days');

// Helper functions
const formatPrice = (price, currency) => {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: currency,
    }).format(price);
};

// Tab options
const tabOptions = computed(() => [
    { key: 'overview', label: t('dashboard.tab_overview') },
    { key: 'products', label: t('dashboard.tab_products') },
    { key: 'orders', label: t('dashboard.tab_orders') },
    { key: 'analytics', label: t('dashboard.nav_analytics') },
    { key: 'marketing', label: t('dashboard.marketing_tools') },
    { key: 'settings', label: t('dashboard.nav_settings') }
]);

// Period options for analytics
const periodOptions = computed(() => [
    { value: '7days', label: t('dashboard.last_7_days') },
    { value: '30days', label: t('dashboard.last_30_days') },
    { value: '90days', label: t('dashboard.last_90_days') },
    { value: '1year', label: t('dashboard.last_year') }
]);
</script>

<template>
    <Head title="Business Dashboard" />

    <CustomerLayout>
        <template #header>
            <div class="relative">
                <!-- Background gradient decoration -->
                <div class="absolute inset-0 bg-gradient-to-r from-blue-50 via-emerald-50 to-blue-50 -z-10 rounded-2xl"></div>

                <div class="relative px-8 py-6">
                    <div class="flex justify-between items-start">
                        <div class="space-y-2">
                            <div class="flex items-center space-x-3">
                                <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
                                <h1 class="text-3xl font-bold bg-gradient-to-r from-gray-900 to-gray-700 dark:from-white dark:to-gray-200 bg-clip-text text-transparent">
                                    {{ t('dashboard.nav_dashboard') }}
                                </h1>
                            </div>
                            <p class="text-gray-600 dark:text-gray-300 text-lg">{{ t('dashboard.business_subtitle') }}</p>
                            <div class="flex items-center space-x-4 text-sm">
                                <span class="text-gray-500 dark:text-gray-400">{{ t('dashboard.last_30_days') }}</span>
                                <span class="px-2 py-1 bg-green-100 dark:bg-green-900 text-green-700 dark:text-green-300 rounded-full font-medium">
                                    ↑ 23.5% {{ t('dashboard.growth') }}
                                </span>
                            </div>
                        </div>

                        <div v-if="currentTenant" class="text-right space-y-2">
                            <div class="inline-flex items-center px-4 py-2 bg-white/80 backdrop-blur-sm rounded-xl shadow-sm border border-white/20">
                                <div class="w-2 h-2 bg-blue-500 rounded-full mr-2"></div>
                                <span class="font-medium text-gray-900">{{ currentTenant.name }}</span>
                            </div>
                            <p class="text-sm text-gray-500 dark:text-gray-400 font-mono">{{ currentTenant.slug }}.kromerce.test</p>
                        </div>
                    </div>
                </div>
            </div>
        </template>

        <div class="px-4 sm:px-6 lg:px-8 py-6">
            <div class="mx-auto max-w-7xl space-y-8">

                <!-- Business Welcome Section -->
                <BusinessWelcome />

                <!-- Business Stats -->
                <BusinessStats />

                <!-- Enhanced navigation tabs -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md border border-gray-200 dark:border-gray-700 p-2 mb-8 mt-4">
                    <div class="flex space-x-2">
                        <button v-for="tab in tabOptions" :key="tab.key"
                                @click="activeTab = tab.key"
                                :class="`flex-1 px-4 py-2 rounded-lg font-medium transition-all ${
                                    activeTab === tab.key
                                        ? 'bg-blue-500 text-white shadow-lg'
                                        : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700'
                                }`">
                            {{ tab.label }}
                        </button>
                    </div>
                </div>

                <!-- Overview Tab Content -->
                <div v-if="activeTab === 'overview'" class="space-y-8">
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

                <!-- Products Tab Content -->
                <div v-if="activeTab === 'products'" class="space-y-8">
                    <!-- Products Header -->
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700 p-6">
                        <div class="flex justify-between items-center mb-6">
                            <div>
                                <h3 class="text-2xl font-bold text-gray-900 dark:text-white">{{ t('dashboard.nav_products') }}</h3>
                                <p class="text-gray-600 dark:text-gray-400 mt-1">Manage your product catalog</p>
                            </div>
                            <Link
                                href="/test-products-create"
                                class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors"
                            >
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                </svg>
                                Add Product
                            </Link>
                        </div>

                        <!-- Quick Stats -->
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                            <div class="bg-blue-50 rounded-lg p-4">
                                <div class="flex items-center">
                                    <div class="p-2 bg-blue-100 rounded-lg">
                                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 6H9a2 2 0 00-2 2v8a2 2 0 002 2h11a2 2 0 002-2V8a2 2 0 00-2-2z"></path>
                                        </svg>
                                    </div>
                                    <div class="ml-4">
                                        <p class="text-sm text-gray-600">Total Products</p>
                                        <p class="text-2xl font-bold text-gray-900">{{ dashboardData.totalProducts || 0 }}</p>
                                    </div>
                                </div>
                            </div>

                            <div class="bg-green-50 rounded-lg p-4">
                                <div class="flex items-center">
                                    <div class="p-2 bg-green-100 rounded-lg">
                                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    </div>
                                    <div class="ml-4">
                                        <p class="text-sm text-gray-600">Active</p>
                                        <p class="text-2xl font-bold text-gray-900">{{ dashboardData.activeProducts || 0 }}</p>
                                    </div>
                                </div>
                            </div>

                            <div class="bg-yellow-50 rounded-lg p-4">
                                <div class="flex items-center">
                                    <div class="p-2 bg-yellow-100 rounded-lg">
                                        <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    </div>
                                    <div class="ml-4">
                                        <p class="text-sm text-gray-600">Low Stock</p>
                                        <p class="text-2xl font-bold text-gray-900">{{ dashboardData.lowStockProducts || 0 }}</p>
                                    </div>
                                </div>
                            </div>

                            <div class="bg-red-50 rounded-lg p-4">
                                <div class="flex items-center">
                                    <div class="p-2 bg-red-100 rounded-lg">
                                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                        </svg>
                                    </div>
                                    <div class="ml-4">
                                        <p class="text-sm text-gray-600">On Sale</p>
                                        <p class="text-2xl font-bold text-gray-900">{{ dashboardData.onSaleProducts || 0 }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex flex-wrap gap-4">
                            <Link
                                href="/test-products"
                                class="inline-flex items-center px-6 py-3 bg-gray-900 text-white rounded-lg hover:bg-gray-800 transition-colors"
                            >
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                </svg>
                                Manage Products
                            </Link>

                            <Link
                                href="/test-products-create"
                                class="inline-flex items-center px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors"
                            >
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                </svg>
                                Add New Product
                            </Link>

                            <button class="inline-flex items-center px-6 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                </svg>
                                Import Products
                            </button>

                            <button class="inline-flex items-center px-6 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                Export Products
                            </button>
                        </div>
                    </div>

                    <!-- Recent Products Preview -->
                    <div v-if="dashboardData.recentProducts && dashboardData.recentProducts.length > 0" class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700 p-6">
                        <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Recent Products</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            <div
                                v-for="product in dashboardData.recentProducts.slice(0, 6)"
                                :key="product.id"
                                class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow"
                            >
                                <div class="flex items-center space-x-3">
                                    <div class="w-12 h-12 bg-gray-100 rounded-lg overflow-hidden flex-shrink-0">
                                        <img
                                            v-if="product.primary_image"
                                            :src="product.primary_image.url"
                                            :alt="product.name"
                                            class="w-full h-full object-cover"
                                        >
                                        <div v-else class="w-full h-full flex items-center justify-center">
                                            <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4 4m0 0l-4-4m4 4l4 4m-2-2h-8m-2 2h8"></path>
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <h5 class="font-medium text-gray-900 truncate">{{ product.name }}</h5>
                                        <p class="text-sm text-gray-500">SKU: {{ product.sku }}</p>
                                        <p class="text-sm font-medium text-gray-900">{{ formatPrice(product.base_price, product.base_currency) }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4 text-center">
                            <Link
                                href="/test-products"
                                class="text-blue-600 hover:text-blue-800 font-medium"
                            >
                                View All Products →
                            </Link>
                        </div>
                    </div>
                </div>

                <!-- Orders Tab Content -->
                <div v-if="activeTab === 'orders'" class="space-y-8">
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700 p-8">
                        <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">{{ t('dashboard.nav_orders') }}</h3>
                        <div class="text-center py-12">
                            <div class="text-6xl mb-4">🛒</div>
                            <p class="text-gray-600 dark:text-gray-400">{{ t('dashboard.orders_coming_soon') }}</p>
                        </div>
                    </div>
                </div>

                <!-- Analytics Tab Content -->
                <div v-if="activeTab === 'analytics'" class="space-y-8">
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700 p-8">
                        <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">{{ t('dashboard.nav_analytics') }}</h3>
                        <div class="text-center py-12">
                            <div class="text-6xl mb-4">📊</div>
                            <p class="text-gray-600 dark:text-gray-400">{{ t('dashboard.analytics_coming_soon') }}</p>
                        </div>
                    </div>
                </div>

                <!-- Marketing Tab Content -->
                <div v-if="activeTab === 'marketing'" class="space-y-8">
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700 p-8">
                        <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">{{ t('dashboard.marketing_tools') }}</h3>
                        <div class="text-center py-12">
                            <div class="text-6xl mb-4">📈</div>
                            <p class="text-gray-600 dark:text-gray-400">{{ t('dashboard.marketing_coming_soon') }}</p>
                        </div>
                    </div>
                </div>

                <!-- Settings Tab Content -->
                <div v-if="activeTab === 'settings'" class="space-y-8">
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700 p-8">
                        <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">{{ t('dashboard.nav_settings') }}</h3>
                        <div class="text-center py-12">
                            <div class="text-6xl mb-4">⚙️</div>
                            <p class="text-gray-600 dark:text-gray-400">{{ t('dashboard.settings_coming_soon') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </CustomerLayout>
</template>
