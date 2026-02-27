<script setup>
import { computed, ref } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';

// Import business dashboard components
import BusinessWelcome from '@/components/Dashboard/BusinessWelcome.vue';
import BusinessStats from '@/components/Dashboard/BusinessStats.vue';
import BusinessQuickActions from '@/components/Dashboard/BusinessQuickActions.vue';
import TopProducts from '@/components/Dashboard/TopProducts.vue';
import RecentOrders from '@/components/Dashboard/RecentOrders.vue';

const page = usePage();
const user = computed(() => page.props.auth.user);
const currentTenant = computed(() => page.props.current_tenant);
const { t } = useI18n();

// State management
const activeTab = ref('overview');
const searchQuery = ref('');
const selectedPeriod = ref('30days');

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

    <AuthenticatedLayout>
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
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700 p-8">
                        <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">{{ t('dashboard.nav_products') }}</h3>
                        <div class="text-center py-12">
                            <div class="text-6xl mb-4">📦</div>
                            <p class="text-gray-600 dark:text-gray-400">{{ t('dashboard.products_coming_soon') }}</p>
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
    </AuthenticatedLayout>
</template>
