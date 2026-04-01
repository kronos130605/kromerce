<script setup>
import { Link } from '@inertiajs/vue3';
import { useTranslations } from '@/composables/useTranslations.js';

const { t } = useTranslations();

defineProps({
    category: {
        type: Object,
        required: true
    }
});
</script>

<template>
    <Link :href="`/marketplace/category/${category.slug}`" class="group block bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden hover:shadow-lg transition-all duration-300 hover:-translate-y-1">
        <!-- Category Image/Icon -->
        <div class="aspect-square bg-gradient-to-br from-blue-100 to-purple-100 dark:from-blue-900/30 dark:to-purple-900/30 flex items-center justify-center">
            <div v-if="category.image" class="w-full h-full">
                <img :src="category.image" :alt="category.name" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" />
            </div>
            <div v-else class="text-6xl">
                {{ category.icon || '📦' }}
            </div>
        </div>

        <!-- Category Info -->
        <div class="p-4 text-center">
            <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-1 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">
                {{ category.name }}
            </h3>
            <p class="text-xs text-gray-500 dark:text-gray-400">
                {{ t('storefront.category.products_count', { count: category.products_count || 0 }) }}
            </p>
        </div>
    </Link>
</template>
