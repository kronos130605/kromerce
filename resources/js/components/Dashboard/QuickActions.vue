<script setup>
import { ref } from 'vue';
import { useI18n } from 'vue-i18n';

const { t } = useI18n();

const quickActions = ref([
    {
        title: 'my_orders',
        description: 'track_purchases',
        icon: '📦',
        href: '#orders'
    },
    {
        title: 'wishlist',
        description: 'saved_items',
        icon: '❤️',
        href: '#wishlist'
    },
    {
        title: 'profile_settings',
        description: 'account_preferences',
        icon: '👤',
        href: '#profile'
    }
]);
</script>

<template>
    <div class="bg-gray-50 rounded-2xl p-8">
        <div class="max-w-7xl mx-auto">
            <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-8">{{ t('dashboard.quick_actions') }}</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <a 
                    v-for="action in quickActions" 
                    :key="action.title"
                    :href="action.href"
                    class="group block p-6 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 hover:border-gray-300 dark:hover:border-gray-600 hover:shadow-lg transition-all duration-300"
                >
                    <div class="text-center mb-4">
                        <div class="w-12 h-12 bg-gray-100 dark:bg-gray-700 rounded-lg flex items-center justify-center mx-auto">
                            <span class="text-2xl">{{ action.icon }}</span>
                        </div>
                    </div>
                    <h4 class="text-base font-medium text-gray-900 dark:text-white group-hover:text-black dark:hover:text-white transition-colors">{{ t(`dashboard.${action.title}`) }}</h4>
                    <p class="text-sm text-gray-600 dark:text-gray-300">{{ t(`dashboard.${action.description}`) }}</p>
                </a>
            </div>
        </div>
    </div>
</template>
