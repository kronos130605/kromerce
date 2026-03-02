<script setup>
import { computed } from 'vue';
import { useI18n } from 'vue-i18n';

const props = defineProps({
    currencyStatus: {
        type: Object,
        default: () => ({})
    }
});

const { t } = useI18n();

// Mock data for now
const mockCurrencyStatus = computed(() => ({
    baseCurrency: 'USD',
    supportedCurrencies: ['USD', 'MXN', 'EUR', 'GBP'],
    lastUpdated: new Date().toISOString(),
    rates: {
        USD: 1.00,
        MXN: 17.50,
        EUR: 0.85,
        GBP: 0.73
    }
}));

const currencyData = computed(() => ({
    ...mockCurrencyStatus.value,
    ...props.currencyStatus
}));
</script>

<template>
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md border border-gray-200 dark:border-gray-700 p-6">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                {{ t('dashboard.currency_status') }}
            </h3>
            <div class="flex items-center space-x-2">
                <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
                <span class="text-sm text-gray-500 dark:text-gray-400">
                    {{ t('dashboard.live_rates') }}
                </span>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <div v-for="currency in currencyData.supportedCurrencies" :key="currency" 
                 class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm font-medium text-gray-600 dark:text-gray-300">
                        {{ currency }}
                    </span>
                    <div v-if="currency === currencyData.baseCurrency" 
                         class="px-2 py-1 bg-blue-100 dark:bg-blue-900 text-blue-700 dark:text-blue-300 rounded-full text-xs font-medium">
                        {{ t('dashboard.base') }}
                    </div>
                </div>
                <div class="text-xl font-bold text-gray-900 dark:text-white">
                    {{ currencyData.rates[currency]?.toFixed(2) || '0.00' }}
                </div>
                <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                    {{ t('dashboard.per_usd') }}
                </div>
            </div>
        </div>

        <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-600">
            <div class="flex items-center justify-between text-sm">
                <span class="text-gray-500 dark:text-gray-400">
                    {{ t('dashboard.last_updated') }}: 
                    {{ new Date(currencyData.lastUpdated).toLocaleString() }}
                </span>
                <button class="text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 font-medium">
                    {{ t('dashboard.refresh_rates') }}
                </button>
            </div>
        </div>
    </div>
</template>
