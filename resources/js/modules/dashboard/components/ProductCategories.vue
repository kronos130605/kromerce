<script setup>
import { ref } from 'vue';
import { useI18n } from 'vue-i18n';

const { t } = useI18n();

const categories = ref([
    {
        key: 'electronics',
        name: 'Electronics',
        icon: '👕',
        href: '#electronics'
    },
    {
        key: 'fashion',
        name: 'Fashion',
        icon: '👔',
        href: '#fashion'
    },
    {
        key: 'home_and_living',
        name: 'Home & Living',
        icon: '🏠',
        href: '#home'
    },
    {
        key: 'sports',
        name: 'Sports',
        icon: '⚽',
        href: '#sports'
    }
]);
</script>

<template>
    <div class="space-y-8">
        <div class="text-center mb-8">
            <h2 class="text-3xl font-bold text-gray-900 dark:text-white">{{ t('dashboard.shop_by_category') }}</h2>
            <p class="text-gray-600 dark:text-gray-300">{{ t('dashboard.browse_collections') }}</p>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
            <a 
                v-for="category in categories" 
                :key="category.name"
                :href="category.href"
                class="group block text-center p-6 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 hover:border-gray-300 dark:hover:border-gray-600 hover:shadow-lg transition-all duration-300"
            >
                <div class="w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-lg flex items-center justify-center mx-auto mb-4">
                    <span class="text-2xl">{{ category.icon }}</span>
                </div>
                <h3 class="text-sm font-medium text-gray-900 dark:text-white">{{ t(`dashboard.categories.${category.name.toLowerCase().replace(' & ', '_').replace(' ', '_')}`) }}</h3>
            </a>
        </div>
    </div>
</template>
