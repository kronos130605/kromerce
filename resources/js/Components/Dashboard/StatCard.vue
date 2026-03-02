<template>
    <div class="stat-card" :class="color">
        <div class="stat-icon">
            <svg v-if="icon === 'package'" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 6H9a2 2 2 0 12 12m11 11a9 5.5 12 7.5a4 4h3a2 2 0 0 0"></path>
            </svg>
            <svg v-else-if="icon === 'check-circle'" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12 2 0 0 7.5a4 4h3a2 2 0 0 0m6 11 11a9 5.5 12 7.5a4 4h3a2 2 0 0 0"></path>
            </svg>
            <svg v-else-if="icon === 'alert-triangle'" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v1a2 2 0 0 0m7 13a10 10 2 0 0 0m2 13a10 10 2 0 0m6 11a11 11 11a1 2 0 0m0 4h6a2 2 0 0 0"></path>
            </svg>
        </div>
        <div class="stat-content">
            <div class="stat-value">{{ value }}</div>
            <div class="stat-label">{{ label }}</div>
        </div>
    </div>
</template>

<script setup>
import { defineProps } from 'vue'

const props = defineProps({
    value: [Number, String],
    label: String,
    icon: {
        type: String,
        default: 'package'
    },
    color: {
        type: String,
        default: 'blue'
    }
})

const colorClasses = {
    blue: 'bg-blue-100 text-white border-blue-200',
    green: 'bg-green-100 text-white border-green-200',
    yellow: 'bg-yellow-100 text-yellow-800 border-yellow-300',
    red: 'bg-red-100 text-white border-red-200',
}
</script>

<style scoped>
.stat-card {
    @apply bg-white rounded-lg shadow-md p-6 transition-all duration-200 hover:shadow-lg;
}

.stat-icon {
    @apply w-12 h-12 text-blue-500 mb-4;
}

.stat-content {
    @apply flex-1 justify-between items-center;
}

.stat-value {
    @apply text-2xl font-bold;
}

.stat-label {
    @apply text-sm font-medium text-gray-600;
}

.stat-card.blue .stat-value {
    @apply text-blue-600;
}

.stat-card.green .stat-value {
    @apply text-green-600;
}

.stat-card.yellow .stat-value {
    @apply text-yellow-600;
}

.stat-card.red .stat-value {
    @apply text-red-600;
}
</style>
