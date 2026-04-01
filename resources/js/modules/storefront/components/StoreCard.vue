<script setup>
import { Link } from '@inertiajs/vue3';
import { useTranslations } from '@/composables/useTranslations.js';

const { t } = useTranslations();

defineProps({
    store: {
        type: Object,
        required: true
    }
});
</script>

<template>
    <Link :href="`/marketplace/stores/${store.slug}`" class="group block bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden hover:shadow-lg transition-shadow duration-300">
        <!-- Store Banner/Cover -->
        <div class="h-24 bg-gradient-to-r from-blue-500 to-purple-600"></div>

        <!-- Store Info -->
        <div class="p-4 -mt-12">
            <!-- Logo -->
            <div class="w-20 h-20 bg-white dark:bg-gray-800 rounded-lg border-4 border-white dark:border-gray-800 shadow-md mb-3 flex items-center justify-center overflow-hidden">
                <img v-if="store.logo" :src="store.logo" :alt="store.name" class="w-full h-full object-cover" />
                <div v-else class="w-full h-full bg-gradient-to-br from-blue-600 to-purple-600 flex items-center justify-center">
                    <span class="text-white font-bold text-2xl">{{ store.name.charAt(0) }}</span>
                </div>
            </div>

            <!-- Store Name -->
            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-1 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">
                {{ store.name }}
            </h3>

            <!-- Store Description -->
            <p v-if="store.description" class="text-sm text-gray-600 dark:text-gray-400 line-clamp-2 mb-3">
                {{ store.description }}
            </p>

            <!-- Stats -->
            <div class="flex items-center gap-4 text-xs text-gray-500 dark:text-gray-400 mb-3">
                <div class="flex items-center gap-1">
                    <svg class="w-4 h-4 text-yellow-400 fill-current" viewBox="0 0 20 20">
                        <path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/>
                    </svg>
                    <span>{{ store.rating || '4.5' }}</span>
                </div>
                <span>•</span>
                <span>{{ t('storefront.store.products_count', { count: store.products_count || 0 }) }}</span>
            </div>

            <!-- Badges -->
            <div class="flex flex-wrap gap-2">
                <span v-if="store.verified" class="inline-flex items-center gap-1 px-2 py-1 bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400 text-xs font-medium rounded">
                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    {{ t('storefront.store.verified') }}
                </span>
                <span v-if="store.top_seller" class="px-2 py-1 bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-400 text-xs font-medium rounded">
                    ⭐ {{ t('storefront.store.top_seller') }}
                </span>
            </div>

            <!-- Visit Store Button -->
            <button class="w-full mt-4 py-2 border border-blue-600 text-blue-600 dark:border-blue-500 dark:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/30 text-sm font-medium rounded-lg transition-colors">
                {{ t('storefront.store.visit_store') }}
            </button>
        </div>
    </Link>
</template>
