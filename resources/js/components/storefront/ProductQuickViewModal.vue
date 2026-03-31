<script setup>
import { ref, computed, watch } from 'vue';
import { Link } from '@inertiajs/vue3';
import { useCart } from '@/composables/useCart';
import { useTranslations } from '@/composables/useTranslations';

const { t } = useTranslations();
const { addToCart, isInCart } = useCart();

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

const imageUrl = computed(() => {
    if (!props.product) return '/images/placeholder-product.png';
    const images = props.product.images || [];
    return images[activeImageIndex.value]?.url
        || images[activeImageIndex.value]?.thumbnail_url
        || '/images/placeholder-product.png';
});

const hasDiscount = computed(() => {
    if (!props.product) return false;
    return props.product.sale_price && props.product.sale_price < props.product.base_price;
});

const discountPercentage = computed(() => {
    if (!hasDiscount.value) return 0;
    return Math.round(((props.product.base_price - props.product.sale_price) / props.product.base_price) * 100);
});

const displayPrice = computed(() => {
    if (!props.product) return 0;
    return hasDiscount.value ? props.product.sale_price : props.product.base_price;
});

const formatPrice = (price) => {
    if (!props.product) return '';
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: props.product.base_currency || 'USD',
    }).format(price);
};

const isOutOfStock = computed(() => props.product?.stock_quantity <= 0);

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
                class="fixed inset-0 z-50 flex items-center justify-center p-4"
                @click.self="close"
            >
                <!-- Backdrop -->
                <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" @click="close" />

                <!-- Modal -->
                <div class="relative bg-white dark:bg-gray-900 rounded-2xl shadow-2xl w-full max-w-3xl max-h-[90vh] overflow-y-auto">
                    <!-- Close Button -->
                    <button
                        @click="close"
                        class="absolute top-4 right-4 z-10 w-9 h-9 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-full flex items-center justify-center transition-colors"
                    >
                        <svg class="w-5 h-5 text-gray-600 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>

                    <div class="grid grid-cols-1 md:grid-cols-2">
                        <!-- Image Section -->
                        <div class="p-6 bg-gray-50 dark:bg-gray-800 rounded-tl-2xl rounded-bl-2xl md:rounded-tr-none rounded-tr-2xl">
                            <!-- Main Image -->
                            <div class="aspect-square rounded-xl overflow-hidden bg-white dark:bg-gray-700 mb-3">
                                <img
                                    :src="imageUrl"
                                    :alt="product.name"
                                    class="w-full h-full object-cover"
                                />
                            </div>
                            <!-- Thumbnails -->
                            <div v-if="product.images?.length > 1" class="flex gap-2 overflow-x-auto pb-1">
                                <button
                                    v-for="(img, idx) in product.images.slice(0, 5)"
                                    :key="idx"
                                    @click="activeImageIndex = idx"
                                    :class="[
                                        'w-14 h-14 flex-shrink-0 rounded-lg overflow-hidden border-2 transition-colors',
                                        activeImageIndex === idx
                                            ? 'border-blue-500'
                                            : 'border-transparent hover:border-gray-300 dark:hover:border-gray-500'
                                    ]"
                                >
                                    <img
                                        :src="img.url || img.thumbnail_url"
                                        :alt="`${product.name} ${idx + 1}`"
                                        class="w-full h-full object-cover"
                                    />
                                </button>
                            </div>
                        </div>

                        <!-- Info Section -->
                        <div class="p-6 flex flex-col gap-4">
                            <!-- Badges -->
                            <div class="flex flex-wrap gap-2">
                                <span v-if="product.featured" class="px-2 py-0.5 bg-yellow-100 dark:bg-yellow-900/40 text-yellow-700 dark:text-yellow-400 text-xs font-semibold rounded-full">
                                    ⭐ {{ t('storefront.product.featured') }}
                                </span>
                                <span v-if="hasDiscount" class="px-2 py-0.5 bg-red-100 dark:bg-red-900/40 text-red-600 dark:text-red-400 text-xs font-semibold rounded-full">
                                    -{{ discountPercentage }}%
                                </span>
                                <span v-if="product.is_new" class="px-2 py-0.5 bg-green-100 dark:bg-green-900/40 text-green-700 dark:text-green-400 text-xs font-semibold rounded-full">
                                    {{ t('storefront.product.new') }}
                                </span>
                            </div>

                            <!-- Store -->
                            <div v-if="product.store" class="flex items-center gap-2">
                                <div class="w-6 h-6 rounded-full bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center flex-shrink-0">
                                    <span class="text-white text-xs font-bold">{{ product.store.name?.charAt(0) }}</span>
                                </div>
                                <Link
                                    :href="`/stores/${product.store.slug}`"
                                    @click="close"
                                    class="text-xs text-blue-600 dark:text-blue-400 hover:underline font-medium"
                                >
                                    {{ product.store.name }}
                                </Link>
                            </div>

                            <!-- Name -->
                            <h2 class="text-xl font-bold text-gray-900 dark:text-white leading-snug">
                                {{ product.name }}
                            </h2>

                            <!-- Rating & Sales -->
                            <div class="flex items-center gap-3 text-sm">
                                <div class="flex items-center gap-1">
                                    <template v-for="i in 5" :key="i">
                                        <svg
                                            :class="i <= Math.round(product.rating || 4.5) ? 'text-yellow-400' : 'text-gray-300 dark:text-gray-600'"
                                            class="w-4 h-4 fill-current"
                                            viewBox="0 0 20 20"
                                        >
                                            <path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/>
                                        </svg>
                                    </template>
                                    <span class="text-gray-600 dark:text-gray-400 ml-1">({{ product.rating || '4.5' }})</span>
                                </div>
                                <span class="text-gray-400">•</span>
                                <span class="text-gray-500 dark:text-gray-400">{{ product.sales_count || 0 }} {{ t('storefront.product.sold') }}</span>
                            </div>

                            <!-- Price -->
                            <div class="flex items-baseline gap-3">
                                <span class="text-3xl font-bold text-gray-900 dark:text-white">
                                    {{ formatPrice(displayPrice) }}
                                </span>
                                <span v-if="hasDiscount" class="text-lg text-gray-400 line-through">
                                    {{ formatPrice(product.base_price) }}
                                </span>
                            </div>

                            <!-- Description -->
                            <div v-if="product.description" class="text-sm text-gray-600 dark:text-gray-400 leading-relaxed border-t border-gray-100 dark:border-gray-700 pt-4">
                                <p class="line-clamp-4">{{ product.description }}</p>
                            </div>

                            <!-- Stock Status -->
                            <div>
                                <span v-if="isOutOfStock" class="inline-flex items-center gap-1.5 text-sm text-red-600 dark:text-red-400 font-medium">
                                    <span class="w-2 h-2 rounded-full bg-red-500 inline-block"></span>
                                    {{ t('storefront.product.out_of_stock') }}
                                </span>
                                <span v-else class="inline-flex items-center gap-1.5 text-sm text-green-600 dark:text-green-400 font-medium">
                                    <span class="w-2 h-2 rounded-full bg-green-500 inline-block"></span>
                                    {{ t('storefront.product.in_stock') }}
                                    <span v-if="product.stock_quantity < 10" class="text-orange-500 dark:text-orange-400">
                                        ({{ product.stock_quantity }} {{ t('storefront.product.left') }})
                                    </span>
                                </span>
                            </div>

                            <!-- Quantity + Add to Cart -->
                            <div class="flex items-center gap-3 mt-auto pt-2">
                                <!-- Quantity Selector -->
                                <div class="flex items-center border border-gray-200 dark:border-gray-600 rounded-lg overflow-hidden">
                                    <button
                                        @click="quantity = Math.max(1, quantity - 1)"
                                        :disabled="quantity <= 1"
                                        class="px-3 py-2 text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 disabled:opacity-40 transition-colors"
                                    >
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />
                                        </svg>
                                    </button>
                                    <span class="px-4 py-2 text-sm font-semibold text-gray-900 dark:text-white min-w-[2.5rem] text-center">{{ quantity }}</span>
                                    <button
                                        @click="quantity = Math.min(product.stock_quantity || 99, quantity + 1)"
                                        :disabled="product.stock_quantity > 0 && quantity >= product.stock_quantity"
                                        class="px-3 py-2 text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 disabled:opacity-40 transition-colors"
                                    >
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                        </svg>
                                    </button>
                                </div>

                                <!-- Add to Cart -->
                                <button
                                    @click="handleAddToCart"
                                    :disabled="isOutOfStock"
                                    :class="[
                                        'flex-1 py-2.5 rounded-lg text-sm font-semibold flex items-center justify-center gap-2 transition-all duration-300',
                                        added
                                            ? 'bg-green-500 text-white'
                                            : isOutOfStock
                                                ? 'bg-gray-200 dark:bg-gray-700 text-gray-400 cursor-not-allowed'
                                                : 'bg-blue-600 hover:bg-blue-700 text-white'
                                    ]"
                                >
                                    <svg v-if="!added" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                                    </svg>
                                    <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                    {{ added ? t('storefront.product.added') : t('storefront.product.add_to_cart') }}
                                </button>
                            </div>

                            <!-- View Full Details Link -->
                            <Link
                                :href="`/products/${product.slug}`"
                                @click="close"
                                class="text-center text-sm text-blue-600 dark:text-blue-400 hover:underline"
                            >
                                {{ t('storefront.product.view_full_details') }} →
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </Transition>
    </Teleport>
</template>
