<script setup>
import { ref, computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import { useCart } from '@/composables/useCart.js';
import { useProductPresentation } from '@/composables/useProductPresentation.js';
import { useTranslations } from '@/composables/useTranslations.js';

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

const emit = defineEmits(['quick-view', 'details-view']);

const added = ref(false);

const productRef = computed(() => props.product);
const {
    primaryImageUrl,
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

const handleDetailsView = (e) => {
    e.preventDefault();
    emit('details-view', props.product);
};
</script>

<template>
    <div class="group flex h-full w-[280px] flex-shrink-0 snap-start flex-col overflow-hidden rounded-[1.35rem] border border-gray-100 bg-white shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-2xl hover:shadow-blue-500/10 sm:w-[300px] lg:w-[320px] dark:border-gray-700 dark:bg-gray-800">
        <Link :href="`/products/${product.id}`" class="flex flex-1 flex-col">
            <div class="relative aspect-[4/3] overflow-hidden bg-gray-100 dark:bg-gray-700">
                <img
                    :src="primaryImageUrl"
                    :alt="product.name"
                    class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105"
                />
                <div class="absolute inset-x-0 bottom-0 h-24 bg-gradient-to-t from-black/50 via-black/10 to-transparent"></div>

                <div class="absolute top-3 left-3 flex max-w-[70%] flex-wrap gap-1.5">
                    <span v-if="hasDiscount" class="rounded-full bg-rose-500 px-2.5 py-1 text-[11px] font-bold text-white shadow-sm">
                        -{{ discountPercentage }}%
                    </span>
                    <span v-if="product.is_new && !hasDiscount" class="rounded-full bg-emerald-500 px-2.5 py-1 text-[11px] font-bold text-white shadow-sm">
                        {{ t('storefront.product.new') }}
                    </span>
                    <span v-if="product.featured" class="rounded-full bg-amber-500 px-2.5 py-1 text-[11px] font-bold text-white shadow-sm">
                        {{ t('storefront.product.featured') }}
                    </span>
                </div>

                <div class="absolute top-3 right-3 flex flex-col gap-2 translate-x-10 opacity-0 transition-all duration-300 group-hover:translate-x-0 group-hover:opacity-100">
                    <button
                        @click="handleQuickView"
                        :title="t('storefront.product.quick_view')"
                        class="flex h-9 w-9 items-center justify-center rounded-full bg-white/95 shadow-lg transition-colors hover:bg-blue-50 dark:bg-gray-800/95 dark:hover:bg-blue-900/30"
                    >
                        <svg class="h-4 w-4 text-gray-600 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                    </button>
                    <button
                        @click="handleDetailsView"
                        :title="t('storefront.product.view_details')"
                        class="flex h-9 w-9 items-center justify-center rounded-full bg-white/95 shadow-lg transition-colors hover:bg-indigo-50 dark:bg-gray-800/95 dark:hover:bg-indigo-900/30"
                    >
                        <svg class="h-4 w-4 text-gray-600 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </button>
                </div>

                <div v-if="isOutOfStock" class="absolute inset-0 flex items-center justify-center bg-black/40">
                    <span class="rounded-full bg-white/90 px-3 py-1 text-xs font-bold text-gray-800 dark:bg-gray-900/90 dark:text-gray-200">
                        {{ t('storefront.product.out_of_stock') }}
                    </span>
                </div>
                <div v-else class="absolute bottom-3 left-3 right-3 flex items-center justify-between rounded-2xl bg-white/90 px-3 py-2 text-xs shadow-lg backdrop-blur dark:bg-gray-900/80">
                    <span class="font-semibold text-gray-800 dark:text-gray-100">{{ stockLabel }}</span>
                    <span class="text-gray-500 dark:text-gray-400">{{ product.sales_count || 0 }} {{ t('storefront.product.sold') }}</span>
                </div>
            </div>

            <div class="flex-1 p-4">
                <div v-if="showStore && product.store" class="mb-2">
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-blue-50 px-2.5 py-1 text-[11px] font-semibold text-blue-700 dark:bg-blue-900/30 dark:text-blue-300">
                        <span class="inline-block h-3.5 w-3.5 flex-shrink-0 rounded-full bg-gradient-to-br from-blue-500 to-purple-600"></span>
                        {{ product.store.name }}
                    </span>
                </div>

                <h3 class="mb-2 line-clamp-2 text-sm font-semibold leading-snug text-gray-900 transition-colors group-hover:text-blue-600 dark:text-white dark:group-hover:text-blue-400">
                    {{ product.name }}
                </h3>

                <div class="mb-3 flex items-center gap-1.5">
                    <div class="flex items-center">
                        <template v-for="i in 5" :key="i">
                            <svg
                                :class="i <= Math.round(ratingValue) ? 'text-amber-400' : 'text-gray-200 dark:text-gray-600'"
                                class="h-3 w-3 fill-current"
                                viewBox="0 0 20 20"
                            >
                                <path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/>
                            </svg>
                        </template>
                    </div>
                    <span class="text-xs font-medium text-gray-600 dark:text-gray-300">{{ ratingValue.toFixed(1) }}</span>
                    <span class="text-xs text-gray-400 dark:text-gray-500">•</span>
                    <span class="text-xs text-gray-500 dark:text-gray-400">{{ reviewCount }} {{ t('storefront.product.reviews') }}</span>
                </div>

                <div class="flex items-end justify-between gap-3">
                    <div class="flex items-baseline gap-1.5">
                        <span class="text-lg font-bold text-gray-900 dark:text-white">
                            {{ formatPrice(displayPrice) }}
                        </span>
                        <span v-if="hasDiscount" class="text-xs text-gray-400 line-through dark:text-gray-500">
                            {{ formatPrice(product.base_price) }}
                        </span>
                    </div>
                    <span v-if="hasDiscount" class="rounded-full bg-emerald-50 px-2 py-1 text-[11px] font-semibold text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300">
                        {{ t('storefront.product.save_short') }} {{ formatPrice(savingsAmount) }}
                    </span>
                </div>

                <div class="mt-3 flex flex-wrap gap-2">
                    <span class="rounded-full bg-gray-100 px-2.5 py-1 text-[11px] font-medium text-gray-600 dark:bg-gray-700 dark:text-gray-300">
                        {{ product.base_currency || 'USD' }}
                    </span>
                    <span class="rounded-full bg-gray-100 px-2.5 py-1 text-[11px] font-medium text-gray-600 dark:bg-gray-700 dark:text-gray-300">
                        {{ isOutOfStock ? t('storefront.product.out_of_stock') : t('storefront.product.in_stock') }}
                    </span>
                </div>
            </div>
        </Link>

        <div class="border-t border-gray-100 px-4 py-3 dark:border-gray-700">
            <div class="flex items-center gap-2">
                <button
                    @click="handleDetailsView"
                    class="inline-flex flex-1 items-center justify-center gap-1.5 rounded-xl border border-gray-200 px-3 py-2 text-xs font-semibold text-gray-700 transition-colors hover:border-blue-300 hover:text-blue-600 dark:border-gray-600 dark:text-gray-200 dark:hover:border-blue-500 dark:hover:text-blue-400"
                >
                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    {{ t('storefront.product.view_details') }}
                </button>
                <button
                    @click="handleAddToCart"
                    :disabled="isOutOfStock"
                    :class="[
                        'inline-flex flex-1 items-center justify-center gap-1.5 rounded-xl px-3 py-2 text-xs font-semibold transition-all duration-300',
                        added
                            ? 'bg-emerald-500 text-white'
                            : isOutOfStock
                                ? 'cursor-not-allowed bg-gray-100 text-gray-400 dark:bg-gray-700'
                                : 'bg-blue-600 text-white hover:bg-blue-700 active:scale-95'
                    ]"
                >
                    <svg v-if="!added" class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    <svg v-else class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    <span>{{ added ? t('storefront.product.added') : t('storefront.product.add_to_cart') }}</span>
                </button>
            </div>
        </div>
    </div>
</template>
