<template>
    <div class="business-currency-status">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                    <svg class="w-5 h-5 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2zm0 8c1.11 0 2 .89 2 2s-.89 2-2 2-2-.89-2-2 .89-2 2 2z"></path>
                    </svg>
                    Currency Status
                </h3>
                <div v-if="currencyStatus.configured" class="flex items-center space-x-2">
                    <div class="w-2 h-2 bg-green-500 rounded-full"></div>
                    <span class="text-sm text-green-600 dark:text-green-400">Configured</span>
                </div>
                <div v-else class="flex items-center space-x-2">
                    <div class="w-2 h-2 bg-yellow-500 rounded-full"></div>
                    <span class="text-sm text-yellow-600 dark:text-yellow-400">Not Set Up</span>
                </div>
            </div>
            
            <div v-if="currencyStatus.configured" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                        <div class="text-sm text-gray-600 dark:text-gray-400 mb-1">Default Currency</div>
                        <div class="text-lg font-semibold text-gray-900 dark:text-white">{{ currencyStatus.defaultCurrency }}</div>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                        <div class="text-sm text-gray-600 dark:text-gray-400 mb-1">Display Currencies</div>
                        <div class="text-lg font-semibold text-gray-900 dark:text-white">{{ currencyStatus.displayCurrencies.join(', ') }}</div>
                    </div>
                </div>
                
                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                    <div class="text-sm text-gray-600 dark:text-gray-400 mb-2">Current Rates</div>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-2">
                        <div v-for="(rate, currency) in currencyStatus.currentRates" :key="currency" class="bg-white dark:bg-gray-600 rounded p-2">
                            <div class="text-sm font-medium text-gray-900 dark:text-white">{{ currency }}</div>
                            <div class="text-xs text-gray-600 dark:text-gray-400">{{ rate.rate }}</div>
                            <div class="text-xs text-blue-600 dark:text-blue-400">{{ rate.source }}</div>
                        </div>
                    </div>
                </div>
                
                <div class="flex items-center justify-between">
                    <div class="text-sm text-gray-600 dark:text-gray-400">
                        Last Update: {{ formatDate(currencyStatus.lastRateUpdate) }}
                    </div>
                    <div class="flex space-x-2">
                        <button @click="updateRates" class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors text-sm font-medium">
                            Update Rates
                        </button>
                        <button @click="configure" class="px-4 py-2 bg-gray-200 dark:bg-gray-600 text-gray-700 dark:text-gray-200 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-500 transition-colors text-sm font-medium">
                            Configure
                        </button>
                    </div>
                </div>
            </div>
            
            <div v-else class="text-center py-8">
                <div class="text-6xl mb-4">💱</div>
                <p class="text-gray-600 dark:text-gray-400 mb-4">Currency configuration not set up for this business.</p>
                <button @click="configure" class="px-6 py-3 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors font-medium">
                    Setup Currency
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
    currencyStatus: {
        type: Object,
        required: true
    }
})

const configured = computed(() => props.currencyStatus.configured)

const formatDate = (date) => {
    if (!date) return 'Never'
    return new Date(date).toLocaleDateString()
}

const updateRates = () => {
    // Emitir evento o llamar a función para actualizar tasas
    console.log('Update rates requested')
}

const configure = () => {
    // Navegar a página de configuración
    window.location.href = '/currency/configure'
}
</script>

<style scoped>
.business-currency-status {
    @apply space-y-6;
}
</style>
