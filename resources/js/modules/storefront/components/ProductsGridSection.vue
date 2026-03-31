<script setup>
import { Link } from '@inertiajs/vue3';
import ProductCard from '@/components/storefront/ProductCard.vue';
import { useTranslations } from '@/composables/useTranslations';

const { t } = useTranslations();

defineProps({
    title: {
        type: String,
        required: true,
    },
    icon: {
        type: String,
        default: '',
    },
    products: {
        type: Array,
        default: () => [],
    },
    viewAllLink: {
        type: String,
        default: '/products',
    },
    bgClass: {
        type: String,
        default: '',
    },
});

const emit = defineEmits(['quick-view']);
</script>

<template>
    <section :class="['py-10', bgClass]">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex items-center justify-between mb-5">
                <h2 class="text-xl font-bold text-gray-900 dark:text-white flex items-center gap-2">
                    <span v-if="icon" class="text-2xl">{{ icon }}</span>
                    {{ title }}
                </h2>
                <Link
                    :href="viewAllLink"
                    class="text-sm text-blue-600 dark:text-blue-400 hover:underline font-medium flex items-center gap-1"
                >
                    {{ t('storefront.sections.view_all') }}
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </Link>
            </div>

            <div
                v-if="products?.length > 0"
                class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-4"
            >
                <ProductCard
                    v-for="product in products"
                    :key="product.id"
                    :product="product"
                    @quick-view="(p) => emit('quick-view', p)"
                />
            </div>

            <div v-else class="py-12 text-center text-gray-400 dark:text-gray-600 text-sm">
                {{ t('storefront.empty.no_products') }}
            </div>
        </div>
    </section>
</template>
