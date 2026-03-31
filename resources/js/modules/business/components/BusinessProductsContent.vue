<script setup>
import { Link } from '@inertiajs/vue3';
import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { useTranslations } from '@/composables/useTranslations';

// Import products component
import ProductsMainContent from '@/modules/products/Products/Content.vue';
import ProductsCreate from '@/modules/products/Products/Create.vue';
import ProductsEdit from '@/modules/products/Products/Edit.vue';
import ProductsShow from '@/modules/products/Products/Show.vue';

const { t } = useTranslations();
const page = usePage();
const props = defineProps({
    products: Object,
    categories: Array,
    filters: Object,
    statistics: Object,
});

// Dashboard data from backend
const dashboardData = computed(() => page.props.dashboard_data || {});

const productsView = computed(() => page.props.productsView || 'index');
const product = computed(() => page.props.product || null);
</script>

<template>
    <div class="space-y-8">
        <div v-if="productsView === 'index'" class="space-y-8">
            <!-- Products Header -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700 p-6">
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h3 class="text-2xl font-bold text-gray-900 dark:text-white">{{ t('dashboard_nav.nav_products') }}</h3>
                        <p class="text-gray-600 dark:text-gray-400 mt-1">{{ t('products.overview') }}</p>
                    </div>
                    <Link
                        href="/products/create"
                        class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors"
                    >
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        {{ t('products.add_new') }}
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
                                <p class="text-sm text-gray-600">{{ t('products.statistics.total_products') }}</p>
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
                                <p class="text-sm text-gray-600">{{ t('products.statistics.active_products') }}</p>
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
                                <p class="text-sm text-gray-600">{{ t('products.statistics.low_stock') }}</p>
                                <p class="text-2xl font-bold text-gray-900">{{ dashboardData.lowStock || 0 }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-red-50 rounded-lg p-4">
                        <div class="flex items-center">
                            <div class="p-2 bg-red-100 rounded-lg">
                                <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm text-gray-600">{{ t('products.statistics.out_of_stock') }}</p>
                                <p class="text-2xl font-bold text-gray-900">{{ dashboardData.outOfStock || 0 }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex flex-wrap gap-4">
                    <button class="inline-flex items-center px-6 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                        </svg>
                        {{ t('products.import_products') }}
                    </button>

                    <button class="inline-flex items-center px-6 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        {{ t('products.export_products') }}
                    </button>
                </div>
            </div>

            <ProductsMainContent
                :products="products"
                :categories="categories"
                :filters="filters"
                :statistics="statistics"
            />
        </div>

        <div v-else>
            <ProductsCreate v-if="productsView === 'create'" :categories="categories" />
            <ProductsEdit v-else-if="productsView === 'edit'" :product="product" :categories="categories" />
            <ProductsShow v-else-if="productsView === 'show'" :product="product" />
        </div>
    </div>
</template>
