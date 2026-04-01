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
    category: { type: Object, required: true },
    products: { type: Object, required: true },
    stores: { type: Object, default: () => ({ data: [] }) },
    filters: { type: Object, default: () => ({}) },
});

const quickViewProduct = ref(null);
const showQuickView = ref(false);
const detailsProduct = ref(null);
const showDetailsView = ref(false);

const localSearch = ref(props.filters.search || '');
const localStore = ref(props.filters.store || '');
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
    router.get(`/category/${props.category.slug}`, {
        search: localSearch.value || undefined,
        store: localStore.value || undefined,
        min_price: localMinPrice.value || undefined,
        max_price: localMaxPrice.value || undefined,
        sort_by: localSortBy.value || undefined,
    }, { preserveState: true, replace: true });
};

const clearFilters = () => {
    localSearch.value = '';
    localStore.value = '';
    localMinPrice.value = '';
    localMaxPrice.value = '';
    localSortBy.value = '';
    router.get(`/category/${props.category.slug}`);
};

const hasActiveFilters = computed(() =>
    localSearch.value || localStore.value || localMinPrice.value || localMaxPrice.value || localSortBy.value
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

        <!-- Category Hero -->
        <div class="bg-gradient-to-br from-blue-600 via-blue-500 to-indigo-600 dark:from-blue-800 dark:via-blue-700 dark:to-indigo-800">
            <div class="max-w-7xl mx-auto px-4 py-12">
                <nav class="flex items-center gap-2 text-sm text-blue-200 mb-4">
                    <Link href="/" class="hover:text-white transition-colors">{{ t('storefront.navigation.home') }}</Link>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    <Link href="/products" class="hover:text-white transition-colors">{{ t('storefront.navigation.products') }}</Link>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    <span class="text-white font-medium">{{ category.name }}</span>
                </nav>
                <div class="flex items-center gap-5">
                    <div class="h-16 w-16 flex-shrink-0 overflow-hidden rounded-2xl bg-white/20 flex items-center justify-center">
                        <img v-if="category.image" :src="category.image" :alt="category.name" class="h-full w-full object-cover" />
                        <span v-else class="text-3xl">{{ category.icon || '📦' }}</span>
                    </div>
                    <div>
                        <h1 class="text-3xl font-bold text-white">{{ category.name }}</h1>
                        <p v-if="category.description" class="mt-1 text-blue-100">{{ category.description }}</p>
                        <p class="mt-1 text-blue-200 text-sm">{{ pagination?.total || 0 }} products</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 py-8">
            <!-- Filter Bar -->
            <div class="mb-6 flex flex-wrap items-center gap-3">
                <input
                    v-model="localSearch"
                    type="text"
                    placeholder="Search in this category..."
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
                    :href="`/category/${category.slug}?page=${page}`"
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
