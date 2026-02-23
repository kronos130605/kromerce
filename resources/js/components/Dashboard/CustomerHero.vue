<script setup>
import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';

const page = usePage();
const user = computed(() => page.props.auth.user);
const { t } = useI18n();
</script>

<template>
    <div class="relative bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-900 dark:to-gray-800">
        <div class="relative px-8 py-16">
            <div class="max-w-7xl mx-auto">
                <div class="text-center space-y-8">
                    <!-- Welcome Message -->
                    <div class="space-y-4">
                        <h1 class="text-5xl font-light text-gray-900 dark:text-white tracking-tight">
                            {{ t('dashboard.welcome') }}, {{ user.name }}
                        </h1>
                        <p class="text-xl text-gray-600 dark:text-gray-300 max-w-2xl mx-auto">
                            {{ t('dashboard.discover_products') }}
                        </p>
                    </div>
                    
                    <!-- User Stats -->
                    <div class="flex items-center justify-center space-x-8">
                        <div class="text-center">
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ t('dashboard.available_balance') }}</p>
                            <p class="text-3xl font-bold text-gray-900 dark:text-white">$250.00</p>
                        </div>
                        <div class="text-center">
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ t('dashboard.vip_member') }}</p>
                            <div class="px-4 py-2 bg-black dark:bg-white dark:text-black text-white dark:text-black text-sm font-medium rounded-full">
                                {{ t('dashboard.exclusive_access') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
