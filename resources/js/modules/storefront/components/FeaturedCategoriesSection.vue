<script setup>
import { Link } from '@inertiajs/vue3';
import CategoryCard from '@/modules/storefront/components/CategoryCard.vue';
import { useTranslations } from '@/composables/useTranslations';

const { t } = useTranslations();

defineProps({
    categories: {
        type: Array,
        default: () => [],
    },
});
</script>

<template>
    <section class="max-w-7xl mx-auto px-4 py-10">
        <div class="flex items-center justify-between mb-5">
            <h2 class="text-xl font-bold text-gray-900 dark:text-white">
                {{ t('storefront.sections.featured_categories') }}
            </h2>
            <Link
                href="/products"
                class="text-sm text-blue-600 dark:text-blue-400 hover:underline font-medium flex items-center gap-1"
            >
                {{ t('storefront.sections.view_all') }}
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </Link>
        </div>

        <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-6 lg:grid-cols-8 gap-3">
            <CategoryCard
                v-for="category in categories"
                :key="category.id"
                :category="category"
            />
        </div>
    </section>
</template>
