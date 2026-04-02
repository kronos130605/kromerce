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
    query: { type: String, default: '' },
    products: { type: Object, required: true },
});

const quickViewProduct = ref(null);
const showQuickView = ref(false);

const searchInput = ref(props.query);

const productList = computed(() => props.products?.data || []);
const pagination = computed(() => props.products);

const handleSearch = () => {
    if (searchInput.value.trim()) {
        router.get('/search', { q: searchInput.value.trim() });
    }
};

const openQuickView = (product) => { quickViewProduct.value = product; showQuickView.value = true; };
const closeQuickView = () => { showQuickView.value = false; setTimeout(() => { quickViewProduct.value = null; }, 200); };
</script>

<template>
    <StorefrontLayout>
        <ProductQuickViewModal :product="quickViewProduct" :show="showQuickView" @close="closeQuickView" />

        <div class="max-w-7xl mx-auto px-4 py-8">
            <!-- Search Header -->
            <div class="mb-8">
                <form @submit.prevent="handleSearch" class="flex gap-3 max-w-2xl">
                    <div class="flex flex-1 overflow-hidden rounded-2xl border border-gray-200 focus-within:border-blue-500 focus-within:ring-2 focus-within:ring-blue-500/20 dark:border-gray-600 transition-all">
                        <input
                            v-model="searchInput"
                            type="text"
                            :placeholder="t('storefront.navigation.search_placeholder')"
                            class="flex-1 px-5 py-3.5 text-sm bg-white dark:bg-gray-800 text-gray-900 dark:text-white outline-none"
                        />
                    </div>
                    <button type="submit" class="rounded-2xl bg-blue-600 px-6 py-3.5 text-sm font-semibold text-white hover:bg-blue-700 transition-colors">
                        {{ t('storefront.navigation.search') }}
                    </button>
                </form>

                <div class="mt-4">
                    <template v-if="query">
                        <h1 class="text-xl font-bold text-gray-900 dark:text-white">
                            Search results for <span class="text-blue-600 dark:text-blue-400">"{{ query }}"</span>
                        </h1>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                            {{ pagination?.total || 0 }} products found
                        </p>
                    </template>
                    <h1 v-else class="text-xl font-bold text-gray-900 dark:text-white">
                        {{ t('storefront.navigation.search') }}
                    </h1>
                </div>
            </div>

            <!-- Results -->
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
                <svg class="mx-auto h-16 w-16 text-gray-300 dark:text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <p class="text-lg font-semibold text-gray-500 dark:text-gray-400">
                    {{ query ? `No results for "${query}"` : 'Type something to search' }}
                </p>
                <div class="mt-6 flex items-center justify-center gap-3">
                    <Link href="/marketplace/products" class="rounded-xl bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-blue-700 transition-colors">
                        Browse all products
                    </Link>
                    <Link href="/marketplace/stores" class="rounded-xl border border-gray-200 px-5 py-2.5 text-sm font-medium text-gray-700 hover:border-blue-300 hover:text-blue-600 transition-colors dark:border-gray-600 dark:text-gray-300">
                        Browse stores
                    </Link>
                </div>
            </div>

            <!-- Pagination -->
            <div v-if="pagination?.last_page > 1" class="mt-10 flex items-center justify-center gap-2">
                <Link
                    v-for="page in pagination.last_page"
                    :key="page"
                    :href="`/search?q=${encodeURIComponent(query)}&page=${page}`"
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
