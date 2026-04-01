<script setup>
import { ref, computed } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import StorefrontLayout from '@/layouts/StorefrontLayout.vue';
import ProductCard from '@/modules/storefront/components/ProductCard.vue';
import ProductDetailsModal from '@/modules/storefront/components/ProductDetailsModal.vue';
import ProductQuickViewModal from '@/modules/storefront/components/ProductQuickViewModal.vue';
import { useTranslations } from '@/composables/useTranslations';

const { t } = useTranslations();
useTranslations('storefront');

const props = defineProps({
    store: { type: Object, required: true },
    products: { type: Object, required: true },
    categories: { type: Array, default: () => [] },
    filters: { type: Object, default: () => ({}) },
});

const quickViewProduct = ref(null);
const showQuickView = ref(false);
const detailsProduct = ref(null);
const showDetailsView = ref(false);

const localSearch = ref(props.filters.search || '');
const localCategory = ref(props.filters.category || '');
const localMinPrice = ref(props.filters.min_price || '');
const localMaxPrice = ref(props.filters.max_price || '');
const localSortBy = ref(props.filters.sort_by || '');

const productList = computed(() => props.products?.data || []);
const pagination = computed(() => props.products);

const sortOptions = [
    { value: '', label: 'Relevance' },
    { value: 'trending', label: 'Trending' },
    { value: 'created_at', label: 'Newest' },
    { value: 'price_asc', label: 'Price: Low to High' },
    { value: 'price_desc', label: 'Price: High to Low' },
];

const applyFilters = () => {
    router.get(`/marketplace/stores/${props.store.slug}/products`, {
        search: localSearch.value || undefined,
        category: localCategory.value || undefined,
        min_price: localMinPrice.value || undefined,
        max_price: localMaxPrice.value || undefined,
        sort_by: localSortBy.value || undefined,
    }, { preserveState: true, replace: true });
};

const clearFilters = () => {
    localSearch.value = '';
    localCategory.value = '';
    localMinPrice.value = '';
    localMaxPrice.value = '';
    localSortBy.value = '';
    router.get(`/marketplace/stores/${props.store.slug}/products`);
};

const hasActiveFilters = computed(() =>
    localSearch.value || localCategory.value || localMinPrice.value || localMaxPrice.value || localSortBy.value
);

const openQuickView = (product) => { quickViewProduct.value = product; showQuickView.value = true; };
const closeQuickView = () => { showQuickView.value = false; setTimeout(() => { quickViewProduct.value = null; }, 200); };
const openDetailsView = (product) => { detailsProduct.value = product; showDetailsView.value = true; };
const closeDetailsView = () => { showDetailsView.value = false; setTimeout(() => { detailsProduct.value = null; }, 200); };
</script>

