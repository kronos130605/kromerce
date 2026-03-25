<script setup>
import { Link, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';

const { t } = useI18n();
const page = usePage();
const props = defineProps({
    products: Object,
    categories: Array,
    filters: Object,
    statistics: Object,
});

// Search and filters
const searchQuery = ref(props.filters.search || '');
const selectedCategory = ref(props.filters.category_id || '');
const selectedStatus = ref(props.filters.status || '');
const minPrice = ref(props.filters.min_price || '');
const maxPrice = ref(props.filters.max_price || '');
const inStock = ref(props.filters.in_stock || false);

const statsCards = computed(() => [
    {
        title: t('products_management.statistics.total_products'),
        value: props.statistics.total_products || 0,
        icon: 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4',
        color: 'blue'
    },
    {
        title: t('products_management.statistics.active_products'),
        value: props.statistics.active_products || 0,
        icon: 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
        color: 'green'
    },
    {
        title: t('products_management.statistics.low_stock'),
        value: props.statistics.low_stock || 0,
        icon: 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z',
        color: 'yellow'
    },
    {
        title: t('products_management.statistics.out_of_stock'),
        value: props.statistics.out_of_stock || 0,
        icon: 'M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
        color: 'red'
    }
]);

// Build query string for filters
const buildQueryString = () => {
    const params = new URLSearchParams();

    if (searchQuery.value) params.append('search', searchQuery.value);
    if (selectedCategory.value) params.append('category_id', selectedCategory.value);
    if (selectedStatus.value) params.append('status', selectedStatus.value);
    if (minPrice.value) params.append('min_price', minPrice.value);
    if (maxPrice.value) params.append('max_price', maxPrice.value);
    if (inStock.value) params.append('in_stock', '1');

    return params.toString();
};

// Apply filters
const applyFilters = () => {
    const queryString = buildQueryString();
    router.visit(queryString ? `/products?${queryString}` : '/products');
};

// Clear filters
const clearFilters = () => {
    searchQuery.value = '';
    selectedCategory.value = '';
    selectedStatus.value = '';
    minPrice.value = '';
    maxPrice.value = '';
    inStock.value = false;

    router.visit('/products');
};

// Check if product is low stock
const isLowStock = (product) => {
    return product.manage_stock && product.stock_quantity <= product.low_stock_threshold;
};

// Check if product is out of stock
const isOutOfStock = (product) => {
    return product.manage_stock && product.stock_quantity === 0;
};
</script>

<template>
    <!-- Statistics Cards -->
    <!--div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div
            v-for="stat in statsCards"
            :key="stat.title"
            class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700"
        >
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div :class="`p-3 rounded-lg bg-${stat.color}-100 dark:bg-${stat.color}-900`">
                        <svg class="w-6 h-6 text-gray-600 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="stat.icon" />
                        </svg>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">{{ stat.title }}</p>
                    <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ stat.value }}</p>
                </div>
            </div>
        </div>
    </div-->

    <!-- Filters -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 mb-6">
        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">{{ t('products_management.list.filters.title') }}</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    {{ t('products_management.list.filters.search') }}
                </label>
                <input
                    v-model="searchQuery"
                    type="text"
                    :placeholder="t('products_management.list.search_placeholder')"
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                />
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    {{ t('products_management.list.filters.category') }}
                </label>
                <select
                    v-model="selectedCategory"
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                >
                    <option value="">{{ t('products_management.list.filters.all_categories') }}</option>
                    <option v-for="category in categories" :key="category.id" :value="category.id">
                        {{ category.name }}
                    </option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    {{ t('products_management.list.filters.status') }}
                </label>
                <select
                    v-model="selectedStatus"
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                >
                    <option value="">{{ t('products_management.list.filters.all_status') }}</option>
                    <option value="active">{{ t('products_management.list.filters.active') }}</option>
                    <option value="inactive">{{ t('products_management.list.filters.inactive') }}</option>
                    <option value="draft">{{ t('products_management.list.filters.draft') }}</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    {{ t('products_management.list.filters.price_range') }}
                </label>
                <div class="flex space-x-2">
                    <input
                        v-model="minPrice"
                        type="number"
                        :placeholder="t('products_management.list.filters.min_price')"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                    />
                    <input
                        v-model="maxPrice"
                        type="number"
                        :placeholder="t('products_management.list.filters.max_price')"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                    />
                </div>
            </div>
        </div>

        <div class="flex items-center justify-between mt-4">
            <label class="flex items-center">
                <input
                    v-model="inStock"
                    type="checkbox"
                    class="rounded border-gray-300 text-blue-600 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700"
                />
                <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">{{ t('products_management.list.filters.in_stock_only') }}</span>
            </label>

            <div class="flex space-x-3">
                <button
                    @click="clearFilters"
                    class="px-4 py-2 text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors"
                >
                    {{ t('products_management.list.clear_filters') }}
                </button>
                <button
                    @click="applyFilters"
                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors"
                >
                    {{ t('products_management.list.apply_filters') }}
                </button>
            </div>
        </div>
    </div>

    <!-- Products Table -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                        {{ t('products_management.list.table.product') }}
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                        {{ t('products_management.list.table.category') }}
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                        {{ t('products_management.list.table.price') }}
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                        {{ t('products_management.list.table.stock') }}
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                        {{ t('products_management.list.table.status') }}
                    </th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                        {{ t('products_management.list.table.actions') }}
                    </th>
                </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                <tr v-if="products.data && products.data.length === 0">
                    <td colspan="6" class="px-6 py-12 text-center">
                        <div class="flex flex-col items-center">
                            <svg class="w-12 h-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293H8m0 0l2.586 2.586a1 1 0 01.707.293l2.414-2.586a1 1 0 01.707-.293H16m-2-2h2M6 13h2" />
                            </svg>
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">
                                {{ t('products_management.list.table.no_products') }}
                            </h3>
                            <p class="text-gray-500 dark:text-gray-400 mb-4">
                                {{ t('products_management.list.table.no_products_description') }}
                            </p>
                            <Link
                                href="/products/create"
                                class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors"
                            >
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                                {{ t('products_management.list.table.create_first') }}
                            </Link>
                        </div>
                    </td>
                </tr>
                <tr v-for="product in products.data" :key="product.id" class="hover:bg-gray-50 dark:hover:bg-gray-700">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 h-10 w-10">
                                <img
                                    v-if="product.image"
                                    :src="product.image"
                                    :alt="product.name"
                                    class="h-10 w-10 rounded-lg object-cover"
                                />
                                <div v-else class="h-10 w-10 rounded-lg bg-gray-200 dark:bg-gray-600 flex items-center justify-center">
                                    <svg class="w-6 h-6 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-4">
                                <div class="text-sm font-medium text-gray-900 dark:text-white">
                                    {{ product.name }}
                                </div>
                                <div class="text-sm text-gray-500 dark:text-gray-400">
                                    {{ product.sku }}
                                </div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900 dark:text-white">
                            {{ product.category?.name || '-' }}
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900 dark:text-white">
                            ${{ product.price }}
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                                    <span :class="`px-2 py-1 text-xs font-medium rounded-full ${
                                        isLowStock(product)
                                            ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300'
                                            : 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300'
                                    }`">
                                        {{ product.stock_quantity }}
                                    </span>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                                <span :class="`px-2 py-1 text-xs font-medium rounded-full ${
                                    product.status === 'active'
                                        ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300'
                                        : product.status === 'inactive'
                                        ? 'bg-gray-100 text-gray-800 dark:bg-gray-900/30 dark:text-gray-300'
                                        : 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300'
                                }`">
                                    {{ product.status }}
                                </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <div class="flex items-center justify-end space-x-2">
                            <Link
                                :href="`/products/${product.id}`"
                                class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300"
                            >
                                {{ t('products_management.list.table.view') }}
                            </Link>
                            <Link
                                :href="`/products/${product.id}/edit`"
                                class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300"
                            >
                                {{ t('products_management.list.table.edit') }}
                            </Link>
                            <button
                                @click="deleteProduct(product.id)"
                                class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300"
                            >
                                {{ t('products_management.list.table.delete') }}
                            </button>
                        </div>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    <div v-if="products.links && products.links.length > 0" class="mt-6 flex items-center justify-between">
        <div class="text-sm text-gray-700 dark:text-gray-300">
            {{ t('products_management.list.showing_results', {
            from: products.from || 0,
            to: products.to || 0,
            total: products.total || 0
        }) }}
        </div>
        <div class="flex items-center space-x-2">
            <Link
                v-if="products.prev_page_url"
                :href="products.prev_page_url"
                class="px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-md hover:bg-gray-50 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-700"
            >
                {{ t('products_management.list.previous') }}
            </Link>
            <Link
                v-if="products.next_page_url"
                :href="products.next_page_url"
                class="px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-md hover:bg-gray-50 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-700"
            >
                {{ t('products_management.list.next') }}
            </Link>
        </div>
    </div>
</template>
