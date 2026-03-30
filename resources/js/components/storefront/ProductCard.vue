<script setup>
import { Link } from '@inertiajs/vue3';
import { computed } from 'vue';
import { useTranslations } from '@/composables/useTranslations';

const { t } = useTranslations();

const props = defineProps({
    product: {
        type: Object,
        required: true
    },
    showStore: {
        type: Boolean,
        default: true
    }
});

const imageUrl = computed(() => {
    return props.product.images?.[0]?.url || props.product.images?.[0]?.thumbnail_url || '/images/placeholder-product.png';
});

const hasDiscount = computed(() => {
    return props.product.sale_price && props.product.sale_price < props.product.base_price;
});

const discountPercentage = computed(() => {
    if (!hasDiscount.value) return 0;
    return Math.round(((props.product.base_price - props.product.sale_price) / props.product.base_price) * 100);
});

const displayPrice = computed(() => {
    return hasDiscount.value ? props.product.sale_price : props.product.base_price;
});

const formatPrice = (price) => {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: props.product.base_currency || 'USD'
    }).format(price);
};
</script>

<template>
    <div class="group bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden hover:shadow-lg transition-shadow duration-300">
        <Link :href="`/products/${product.slug}`" class="block">
            <!-- Image -->
            <div class="relative aspect-square overflow-hidden bg-gray-100 dark:bg-gray-700">
                <img
                    :src="imageUrl"
                    :alt="product.name"
                    class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                />
                
                <!-- Badges -->
                <div class="absolute top-2 left-2 flex flex-col gap-1">
                    <span v-if="product.featured" class="px-2 py-1 bg-yellow-500 text-white text-xs font-bold rounded">
                        ⭐ {{ t('storefront.product.featured') }}
                    </span>
                    <span v-if="hasDiscount" class="px-2 py-1 bg-red-500 text-white text-xs font-bold rounded">
                        -{{ discountPercentage }}%
                    </span>
                    <span v-if="product.is_new" class="px-2 py-1 bg-green-500 text-white text-xs font-bold rounded">
                        {{ t('storefront.product.new') }}
                    </span>
                </div>

                <!-- Quick Actions -->
                <div class="absolute top-2 right-2 flex flex-col gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                    <button class="w-8 h-8 bg-white dark:bg-gray-800 rounded-full flex items-center justify-center shadow-md hover:bg-red-50 dark:hover:bg-red-900/30 transition-colors">
                        <svg class="w-4 h-4 text-gray-700 dark:text-gray-300 hover:text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Content -->
            <div class="p-4">
                <!-- Store Name -->
                <div v-if="showStore && product.store" class="mb-2">
                    <Link :href="`/stores/${product.store.slug}`" class="text-xs text-gray-500 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400">
                        {{ product.store.name }}
                    </Link>
                </div>

                <!-- Product Name -->
                <h3 class="text-sm font-medium text-gray-900 dark:text-white line-clamp-2 mb-2 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">
                    {{ product.name }}
                </h3>

                <!-- Price -->
                <div class="flex items-baseline gap-2 mb-2">
                    <span class="text-lg font-bold text-gray-900 dark:text-white">
                        {{ formatPrice(displayPrice) }}
                    </span>
                    <span v-if="hasDiscount" class="text-sm text-gray-500 dark:text-gray-400 line-through">
                        {{ formatPrice(product.base_price) }}
                    </span>
                </div>

                <!-- Rating & Sales -->
                <div class="flex items-center gap-3 text-xs text-gray-500 dark:text-gray-400">
                    <div class="flex items-center gap-1">
                        <svg class="w-4 h-4 text-yellow-400 fill-current" viewBox="0 0 20 20">
                            <path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/>
                        </svg>
                        <span>{{ product.rating || '4.5' }}</span>
                    </div>
                    <span>•</span>
                    <span>{{ product.sales_count || 0 }} sold</span>
                </div>

                <!-- Stock Status -->
                <div v-if="product.stock_quantity <= 0" class="mt-2">
                    <span class="text-xs text-red-600 dark:text-red-400 font-medium">{{ t('storefront.product.out_of_stock') }}</span>
                </div>
                <div v-else-if="product.stock_quantity < 10" class="mt-2">
                    <span class="text-xs text-orange-600 dark:text-orange-400 font-medium">{{ t('storefront.product.in_stock') }}</span>
                </div>
            </div>
        </Link>

        <!-- Add to Cart Button -->
        <div class="px-4 pb-4">
            <button class="w-full py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors flex items-center justify-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
                {{ t('storefront.product.add_to_cart') }}
            </button>
        </div>
    </div>
</template>
