<script setup>
import { ref, computed } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import StorefrontLayout from '@/layouts/StorefrontLayout.vue';
import ProductCard from '@/modules/storefront/components/ProductCard.vue';
import ProductQuickViewModal from '@/modules/storefront/components/ProductQuickViewModal.vue';
import { useTranslations } from '@/composables/useTranslations';

const { t } = useTranslations();
useTranslations('storefront');

const props = defineProps({
    products: { type: Object, required: true },
    categories: { type: Array, default: () => [] },
    stores: { type: Object, default: () => ({ data: [] }) },
    filters: { type: Object, default: () => ({}) },
});

const quickViewProduct = ref(null);
const showQuickView = ref(false);
const showFilters = ref(false);

const localSearch = ref(props.filters.search || '');
const localCategory = ref(props.filters.category || '');
const localStore = ref(props.filters.store || '');
const localMinPrice = ref(props.filters.min_price || '');
const localMaxPrice = ref(props.filters.max_price || '');
const localSortBy = ref(props.filters.sort_by || '');
const localSortOrder = ref(props.filters.sort_order || 'desc');

const sortOptions = [
    { value: '', label: 'Relevance' },
    { value: 'trending', label: 'Trending' },
    { value: 'created_at', label: 'Newest' },
    { value: 'price_asc', label: 'Price: Low to High' },
    { value: 'price_desc', label: 'Price: High to Low' },
];

const productList = computed(() => props.products?.data || []);
const pagination = computed(() => props.products);

const applyFilters = () => {
    router.get('/products', {
        search: localSearch.value || undefined,
        category: localCategory.value || undefined,
        store: localStore.value || undefined,
        min_price: localMinPrice.value || undefined,
        max_price: localMaxPrice.value || undefined,
        sort_by: localSortBy.value || undefined,
        sort_order: localSortOrder.value || undefined,
    }, { preserveState: true, replace: true });
};

const clearFilters = () => {
    localSearch.value = '';
    localCategory.value = '';
    localStore.value = '';
    localMinPrice.value = '';
    localMaxPrice.value = '';
    localSortBy.value = '';
    router.get('/products');
};

const hasActiveFilters = computed(() =>
    localSearch.value || localCategory.value || localStore.value || localMinPrice.value || localMaxPrice.value || localSortBy.value
);

const openQuickView = (product) => { quickViewProduct.value = product; showQuickView.value = true; };
const closeQuickView = () => { showQuickView.value = false; setTimeout(() => { quickViewProduct.value = null; }, 200); };
</script>

