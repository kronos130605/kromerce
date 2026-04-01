<script setup>
import { ref } from 'vue';
import { Link } from '@inertiajs/vue3';
import StorefrontLayout from '@/layouts/StorefrontLayout.vue';
import ProductDetailsModal from '@/modules/storefront/components/ProductDetailsModal.vue';
import ProductQuickViewModal from '@/modules/storefront/components/ProductQuickViewModal.vue';
import ProductsGridSection from '@/modules/storefront/components/ProductsGridSection.vue';
import { useTranslations } from '@/composables/useTranslations';

const { t } = useTranslations();
useTranslations('storefront');

const props = defineProps({
    store: { type: Object, required: true },
    featured_products: { type: Array, default: () => [] },
    all_products: { type: Object, default: () => ({ data: [] }) },
    stats: { type: Object, default: () => ({}) },
});

const quickViewProduct = ref(null);
const showQuickView = ref(false);
const detailsProduct = ref(null);
const showDetailsView = ref(false);

const openQuickView = (product) => { quickViewProduct.value = product; showQuickView.value = true; };
const closeQuickView = () => { showQuickView.value = false; setTimeout(() => { quickViewProduct.value = null; }, 200); };
const openDetailsView = (product) => { detailsProduct.value = product; showDetailsView.value = true; };
const closeDetailsView = () => { showDetailsView.value = false; setTimeout(() => { detailsProduct.value = null; }, 200); };

</script>

<template>
    <StorefrontLayout>
        <ProductDetailsModal :product="detailsProduct" :show="showDetailsView" @close="closeDetailsView" />
        <ProductQuickViewModal :product="quickViewProduct" :show="showQuickView" @close="closeQuickView" @open-details="openDetailsView" />

        <!-- Store Banner -->
        <div class="relative overflow-hidden">
            <!-- Banner image or gradient fallback -->
            <div class="h-52 w-full bg-gradient-to-r from-blue-600 via-indigo-500 to-purple-600 dark:from-blue-900 dark:via-indigo-800 dark:to-purple-900">
                <img v-if="store.banner" :src="store.banner" :alt="store.name" class="h-full w-full object-cover" />
            </div>

            <!-- Store info overlay -->
            <div class="max-w-7xl mx-auto px-4">
                <div class="relative -mt-12 pb-5 flex flex-col sm:flex-row sm:items-end gap-5">
                    <!-- Logo -->
                    <div class="h-24 w-24 flex-shrink-0 overflow-hidden rounded-2xl border-4 border-white bg-white shadow-xl dark:border-gray-900 dark:bg-gray-800">
                        <img v-if="store.logo" :src="store.logo" :alt="store.name" class="h-full w-full object-cover" />
                        <div v-else class="flex h-full w-full items-center justify-center bg-gradient-to-br from-blue-600 to-purple-600">
                            <span class="text-3xl font-bold text-white">{{ store.name?.charAt(0) }}</span>
                        </div>
                    </div>

                    <!-- Name & Badges -->
                    <div class="flex-1">
                        <div class="flex flex-wrap items-center gap-2 mb-1">
                            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ store.name }}</h1>
                            <span v-if="store.verified" class="inline-flex items-center gap-1 rounded-full bg-blue-100 px-2.5 py-0.5 text-xs font-semibold text-blue-700 dark:bg-blue-900/40 dark:text-blue-300">
                                <svg class="w-3 h-3 fill-current" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                {{ t('storefront.store.verified') }}
                            </span>
                            <span v-if="store.top_seller" class="rounded-full bg-amber-100 px-2.5 py-0.5 text-xs font-semibold text-amber-700 dark:bg-amber-900/40 dark:text-amber-300">
                                ⭐ {{ t('storefront.store.top_seller') }}
                            </span>
                        </div>
                        <p v-if="store.description" class="text-sm text-gray-600 dark:text-gray-400 line-clamp-1">{{ store.description }}</p>
                    </div>

                    <!-- Stats -->
                    <div class="flex items-center gap-5 text-sm flex-shrink-0">
                        <div class="text-center">
                            <p class="text-xl font-bold text-gray-900 dark:text-white">{{ stats.total_products || 0 }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Products</p>
                        </div>
                        <div class="text-center">
                            <div class="flex items-center justify-center gap-1">
                                <svg class="w-4 h-4 text-amber-400 fill-current" viewBox="0 0 20 20"><path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/></svg>
                                <p class="text-xl font-bold text-gray-900 dark:text-white">{{ stats.rating || '4.5' }}</p>
                            </div>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Rating</p>
                        </div>
                        <div class="text-center">
                            <p class="text-xl font-bold text-gray-900 dark:text-white">{{ stats.total_reviews || 0 }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ t('storefront.product.reviews') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Store Navigation Tabs -->
        <div class="border-b border-gray-200 dark:border-gray-700 sticky top-16 z-30 bg-white dark:bg-gray-800 shadow-sm">
            <div class="max-w-7xl mx-auto px-4">
                <nav class="flex gap-0">
                    <Link
                        :href="`/marketplace/stores/${store.slug}`"
                        class="border-b-2 border-blue-600 px-6 py-4 text-sm font-semibold text-blue-600 dark:text-blue-400"
                    >
                        Home
                    </Link>
                    <Link
                        :href="`/marketplace/stores/${store.slug}/products`"
                        class="border-b-2 border-transparent px-6 py-4 text-sm font-medium text-gray-600 hover:text-gray-900 hover:border-gray-300 transition-colors dark:text-gray-400 dark:hover:text-white"
                    >
                        Products
                    </Link>
                    <Link
                        :href="`/marketplace/stores/${store.slug}/about`"
                        class="border-b-2 border-transparent px-6 py-4 text-sm font-medium text-gray-600 hover:text-gray-900 hover:border-gray-300 transition-colors dark:text-gray-400 dark:hover:text-white"
                    >
                        About
                    </Link>
                </nav>
            </div>
        </div>

        <!-- Featured Products -->
        <ProductsGridSection
            v-if="featured_products?.length > 0"
            title="Featured Products"
            icon="⭐"
            :products="featured_products"
            :show-store="false"
            :view-all-link="`/marketplace/stores/${store.slug}/products`"
            bg-class="bg-white dark:bg-gray-800"
            @quick-view="openQuickView"
            @details-view="openDetailsView"
        />

        <!-- All Products Preview -->
        <ProductsGridSection
            v-if="all_products?.data?.length > 0"
            title="All Products"
            icon="🛍️"
            :products="all_products?.data || []"
            :show-store="false"
            :view-all-link="`/marketplace/stores/${store.slug}/products`"
            bg-class="bg-gray-50 dark:bg-gray-900"
            @quick-view="openQuickView"
            @details-view="openDetailsView"
        />

        <!-- Empty state -->
        <div v-if="!featured_products?.length && !all_products?.data?.length" class="py-24 text-center">
            <svg class="mx-auto h-16 w-16 text-gray-300 dark:text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
            <p class="text-lg font-semibold text-gray-500 dark:text-gray-400">{{ t('storefront.empty.no_products') }}</p>
        </div>
    </StorefrontLayout>
</template>
