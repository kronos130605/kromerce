<script setup>
import { computed, ref, watch } from 'vue';
import { Link } from '@inertiajs/vue3';
import { useCart } from '@/composables/useCart';
import { useProductPresentation } from '@/composables/useProductPresentation';
import { useTranslations } from '@/composables/useTranslations';

const { t } = useTranslations();
const { addToCart } = useCart();

const props = defineProps({
    product: {
        type: Object,
        default: null,
    },
    show: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(['close']);

const quantity = ref(1);
const added = ref(false);
const activeImageIndex = ref(0);

watch(() => props.product, () => {
    quantity.value = 1;
    added.value = false;
    activeImageIndex.value = 0;
});

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

const imageUrl = computed(() =>
    gallery.value[activeImageIndex.value]?.url
    || gallery.value[activeImageIndex.value]?.thumbnail_url
    || '/images/placeholder-product.png'
);

const productHighlights = computed(() => {
    if (!props.product) return [];

    return [
        {
            label: t('storefront.product.condition_label'),
            value: props.product.condition || t('storefront.product.condition_new'),
        },
        {
            label: t('storefront.product.availability_label'),
            value: isOutOfStock.value
                ? t('storefront.product.out_of_stock')
                : `${props.product.stock_quantity || 0} ${t('storefront.product.units_available')}`,
        },
        {
            label: t('storefront.product.store_label'),
            value: props.product.store?.name || 'Kromerce',
        },
    ];
});

const handleAddToCart = () => {
    if (!props.product || isOutOfStock.value) return;
    addToCart(props.product, quantity.value);
    added.value = true;
    setTimeout(() => { added.value = false; }, 2000);
};

const close = () => emit('close');
</script>

<template>
    <Teleport to="body">
        <Transition
            enter-active-class="transition ease-out duration-200"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="transition ease-in duration-150"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div
                v-if="show && product"
                class="fixed inset-0 z-[60] flex items-center justify-center p-4"
                @click.self="close"
            >
                <div class="absolute inset-0 bg-slate-950/70 backdrop-blur-sm" @click="close" />

                <div class="relative w-full max-w-6xl max-h-[92vh] overflow-y-auto rounded-[2rem] bg-white shadow-2xl dark:bg-gray-900">
                    <button
                        @click="close"
                        class="absolute right-5 top-5 z-10 flex h-11 w-11 items-center justify-center rounded-full border border-white/20 bg-white/90 text-gray-700 shadow-lg backdrop-blur transition-colors hover:bg-white dark:border-gray-700 dark:bg-gray-800/90 dark:text-gray-200 dark:hover:bg-gray-800"
                    >
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>

                    <div class="grid grid-cols-1 lg:grid-cols-[1.1fr_0.9fr]">
                        <div class="bg-gradient-to-br from-slate-50 via-white to-blue-50/70 p-6 dark:from-gray-800 dark:via-gray-900 dark:to-blue-950/30">
                            <div class="overflow-hidden rounded-[1.5rem] border border-white/70 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">
                                <div class="aspect-[4/3] overflow-hidden bg-gray-100 dark:bg-gray-700">
                                    <img
                                        :src="imageUrl"
                                        :alt="product.name"
                                        class="h-full w-full object-cover"
                                    />
                                </div>
                            </div>

                            <div class="mt-4 flex gap-3 overflow-x-auto pb-1">
                                <button
                                    v-for="(img, idx) in gallery"
                                    :key="idx"
                                    @click="activeImageIndex = idx"
                                    :class="[
                                        'h-20 w-20 flex-shrink-0 overflow-hidden rounded-2xl border-2 bg-white transition-all dark:bg-gray-800',
                                        activeImageIndex === idx
                                            ? 'border-blue-500 shadow-md shadow-blue-500/20'
                                            : 'border-transparent hover:border-gray-300 dark:hover:border-gray-600'
                                    ]"
                                >
                                    <img
                                        :src="img.url || img.thumbnail_url"
                                        :alt="`${product.name} ${idx + 1}`"
                                        class="h-full w-full object-cover"
                                    />
                                </button>
                            </div>

                            <div class="mt-6 grid gap-3 sm:grid-cols-3">
                                <div
                                    v-for="item in productHighlights"
                                    :key="item.label"
                                    class="rounded-2xl border border-gray-100 bg-white/90 p-4 shadow-sm dark:border-gray-700 dark:bg-gray-800/80"
                                >
                                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-gray-400 dark:text-gray-500">
                                        {{ item.label }}
                                    </p>
                                    <p class="mt-2 text-sm font-semibold text-gray-900 dark:text-white">
                                        {{ item.value }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="flex flex-col p-6 lg:p-8">
                            <div class="flex flex-wrap items-center gap-2">
                                <span v-if="product.featured" class="rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold text-amber-700 dark:bg-amber-900/40 dark:text-amber-300">
                                    {{ t('storefront.product.featured') }}
                                </span>
                                <span v-if="product.is_new" class="rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300">
                                    {{ t('storefront.product.new') }}
                                </span>
                                <span v-if="hasDiscount" class="rounded-full bg-rose-100 px-3 py-1 text-xs font-semibold text-rose-700 dark:bg-rose-900/40 dark:text-rose-300">
                                    -{{ discountPercentage }}% {{ t('storefront.product.sale') }}
                                </span>
                                <span class="rounded-full bg-blue-50 px-3 py-1 text-xs font-semibold text-blue-700 dark:bg-blue-900/30 dark:text-blue-300">
                                    {{ stockLabel }}
                                </span>
                            </div>

                            <div class="mt-5 flex items-start justify-between gap-4">
                                <div>
                                    <p v-if="product.store?.name" class="text-sm font-medium text-blue-600 dark:text-blue-400">
                                        {{ product.store.name }}
                                    </p>
                                    <h2 class="mt-1 text-3xl font-bold tracking-tight text-gray-900 dark:text-white">
                                        {{ product.name }}
                                    </h2>
                                </div>
                            </div>

                            <div class="mt-4 flex flex-wrap items-center gap-4 text-sm text-gray-500 dark:text-gray-400">
                                <div class="flex items-center gap-2">
                                    <div class="flex items-center">
                                        <template v-for="i in 5" :key="i">
                                            <svg
                                                :class="i <= Math.round(ratingValue) ? 'text-amber-400' : 'text-gray-200 dark:text-gray-700'"
                                                class="h-4 w-4 fill-current"
                                                viewBox="0 0 20 20"
                                            >
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

                            <div class="mt-6 rounded-3xl border border-gray-100 bg-gray-50 p-5 dark:border-gray-800 dark:bg-gray-800/80">
                                <div class="flex flex-wrap items-end gap-3">
                                    <span class="text-4xl font-bold tracking-tight text-gray-900 dark:text-white">
                                        {{ formatPrice(displayPrice) }}
                                    </span>
                                    <span v-if="hasDiscount" class="text-lg text-gray-400 line-through">
                                        {{ formatPrice(product.base_price) }}
                                    </span>
                                </div>
                                <p v-if="hasDiscount" class="mt-2 text-sm font-medium text-emerald-600 dark:text-emerald-400">
                                    {{ t('storefront.product.you_save') }} {{ formatPrice(savingsAmount) }}
                                </p>
                            </div>

                            <div v-if="product.description" class="mt-6 rounded-3xl border border-gray-100 p-5 dark:border-gray-800">
                                <h3 class="text-sm font-semibold uppercase tracking-[0.16em] text-gray-400 dark:text-gray-500">
                                    {{ t('storefront.product.description_label') }}
                                </h3>
                                <p class="mt-3 text-sm leading-7 text-gray-600 dark:text-gray-300">
                                    {{ product.description }}
                                </p>
                            </div>

                            <div class="mt-6 grid gap-3 sm:grid-cols-2">
                                <div class="rounded-2xl border border-gray-100 p-4 dark:border-gray-800">
                                    <p class="text-xs font-semibold uppercase tracking-[0.16em] text-gray-400 dark:text-gray-500">
                                        {{ t('storefront.product.shipping_label') }}
                                    </p>
                                    <p class="mt-2 text-sm font-medium text-gray-800 dark:text-gray-200">
                                        {{ t('storefront.product.shipping_value') }}
                                    </p>
                                </div>
                                <div class="rounded-2xl border border-gray-100 p-4 dark:border-gray-800">
                                    <p class="text-xs font-semibold uppercase tracking-[0.16em] text-gray-400 dark:text-gray-500">
                                        {{ t('storefront.product.purchase_protection_label') }}
                                    </p>
                                    <p class="mt-2 text-sm font-medium text-gray-800 dark:text-gray-200">
                                        {{ t('storefront.product.purchase_protection_value') }}
                                    </p>
                                </div>
                            </div>

                            <div class="mt-8 flex flex-col gap-4 sm:flex-row sm:items-center">
                                <div class="flex items-center rounded-2xl border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-800">
                                    <button
                                        @click="quantity = Math.max(1, quantity - 1)"
                                        :disabled="quantity <= 1"
                                        class="px-4 py-3 text-gray-600 transition-colors hover:bg-gray-50 disabled:opacity-40 dark:text-gray-300 dark:hover:bg-gray-700"
                                    >
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />
                                        </svg>
                                    </button>
                                    <span class="min-w-[3rem] px-3 text-center text-sm font-semibold text-gray-900 dark:text-white">{{ quantity }}</span>
                                    <button
                                        @click="quantity = Math.min(product.stock_quantity || 99, quantity + 1)"
                                        :disabled="product.stock_quantity > 0 && quantity >= product.stock_quantity"
                                        class="px-4 py-3 text-gray-600 transition-colors hover:bg-gray-50 disabled:opacity-40 dark:text-gray-300 dark:hover:bg-gray-700"
                                    >
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                        </svg>
                                    </button>
                                </div>

                                <button
                                    @click="handleAddToCart"
                                    :disabled="isOutOfStock"
                                    :class="[
                                        'inline-flex flex-1 items-center justify-center gap-2 rounded-2xl px-6 py-3.5 text-sm font-semibold transition-all duration-300',
                                        added
                                            ? 'bg-emerald-500 text-white'
                                            : isOutOfStock
                                                ? 'cursor-not-allowed bg-gray-200 text-gray-400 dark:bg-gray-700'
                                                : 'bg-blue-600 text-white hover:bg-blue-700'
                                    ]"
                                >
                                    <svg v-if="!added" class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                                    </svg>
                                    <svg v-else class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                    {{ added ? t('storefront.product.added') : t('storefront.product.add_to_cart') }}
                                </button>
                            </div>

                            <div class="mt-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                                <Link
                                    :href="`/products/${product.slug}`"
                                    @click="close"
                                    class="inline-flex items-center gap-2 text-sm font-semibold text-blue-600 transition-colors hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300"
                                >
                                    {{ t('storefront.product.view_full_details') }}
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                    </svg>
                                </Link>
                                <p class="text-xs text-gray-400 dark:text-gray-500">
                                    {{ t('storefront.product.taxes_notice') }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </Transition>
    </Teleport>
</template>
