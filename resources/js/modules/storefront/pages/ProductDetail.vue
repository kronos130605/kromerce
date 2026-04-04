<script setup>
import { ref, computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import StorefrontLayout from '@/layouts/StorefrontLayout.vue';
import StorefrontProductCarousel from '@/modules/storefront/components/StorefrontProductCarousel.vue';
import ProductQuickViewModal from '@/modules/storefront/components/ProductQuickViewModal.vue';
import { useCart } from '@/composables/useCart';
import { useProductPresentation } from '@/composables/useProductPresentation';
import { useTranslations } from '@/composables/useTranslations';

import { useProductGallery } from '@/composables/useProductGallery.js';
import ProductImageGallery from '@/components/shared/ProductImageGallery.vue';

const { t } = useTranslations();
useTranslations('storefront');
const { addToCart, createAddToCartHandler } = useCart();

const props = defineProps({
    product: { type: Object, required: true },
    related_products: { type: Array, default: () => [] },
    store_products: { type: Array, default: () => [] },
    sale_currencies: { type: Array, default: () => [] },
    breadcrumb_context: { type: String, default: 'default' },
    breadcrumb_store: { type: Object, default: null },
});

const hasMultipleCurrencies = computed(() => props.sale_currencies.length > 1);
const selectedCurrency = ref(props.sale_currencies[0]?.code ?? props.product.base_currency ?? 'USD');

const selectedCurrencyMeta = computed(() =>
    props.sale_currencies.find(c => c.code === selectedCurrency.value) ??
    { code: selectedCurrency.value, symbol: props.product.base_currency, flag: '' }
);

const quantity = ref(1);
const quickViewProduct = ref(null);
const showQuickView = ref(false);

const productRef = computed(() => props.product);
const {
    gallery,
    hasDiscount,
    discountPercentage,
    displayPrice,
    savingsAmount,
    isOutOfStock,
    ratingValue,
    reviewCount,
    stockLabel,
    formatPrice,
} = useProductPresentation(productRef);

// Use the gallery composable for image navigation
const {
    activeImageIndex,
    activeImageUrl,
    isActiveImage,
    setActiveImage,
} = useProductGallery(productRef, { galleryRef: gallery });

// Create the add to cart handler with visual feedback
const { handleAddToCart, added } = createAddToCartHandler();

// Wrapper to call with proper arguments — passes selected currency
const onAddToCart = () => {
    handleAddToCart(
        { ...props.product, selected_currency: selectedCurrency.value },
        quantity.value,
        { isOutOfStock: isOutOfStock.value, validateProduct: false }
    );
};

const openQuickView = (product) => {
    quickViewProduct.value = product;
    showQuickView.value = true;
};
const closeQuickView = () => {
    showQuickView.value = false;
    setTimeout(() => { quickViewProduct.value = null; }, 200);
};
</script>

<template>
    <StorefrontLayout>
        <ProductQuickViewModal :product="quickViewProduct" :show="showQuickView" @close="closeQuickView" />

        <div class="max-w-7xl mx-auto px-4 py-8">
            <!-- Breadcrumb -->
            <nav class="flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400 mb-6">
                <Link href="/" class="hover:text-blue-600 dark:hover:text-blue-400 transition-colors">{{ t('storefront.navigation.home') }}</Link>
                
                <!-- Store context: Home > Store Name > Product -->
                <template v-if="breadcrumb_context === 'store' && breadcrumb_store">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    <Link :href="`/marketplace/stores/${breadcrumb_store.slug}`" class="hover:text-blue-600 dark:hover:text-blue-400 transition-colors truncate max-w-[150px]">
                        {{ breadcrumb_store.name }}
                    </Link>
                </template>
                
                <!-- Products listing context: Home > Products > Product -->
                <template v-else-if="breadcrumb_context === 'products'">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    <Link href="/marketplace/products" class="hover:text-blue-600 dark:hover:text-blue-400 transition-colors">{{ t('storefront.navigation.products') }}</Link>
                </template>
                
                <!-- Default/Other: Home > Product (no middle item) -->
                
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                <span class="text-gray-900 dark:text-white font-medium truncate max-w-[200px]">{{ product.name }}</span>
            </nav>

            <!-- Product Main Section -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-10 mb-14">
                <!-- Gallery -->
                <div class="space-y-4">
                    <ProductImageGallery
                        :product="product"
                        :gallery="gallery"
                        :active-image-url="activeImageUrl"
                        :active-image-index="activeImageIndex"
                        :thumbnail-limit="null"
                        thumbnail-size="md"
                        variant="shadow"
                        container-class=""
                        @set-active="setActiveImage"
                    />
                </div>

                <!-- Product Info -->
                <div class="flex flex-col gap-5">
                    <!-- Badges -->
                    <div class="flex flex-wrap gap-2">
                        <span v-if="hasDiscount" class="rounded-full bg-rose-100 px-3 py-1 text-xs font-semibold text-rose-700 dark:bg-rose-900/40 dark:text-rose-300">
                            -{{ discountPercentage }}% {{ t('storefront.product.sale') }}
                        </span>
                        <span v-if="product.is_new" class="rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300">
                            {{ t('storefront.product.new') }}
                        </span>
                        <span v-if="product.featured" class="rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold text-amber-700 dark:bg-amber-900/40 dark:text-amber-300">
                            {{ t('storefront.product.featured') }}
                        </span>
                        <span class="rounded-full bg-blue-50 px-3 py-1 text-xs font-semibold text-blue-700 dark:bg-blue-900/30 dark:text-blue-300">
                            {{ stockLabel }}
                        </span>
                    </div>

                    <!-- Store & Name -->
                    <div>
                        <Link v-if="product.store" :href="`/marketplace/stores/${product.store.slug}`" class="text-sm font-semibold text-blue-600 dark:text-blue-400 hover:underline">
                            {{ product.store.name }}
                        </Link>
                        <h1 class="mt-1 text-3xl font-bold tracking-tight text-gray-900 dark:text-white leading-tight">
                            {{ product.name }}
                        </h1>
                    </div>

                    <!-- Rating -->
                    <div class="flex items-center gap-3 text-sm text-gray-500 dark:text-gray-400">
                        <div class="flex items-center gap-1.5">
                            <div class="flex items-center">
                                <template v-for="i in 5" :key="i">
                                    <svg :class="i <= Math.round(ratingValue) ? 'text-amber-400' : 'text-gray-200 dark:text-gray-700'" class="h-4 w-4 fill-current" viewBox="0 0 20 20">
                                        <path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/>
                                    </svg>
                                </template>
                            </div>
                            <span class="font-semibold text-gray-700 dark:text-gray-200">{{ ratingValue.toFixed(1) }}</span>
                        </div>
                        <span>•</span>
                        <span>{{ reviewCount }} {{ t('storefront.product.reviews') }}</span>
                        <span>•</span>
                        <span>{{ product.sales_count || 0 }} {{ t('storefront.product.sold') }}</span>
                    </div>

                    <!-- Currency selector (when product has multiple sale currencies) -->
                    <div v-if="hasMultipleCurrencies" class="space-y-2">
                        <p class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ t('storefront.product.select_currency') }}</p>
                        <div class="flex flex-wrap gap-2">
                            <button
                                v-for="cur in sale_currencies"
                                :key="cur.code"
                                type="button"
                                @click="selectedCurrency = cur.code"
                                :class="[
                                    'flex items-center gap-1.5 rounded-xl border-2 px-4 py-2 text-sm font-semibold transition-all duration-150',
                                    selectedCurrency === cur.code
                                        ? 'border-blue-500 bg-blue-50 dark:bg-blue-950/30 text-blue-700 dark:text-blue-300'
                                        : 'border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-400 hover:border-gray-300 dark:hover:border-gray-600'
                                ]"
                            >
                                <span v-if="cur.flag">{{ cur.flag }}</span>
                                <span>{{ cur.code }}</span>
                            </button>
                        </div>
                    </div>

                    <!-- Price -->
                    <div class="rounded-3xl border border-gray-100 bg-gray-50 p-5 dark:border-gray-700 dark:bg-gray-800/80">
                        <div class="flex flex-wrap items-end gap-3">
                            <span class="text-4xl font-bold tracking-tight text-gray-900 dark:text-white">{{ formatPrice(displayPrice) }}</span>
                            <span v-if="hasMultipleCurrencies" class="text-base font-semibold text-gray-400 dark:text-gray-500">{{ selectedCurrency }}</span>
                            <span v-if="hasDiscount" class="text-xl text-gray-400 line-through dark:text-gray-500">{{ formatPrice(product.base_price) }}</span>
                        </div>
                        <p v-if="hasDiscount" class="mt-2 text-sm font-medium text-emerald-600 dark:text-emerald-400">
                            {{ t('storefront.product.you_save') }} {{ formatPrice(savingsAmount) }}
                        </p>
                    </div>

                    <!-- Categories -->
                    <div v-if="product.categories?.length" class="flex flex-wrap gap-2">
                        <Link
                            v-for="cat in product.categories"
                            :key="cat.id"
                            :href="`/marketplace/category/${cat.slug}`"
                            class="rounded-full border border-gray-200 px-3 py-1 text-xs font-medium text-gray-600 hover:border-blue-300 hover:text-blue-600 transition-colors dark:border-gray-700 dark:text-gray-400 dark:hover:border-blue-500 dark:hover:text-blue-400"
                        >
                            {{ cat.name }}
                        </Link>
                    </div>

                    <!-- Add to Cart -->
                    <div class="flex flex-col gap-4 sm:flex-row sm:items-center">
                        <div class="flex items-center rounded-2xl border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-800">
                            <button @click="quantity = Math.max(1, quantity - 1)" :disabled="quantity <= 1" class="px-4 py-3 text-gray-600 hover:bg-gray-50 disabled:opacity-40 dark:text-gray-300 dark:hover:bg-gray-700 transition-colors">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/></svg>
                            </button>
                            <span class="min-w-[3rem] px-3 text-center text-sm font-semibold text-gray-900 dark:text-white">{{ quantity }}</span>
                            <button @click="quantity = Math.min(product.stock_quantity || 99, quantity + 1)" :disabled="product.stock_quantity > 0 && quantity >= product.stock_quantity" class="px-4 py-3 text-gray-600 hover:bg-gray-50 disabled:opacity-40 dark:text-gray-300 dark:hover:bg-gray-700 transition-colors">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            </button>
                        </div>
                        <button
                            @click="onAddToCart"
                            :disabled="isOutOfStock"
                            :class="[
                                'inline-flex flex-1 items-center justify-center gap-2 rounded-2xl px-6 py-3.5 text-sm font-semibold transition-all duration-300',
                                added ? 'bg-emerald-500 text-white' : isOutOfStock ? 'cursor-not-allowed bg-gray-200 text-gray-400 dark:bg-gray-700' : 'bg-blue-600 text-white hover:bg-blue-700 active:scale-95'
                            ]"
                        >
                            <svg v-if="!added" class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                            <svg v-else class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            {{ added ? t('storefront.product.added') : t('storefront.product.add_to_cart') }}
                        </button>
                    </div>

                    <!-- Info Cards -->
                    <div class="grid grid-cols-2 gap-3">
                        <div class="rounded-2xl border border-gray-100 p-4 dark:border-gray-700">
                            <p class="text-xs font-semibold uppercase tracking-[0.16em] text-gray-400 dark:text-gray-500">{{ t('storefront.product.shipping_label') }}</p>
                            <p class="mt-2 text-sm font-medium text-gray-800 dark:text-gray-200">{{ t('storefront.product.shipping_value') }}</p>
                        </div>
                        <div class="rounded-2xl border border-gray-100 p-4 dark:border-gray-700">
                            <p class="text-xs font-semibold uppercase tracking-[0.16em] text-gray-400 dark:text-gray-500">{{ t('storefront.product.purchase_protection_label') }}</p>
                            <p class="mt-2 text-sm font-medium text-gray-800 dark:text-gray-200">{{ t('storefront.product.purchase_protection_value') }}</p>
                        </div>
                    </div>

                    <p class="text-xs text-gray-400 dark:text-gray-500">{{ t('storefront.product.taxes_notice') }}</p>
                </div>
            </div>

            <!-- Description -->
            <div v-if="product.description" class="mb-14 rounded-3xl border border-gray-100 bg-white p-8 dark:border-gray-700 dark:bg-gray-800">
                <h2 class="mb-4 text-lg font-bold text-gray-900 dark:text-white">{{ t('storefront.product.description_label') }}</h2>
                <p class="text-sm leading-8 text-gray-600 dark:text-gray-300 whitespace-pre-line">{{ product.description }}</p>
            </div>

            <!-- Store Products -->
            <section v-if="store_products?.length > 0" class="mb-14">
                <div class="mb-5 flex items-center justify-between">
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white">
                        {{ product.store?.name ? `More from ${product.store.name}` : 'More Products' }}
                    </h2>
                    <Link v-if="product.store" :href="`/marketplace/stores/${product.store.slug}/products`" class="text-sm text-blue-600 dark:text-blue-400 hover:underline font-medium flex items-center gap-1">
                        {{ t('storefront.sections.view_all') }}
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </Link>
                </div>
                <StorefrontProductCarousel
                    :products="store_products"
                    :show-store="false"
                    @quick-view="openQuickView"
                />
            </section>

            <!-- Related Products -->
            <section v-if="related_products?.length > 0">
                <div class="mb-5 flex items-center justify-between">
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white">Related Products</h2>
                    <Link href="/marketplace/products" class="text-sm text-blue-600 dark:text-blue-400 hover:underline font-medium flex items-center gap-1">
                        {{ t('storefront.sections.view_all') }}
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </Link>
                </div>
                <StorefrontProductCarousel
                    :products="related_products"
                    @quick-view="openQuickView"
                />
            </section>
        </div>
    </StorefrontLayout>
</template>