<template>
    <StorefrontLayout>
        <ProductDetailsModal :product="detailsProduct" :show="showDetailsView" @close="closeDetailsView" />
        <ProductQuickViewModal :product="quickViewProduct" :show="showQuickView" @close="closeQuickView" @open-details="openDetailsView" />

        <!-- Store Mini Header -->
        <div class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 shadow-sm">
            <div class="max-w-7xl mx-auto px-4 py-4 flex items-center gap-4">
                <Link :href="`/marketplace/stores/${store.slug}`" class="h-12 w-12 flex-shrink-0 overflow-hidden rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 shadow-sm">
                    <img v-if="store.logo" :src="store.logo" :alt="store.name" class="h-full w-full object-cover" />
                    <div v-else class="flex h-full w-full items-center justify-center bg-gradient-to-br from-blue-600 to-purple-600">
                        <span class="text-lg font-bold text-white">{{ store.name?.charAt(0) }}</span>
                    </div>
                </Link>
                <div class="flex-1 min-w-0">
                    <Link :href="`/marketplace/stores/${store.slug}`" class="font-bold text-gray-900 dark:text-white hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
                        {{ store.name }}
                    </Link>
                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ pagination?.total || 0 }} products</p>
                </div>
            </div>

            <!-- Navigation Tabs -->
            <div class="max-w-7xl mx-auto px-4">
                <nav class="flex gap-0">
                    <Link :href="`/marketplace/stores/${store.slug}`" class="border-b-2 border-transparent px-6 py-3 text-sm font-medium text-gray-600 hover:text-gray-900 hover:border-gray-300 transition-colors dark:text-gray-400 dark:hover:text-white">
                        Home
                    </Link>
                    <Link :href="`/marketplace/stores/${store.slug}/products`" class="border-b-2 border-blue-600 px-6 py-3 text-sm font-semibold text-blue-600 dark:text-blue-400">
                        Products
                    </Link>
                    <Link :href="`/marketplace/stores/${store.slug}/about`" class="border-b-2 border-transparent px-6 py-3 text-sm font-medium text-gray-600 hover:text-gray-900 hover:border-gray-300 transition-colors dark:text-gray-400 dark:hover:text-white">
                        About
                    </Link>
                </nav>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 py-8">
            <!-- Category Tabs -->
            <div v-if="categories?.length" class="mb-6 flex flex-wrap gap-2">
                <button
                    @click="localCategory = ''; applyFilters()"
                    :class="['rounded-full px-4 py-1.5 text-sm font-medium transition-colors', !localCategory ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600']"
                >
                    All
                </button>
                <button
                    v-for="cat in categories"
                    :key="cat.id"
                    @click="localCategory = cat.slug; applyFilters()"
                    :class="[
                        'rounded-full px-4 py-1.5 text-sm font-medium transition-colors',
                        localCategory === cat.slug
                            ? 'bg-blue-600 text-white'
                            : 'bg-gray-100 text-gray-700 hover:bg-blue-50 hover:text-blue-600 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-blue-900/30 dark:hover:text-blue-400'
                    ]"
                >
                    {{ cat.name }}
                </button>
            </div>

            <!-- Filter Bar -->
            <div class="mb-6 flex flex-wrap items-center gap-3">
                <input
                    v-model="localSearch"
                    type="text"
                    placeholder="Search products in this store..."
                    class="flex-1 min-w-[200px] rounded-xl border border-gray-200 bg-white px-4 py-2.5 text-sm text-gray-900 dark:border-gray-600 dark:bg-gray-800 dark:text-white"
                    @keyup.enter="applyFilters"
                />
                <div class="flex items-center gap-2">
                    <input v-model="localMinPrice" type="number" placeholder="Min $" class="w-24 rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-sm dark:border-gray-600 dark:bg-gray-800 dark:text-white" />
                    <span class="text-gray-400">–</span>
                    <input v-model="localMaxPrice" type="number" placeholder="Max $" class="w-24 rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-sm dark:border-gray-600 dark:bg-gray-800 dark:text-white" />
                </div>
                <select v-model="localSortBy" @change="applyFilters" class="rounded-xl border border-gray-200 bg-white px-3 py-2.5 text-sm text-gray-700 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200">
                    <option v-for="opt in sortOptions" :key="opt.value" :value="opt.value">{{ opt.label }}</option>
                </select>
                <button @click="applyFilters" class="rounded-xl bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-blue-700 transition-colors">Apply</button>
                <button v-if="hasActiveFilters" @click="clearFilters" class="rounded-xl border border-gray-200 px-4 py-2.5 text-sm text-gray-500 hover:text-red-500 transition-colors dark:border-gray-600">Clear</button>
            </div>

            <!-- Products Grid -->
            <div v-if="productList.length > 0" class="grid grid-cols-1 gap-5 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4">
                <ProductCard
                    v-for="product in productList"
                    :key="product.id"
                    :product="product"
                    :show-store="false"
                    class="!w-full"
                    @quick-view="openQuickView"
                    @details-view="openDetailsView"
                />
            </div>

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
                    :href="`/marketplace/stores/${store.slug}/products?page=${page}`"
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
