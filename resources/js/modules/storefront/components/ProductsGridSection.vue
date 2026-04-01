<script setup>
import { Link } from '@inertiajs/vue3';
import StorefrontProductCarousel from '@/components/storefront/StorefrontProductCarousel.vue';
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

const emit = defineEmits(['quick-view', 'details-view']);
</script>

<template>
    <section :class="['py-10', bgClass]">
        <div class="max-w-7xl mx-auto px-4">
            <div class="mb-5 flex items-center justify-between gap-4">
                <h2 class="text-xl font-bold text-gray-900 dark:text-white flex items-center gap-2">
                    <span v-if="icon" class="text-2xl">{{ icon }}</span>
                    {{ title }}
                </h2>
                <div class="flex items-center gap-2">
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
            </div>

            <StorefrontProductCarousel
                v-if="products?.length > 0"
                :products="products"
                :show-arrows="true"
                arrows-class="absolute right-0 -top-14"
                @quick-view="(p) => emit('quick-view', p)"
                @details-view="(p) => emit('details-view', p)"
            />

            <div v-else class="py-12 text-center text-gray-400 dark:text-gray-600 text-sm">
                {{ t('storefront.empty.no_products') }}
            </div>
        </div>
    </section>
</template>
