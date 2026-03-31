<script setup>
import { ref, computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import { useCart } from '@/composables/useCart';
import { useTranslations } from '@/composables/useTranslations';

const { t } = useTranslations();
const { addToCart } = useCart();

const props = defineProps({
    product: {
        type: Object,
        required: true,
    },
    showStore: {
        type: Boolean,
        default: true,
    },
});

const emit = defineEmits(['quick-view']);

const added = ref(false);

const imageUrl = computed(() =>
    props.product.images?.[0]?.url
    || props.product.images?.[0]?.thumbnail_url
    || '/images/placeholder-product.png'
);

const hasDiscount = computed(() =>
    props.product.sale_price && props.product.sale_price < props.product.base_price
);

const discountPercentage = computed(() => {
    if (!hasDiscount.value) return 0;
    return Math.round(((props.product.base_price - props.product.sale_price) / props.product.base_price) * 100);
});

const displayPrice = computed(() =>
    hasDiscount.value ? props.product.sale_price : props.product.base_price
);

const isOutOfStock = computed(() => props.product.stock_quantity <= 0);

const ratingValue = computed(() => parseFloat(props.product.rating || 4.5));

const formatPrice = (price) =>
    new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: props.product.base_currency || 'USD',
    }).format(price);

const handleAddToCart = (e) => {
    e.preventDefault();
    if (isOutOfStock.value) return;
    addToCart(props.product, 1);
    added.value = true;
    setTimeout(() => { added.value = false; }, 2000);
};

const handleQuickView = (e) => {
    e.preventDefault();
    emit('quick-view', props.product);
};
</script>

<template>
    <div class="group bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 overflow-hidden hover:shadow-xl hover:-translate-y-0.5 transition-all duration-300">
        <Link :href="`/products/${product.slug}`" class="block">
            <!-- Image -->
            <div class="relative aspect-[4/3] overflow-hidden bg-gray-100 dark:bg-gray-700">
                <img
                    :src="imageUrl"
                    :alt="product.name"
                    class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                />

                <!-- Badges -->
                <div class="absolute top-2 left-2 flex flex-col gap-1">
                    <span v-if="hasDiscount" class="px-2 py-0.5 bg-red-500 text-white text-xs font-bold rounded-full shadow-sm">
                        -{{ discountPercentage }}%
                    </span>
                    <span v-if="product.is_new && !hasDiscount" class="px-2 py-0.5 bg-emerald-500 text-white text-xs font-bold rounded-full shadow-sm">
                        {{ t('storefront.product.new') }}
                    </span>
                    <span v-if="product.featured" class="px-2 py-0.5 bg-amber-500 text-white text-xs font-bold rounded-full shadow-sm">
                        {{ t('storefront.product.featured') }}
                    </span>
                </div>

                <!-- Hover Actions -->
                <div class="absolute top-2 right-2 flex flex-col gap-1.5 translate-x-10 group-hover:translate-x-0 opacity-0 group-hover:opacity-100 transition-all duration-300">
                    <!-- Quick View -->
                    <button
                        @click="handleQuickView"
                        :title="t('storefront.product.quick_view')"
                        class="w-8 h-8 bg-white dark:bg-gray-800 rounded-full flex items-center justify-center shadow-md hover:bg-blue-50 dark:hover:bg-blue-900/30 transition-colors"
                    >
                        <svg class="w-4 h-4 text-gray-600 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                    </button>
                    <!-- Wishlist -->
                    <button
                        :title="t('storefront.product.add_to_wishlist')"
                        class="w-8 h-8 bg-white dark:bg-gray-800 rounded-full flex items-center justify-center shadow-md hover:bg-red-50 dark:hover:bg-red-900/30 transition-colors"
                    >
                        <svg class="w-4 h-4 text-gray-600 dark:text-gray-300 hover:text-red-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                        </svg>
                    </button>
                </div>

                <!-- Out of Stock Overlay -->
                <div v-if="isOutOfStock" class="absolute inset-0 bg-black/40 flex items-center justify-center">
                    <span class="px-3 py-1 bg-white/90 dark:bg-gray-900/90 text-gray-800 dark:text-gray-200 text-xs font-bold rounded-full">
                        {{ t('storefront.product.out_of_stock') }}
                    </span>
                </div>
            </div>

            <!-- Content -->
            <div class="p-3">
                <!-- Store -->
                <div v-if="showStore && product.store" class="mb-1.5">
                    <span class="inline-flex items-center gap-1 text-xs text-blue-600 dark:text-blue-400 font-medium hover:underline">
                        <span class="w-3.5 h-3.5 rounded-full bg-gradient-to-br from-blue-500 to-purple-600 flex-shrink-0 inline-block"></span>
                        {{ product.store.name }}
                    </span>
                </div>

                <!-- Product Name -->
                <h3 class="text-sm font-semibold text-gray-900 dark:text-white line-clamp-2 mb-2 leading-snug group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">
                    {{ product.name }}
                </h3>

                <!-- Rating -->
                <div class="flex items-center gap-1.5 mb-2">
                    <div class="flex items-center">
                        <template v-for="i in 5" :key="i">
                            <svg
                                :class="i <= Math.round(ratingValue) ? 'text-amber-400' : 'text-gray-200 dark:text-gray-600'"
                                class="w-3 h-3 fill-current"
                                viewBox="0 0 20 20"
                            >
                                <path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/>
                            </svg>
                        </template>
                    </div>
                    <span class="text-xs text-gray-500 dark:text-gray-400">({{ ratingValue.toFixed(1) }})</span>
                    <span class="text-xs text-gray-400 dark:text-gray-500">• {{ product.sales_count || 0 }} {{ t('storefront.product.sold') }}</span>
                </div>

                <!-- Price -->
                <div class="flex items-baseline gap-1.5">
                    <span class="text-base font-bold text-gray-900 dark:text-white">
                        {{ formatPrice(displayPrice) }}
                    </span>
                    <span v-if="hasDiscount" class="text-xs text-gray-400 dark:text-gray-500 line-through">
                        {{ formatPrice(product.base_price) }}
                    </span>
                </div>
            </div>
        </Link>

        <!-- Add to Cart -->
        <div class="px-3 pb-3">
            <button
                @click="handleAddToCart"
                :disabled="isOutOfStock"
                :class="[
                    'w-full py-2 text-xs font-semibold rounded-lg flex items-center justify-center gap-1.5 transition-all duration-300',
                    added
                        ? 'bg-emerald-500 text-white'
                        : isOutOfStock
                            ? 'bg-gray-100 dark:bg-gray-700 text-gray-400 cursor-not-allowed'
                            : 'bg-blue-600 hover:bg-blue-700 active:scale-95 text-white'
                ]"
            >
                <svg v-if="!added" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
                <svg v-else class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                {{ added ? t('storefront.product.added') : t('storefront.product.add_to_cart') }}
            </button>
        </div>
    </div>
</template>