<template>
    <StorefrontLayout>
        <ProductQuickViewModal :product="quickViewProduct" :show="showQuickView" @close="closeQuickView" />

        <div class="max-w-7xl mx-auto px-4 py-8">
            <!-- Header -->
            <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ t('storefront.navigation.all_products') }}</h1>
                    <p v-if="pagination?.total" class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        {{ pagination.total }} products found
                    </p>
                </div>
                <div class="flex items-center gap-3">
                    <button
                        @click="showFilters = !showFilters"
                        class="flex items-center gap-2 rounded-xl border border-gray-200 px-4 py-2.5 text-sm font-medium text-gray-700 hover:border-blue-300 hover:text-blue-600 transition-colors dark:border-gray-600 dark:text-gray-300 dark:hover:border-blue-500"
                    >
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                        Filters
                        <span v-if="hasActiveFilters" class="h-2 w-2 rounded-full bg-blue-600"></span>
                    </button>
                    <select
                        v-model="localSortBy"
                        @change="applyFilters"
                        class="rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-sm text-gray-700 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200"
                    >
                        <option v-for="opt in sortOptions" :key="opt.value" :value="opt.value">{{ opt.label }}</option>
                    </select>
                </div>
            </div>

            <!-- Filter Panel -->
            <Transition enter-active-class="transition ease-out duration-200" enter-from-class="opacity-0 -translate-y-2" enter-to-class="opacity-100 translate-y-0" leave-active-class="transition ease-in duration-150" leave-from-class="opacity-100 translate-y-0" leave-to-class="opacity-0 -translate-y-2">
                <div v-if="showFilters" class="mb-6 rounded-2xl border border-gray-100 bg-white p-5 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                        <div>
                            <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Search</label>
                            <input v-model="localSearch" type="text" placeholder="Product name..." class="w-full rounded-xl border border-gray-200 bg-gray-50 px-3 py-2 text-sm text-gray-900 dark:border-gray-600 dark:bg-gray-700 dark:text-white" @keyup.enter="applyFilters" />
                        </div>
                        <div>
                            <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Category</label>
                            <select v-model="localCategory" class="w-full rounded-xl border border-gray-200 bg-gray-50 px-3 py-2 text-sm text-gray-900 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                                <option value="">All Categories</option>
                                <option v-for="cat in categories" :key="cat.id" :value="cat.slug">{{ cat.name }}</option>
                            </select>
                        </div>
                        <div>
                            <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Price Range</label>
                            <div class="flex items-center gap-2">
                                <input v-model="localMinPrice" type="number" placeholder="Min" class="w-full rounded-xl border border-gray-200 bg-gray-50 px-3 py-2 text-sm text-gray-900 dark:border-gray-600 dark:bg-gray-700 dark:text-white" />
                                <span class="text-gray-400">–</span>
                                <input v-model="localMaxPrice" type="number" placeholder="Max" class="w-full rounded-xl border border-gray-200 bg-gray-50 px-3 py-2 text-sm text-gray-900 dark:border-gray-600 dark:bg-gray-700 dark:text-white" />
                            </div>
                        </div>
                        <div class="flex items-end gap-2">
                            <button @click="applyFilters" class="flex-1 rounded-xl bg-blue-600 py-2.5 text-sm font-semibold text-white hover:bg-blue-700 transition-colors">Apply</button>
                            <button v-if="hasActiveFilters" @click="clearFilters" class="rounded-xl border border-gray-200 px-3 py-2.5 text-sm text-gray-500 hover:text-red-500 transition-colors dark:border-gray-600 dark:text-gray-400">Clear</button>
                        </div>
                    </div>
                </div>
            </Transition>

            <!-- Category Pills -->
            <div v-if="categories?.length" class="mb-6 flex flex-wrap gap-2">
                <Link
                    href="/marketplace/products"
                    :class="['rounded-full px-4 py-1.5 text-sm font-medium transition-colors', !localCategory ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600']"
                >
                    All
                </Link>
                <Link
                    v-for="cat in categories.slice(0, 8)"
                    :key="cat.id"
                    :href="`/marketplace/category/${cat.slug}`"
                    class="rounded-full bg-gray-100 px-4 py-1.5 text-sm font-medium text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-colors dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-blue-900/30 dark:hover:text-blue-400"
                >
                    {{ cat.name }}
                </Link>
            </div>

            <!-- Products Grid -->
            <div v-if="productList.length > 0" class="grid grid-cols-1 gap-5 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4">
                <ProductCard
                    v-for="product in productList"
                    :key="product.id"
                    :product="product"
                    class="!w-full"
                    @quick-view="openQuickView"
                />
            </div>

            <!-- Empty State -->
            <div v-else class="py-24 text-center">
                <svg class="mx-auto h-16 w-16 text-gray-300 dark:text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                <p class="text-lg font-semibold text-gray-500 dark:text-gray-400">{{ t('storefront.empty.no_products') }}</p>
                <button v-if="hasActiveFilters" @click="clearFilters" class="mt-4 text-sm text-blue-600 hover:underline dark:text-blue-400">Clear filters</button>
            </div>

            <!-- Pagination -->
            <div v-if="pagination?.last_page > 1" class="mt-10 flex items-center justify-center gap-2">
                <Link
                    v-for="page in pagination.last_page"
                    :key="page"
                    :href="`/products?page=${page}${localSearch ? '&search=' + localSearch : ''}${localCategory ? '&category=' + localCategory : ''}`"
                    :class="[
                        'flex h-10 w-10 items-center justify-center rounded-xl text-sm font-medium transition-colors',
                        page === pagination.current_page
                            ? 'bg-blue-600 text-white'
                            : 'border border-gray-200 text-gray-600 hover:border-blue-300 hover:text-blue-600 dark:border-gray-600 dark:text-gray-400'
                    ]"
                >
                    {{ page }}
                </Link>
            </div>
        </div>
    </StorefrontLayout>
</template>
