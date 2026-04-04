<script setup>
import { computed } from 'vue';
import { useTranslations } from '@/composables/useTranslations';

const props = defineProps({
    currencyStatus: {
        type: Object,
        default: () => ({})
    }
});

const { t } = useTranslations();

// Map full source names to short badges
const getSourceBadge = (fullName) => {
    if (!fullName) return '?';
    if (fullName.includes('Banco Central')) return 'BCC';
    if (fullName.includes('ElToque')) return 'ElToque';
    if (fullName.includes('ExchangeRate')) return 'ExchangeRate';
    if (fullName.includes('OpenExchange')) return 'OpenEx';
    return fullName.split(' ')[0];
};

const hasData = computed(() => props.currencyStatus?.rates?.length > 0);

const rates = computed(() => props.currencyStatus?.rates ?? []);

const lastUpdated = computed(() => props.currencyStatus?.last_updated ?? null);

const sourceLabel = computed(() => {
    const s = props.currencyStatus;
    if (!s) return null;
    const parts = [];
    if (s.cuba_source) parts.push(s.cuba_source);
    if (s.foreign_source && s.foreign_source !== s.cuba_source) parts.push(s.foreign_source);
    return parts.join(' · ') || null;
});
</script>

<template>
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md border border-gray-200 dark:border-gray-700 p-6">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                {{ t('currency.currency_status') }}
            </h3>
            <div class="flex items-center space-x-2">
                <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
                <span class="text-sm text-gray-500 dark:text-gray-400">
                    {{ t('currency.live_rates') }}
                </span>
            </div>
        </div>

        <!-- No data state -->
        <div v-if="!hasData" class="py-8 text-center">
            <p class="text-sm text-gray-400 dark:text-gray-500">{{ t('currency.no_rates_available') }}</p>
        </div>

        <!-- Rate pairs grid -->
        <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <div v-for="item in rates" :key="`${item.from}-${item.to}`"
                 class="relative bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                <!-- Source badge - top left corner, no margin -->
                <span class="absolute top-0 left-0 text-[10px] px-1.5 py-0.5 bg-blue-100 dark:bg-blue-900/40 text-blue-700 dark:text-blue-300 rounded-br rounded-tl font-medium tracking-tight">
                    {{ getSourceBadge(item.source) }}
                </span>

                <div class="flex items-center gap-2 mb-2">
                    <span class="text-lg font-bold text-gray-900 dark:text-white">{{ item.from }}</span>
                    <svg class="h-3 w-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                    </svg>
                    <span class="text-lg font-bold text-gray-900 dark:text-white">{{ item.to }}</span>
                </div>

                <div class="text-2xl font-bold text-gray-900 dark:text-white">
                    {{ item.rate.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 4 }) }}
                </div>

                <div class="text-xs text-gray-400 dark:text-gray-500 mt-1">
                    {{ item.effective_date }}
                </div>
            </div>
        </div>

        <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-600">
            <div class="flex items-center justify-between text-sm">
                <span class="text-gray-500 dark:text-gray-400">
                    <template v-if="sourceLabel">{{ sourceLabel }} · </template>
                    <template v-if="lastUpdated">{{ t('currency.last_updated') }}: {{ new Date(lastUpdated).toLocaleDateString() }}</template>
                    <template v-else>{{ t('currency.no_rates_available') }}</template>
                </span>
            </div>
        </div>
    </div>
</template>
