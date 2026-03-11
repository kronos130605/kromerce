<template>
    <div class="currency-status-card">
        <div class="status-header">
            <h3 class="status-title">Currency Status</h3>
            <div class="status-badge" :class="configured ? 'configured' : 'not-configured'">
                {{ configured ? '✅ Configured' : '⚠️ Not Set Up' }}
            </div>
        </div>
        
        <div v-if="configured" class="status-details">
            <div class="currency-info">
                <div class="info-row">
                    <span class="label">Default:</span>
                    <span class="value">{{ currencyStatus.defaultCurrency }}</span>
                </div>
                <div class="info-row">
                    <span class="label">Display:</span>
                    <span class="value">{{ currencyStatus.displayCurrencies.join(', ') }}</span>
                </div>
                <div class="info-row">
                    <span class="label">Last Update:</span>
                    <span class="value">{{ formatDate(currencyStatus.lastRateUpdate) }}</span>
                </div>
                <div class="info-row">
                    <span class="label">Auto Update:</span>
                    <span class="value" :class="currencyStatus.autoUpdateEnabled ? 'enabled' : 'disabled'">
                        {{ currencyStatus.autoUpdateEnabled ? 'Enabled' : 'Disabled' }}
                    </span>
                </div>
            </div>
            
            <div class="current-rates">
                <h4>Current Rates</h4>
                <div class="rates-grid">
                    <div v-for="(rate, currency) in currencyStatus.currentRates" :key="currency" class="rate-item">
                        <span class="currency">{{ currency }}</span>
                        <span class="rate">{{ rate.rate }}</span>
                        <span class="source">{{ rate.source }}</span>
                    </div>
                </div>
            </div>
            
            <div class="status-actions">
                <button @click="handleUpdateRates" class="btn btn-primary">
                    Update Rates
                </button>
                <button @click="handleConfigure" class="btn btn-secondary">
                    Configure
                </button>
            </div>
        </div>
        
        <div v-else class="not-configured">
            <div class="not-configured-content">
                <p>Currency configuration not set up for this business.</p>
                <button @click="handleConfigure" class="btn btn-primary">
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

const handleUpdateRates = () => {
    // Emitir evento o llamar a función para actualizar tasas
    console.log('Update rates requested')
}

const handleConfigure = () => {
    // Navegar a página de configuración
    window.location.href = '/currency/configure'
}
</script>

<style scoped>
.currency-status-card {
    @apply bg-white rounded-lg shadow-md p-6;
}

.status-header {
    @apply flex justify-between items-center mb-4;
}

.status-title {
    @apply text-lg font-semibold text-gray-900;
}

.status-badge {
    @apply px-3 py-1 rounded-full text-xs font-medium;
}

.configured {
    @apply bg-green-100 text-green-800;
}

.not-configured {
    @apply bg-yellow-100 text-yellow-800;
}

.status-details {
    @apply space-y-4;
}

.currency-info {
    @apply space-y-2;
}

.info-row {
    @apply flex justify-between items-center;
}

.label {
    @apply text-sm font-medium text-gray-600;
}

.value {
    @apply text-sm font-medium text-gray-900;
}

.enabled {
    @apply text-green-600;
}

.disabled {
    @apply text-gray-500;
}

.current-rates h4 {
    @apply text-base font-medium text-gray-900 mb-2;
}

.rates-grid {
    @apply grid grid-cols-1 gap-2;
}

.rate-item {
    @apply flex justify-between items-center p-2 bg-gray-50 rounded;
}

.currency {
    @apply font-medium text-gray-900;
}

.rate {
    @apply font-mono text-gray-700;
}

.source {
    @apply text-xs text-gray-500;
}

.status-actions {
    @apply flex space-x-2 mt-4;
}

.btn {
    @apply px-4 py-2 rounded-md text-sm font-medium transition-colors;
}

.btn-primary {
    @apply bg-blue-600 text-white hover:bg-blue-700;
}

.btn-secondary {
    @apply bg-gray-200 text-gray-700 hover:bg-gray-300;
}

.not-configured {
    @apply text-center py-4;
}

.not-configured-content p {
    @apply text-gray-600 mb-4;
}
</style>
