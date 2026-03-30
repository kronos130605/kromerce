<script setup>
import { useTranslations } from '@/composables/useTranslations';

const { t } = useTranslations();
</script>

<template>
    <div class="space-y-8">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700 p-8">
            <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">{{ t('dashboard.nav_analytics') }}</h3>
            <div class="text-center py-12">
                <div class="text-6xl mb-4">📊</div>
                <p class="text-gray-600 dark:text-gray-400">{{ t('dashboard.analytics_coming_soon') }}</p>
            </div>
        </div>
    </div>
</template>
