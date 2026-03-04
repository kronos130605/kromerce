<template>
    <div class="chart-card">
        <h3 class="card-title">{{ title }}</h3>
        <div v-if="data.length === 0" class="empty-state">
            <div class="empty-icon">
                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6H2m7 7V10a2 2 0 0 0 2 2h5.586a1 1 0 0 0 .707.293l-6.414 6.414a1 1 0 0 0 .707-.293l-6.415-6.586a1 1 0 0 0-.707-.293l-6.586 6.586a1 1 0 0 0-.707-.293l-6.586-6.586z"></path>
                </svg>
            </div>
            <p class="empty-message">No data available</p>
        </div>
        
        <div v-else class="chart-container">
            <div class="simple-chart">
                <div class="chart-bars">
                    <div v-for="(item, index) in data" :key="index" class="chart-bar" :style="{ height: getBarHeight(item.value) + '%' }">
                        <div class="bar-tooltip">{{ item.label }}: {{ formatValue(item.value) }}</div>
                    </div>
                </div>
                <div class="chart-labels">
                    <div v-for="(item, index) in data" :key="index" class="chart-label">
                        {{ item.label }}
                    </div>
                </div>
            </div>
            <div class="chart-legend">
                <div class="legend-item">
                    <span class="legend-color" style="background-color: #3B82F6"></span>
                    <span class="legend-label">{{ getLegendLabel() }}</span>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
    title: {
        type: String,
        required: true
    },
    data: {
        type: Array,
        required: true
    },
    type: {
        type: String,
        default: 'bar'
    }
})

const maxValue = computed(() => {
    const validValues = props.data.map(item => {
        const value = Number(item.value)
        return isNaN(value) ? 0 : value
    })
    return Math.max(...validValues, 1) // Mínimo 1 para evitar división por cero
})

const getBarHeight = (value) => {
    if (value === undefined || value === null) {
        return 0
    }
    const numValue = Number(value)
    if (isNaN(numValue)) {
        return 0
    }
    return maxValue.value > 0 ? (numValue / maxValue.value) * 100 : 0
}

const formatValue = (value) => {
    if (value === undefined || value === null) {
        return '0'
    }
    
    const numValue = Number(value)
    if (isNaN(numValue)) {
        return '0'
    }
    
    if (props.title.includes('Revenue')) {
        return `$${numValue.toLocaleString()}`
    } else if (props.title.includes('Products')) {
        return numValue.toString()
    } else {
        return numValue.toString()
    }
}

const getLegendLabel = () => {
    if (props.title.includes('Revenue')) {
        return 'Revenue'
    } else if (props.title.includes('Products')) {
        return 'Products'
    } else if (props.title.includes('Currency')) {
        return 'Exchange Rate'
    } else {
        return 'Data'
    }
}
</script>

<style scoped>
.chart-card {
    @apply bg-white rounded-lg shadow-md p-6;
}

.card-title {
    @apply text-lg font-semibold text-gray-900 mb-4;
}

.empty-state {
    @apply text-center py-8;
}

.empty-icon {
    @apply mx-auto mb-4;
}

.empty-message {
    @apply text-gray-500;
}

.chart-container {
    @apply space-y-4;
}

.simple-chart {
    @apply bg-gray-50 rounded-lg p-4;
}

.chart-bars {
    @apply flex items-end justify-between h-32 mb-2;
}

.chart-bar {
    @apply flex-1 mx-1 bg-blue-500 rounded-t relative cursor-pointer hover:bg-blue-600 transition-colors;
    min-height: 4px;
}

.bar-tooltip {
    @apply absolute -top-8 left-1/2 transform -translate-x-1/2 bg-gray-800 text-white text-xs px-2 py-1 rounded opacity-0 hover:opacity-100 transition-opacity whitespace-nowrap;
}

.chart-labels {
    @apply flex justify-between text-xs text-gray-600;
}

.chart-label {
    @apply flex-1 text-center;
}

.chart-legend {
    @apply flex justify-center;
}

.legend-item {
    @apply flex items-center space-x-2 text-sm;
}

.legend-color {
    @apply w-3 h-3 rounded-full;
}

.legend-label {
    @apply text-xs font-medium text-gray-700;
}
</style>
