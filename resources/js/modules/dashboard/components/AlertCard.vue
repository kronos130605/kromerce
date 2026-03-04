<template>
    <div class="alert-card" :class="alertClass">
        <div class="alert-icon">
            <svg v-if="alert.type === 'warning'" class="w-5 h-5 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v1a2 2 0 0 0 2 2m-6 4a2 2 0 0 0 2 2m0 4a2 2 0 0 0 2 2m6 11a11 11 11a1 2 2 0 0 0m0 4h6a2 2 0 0 0"></path>
            </svg>
            <svg v-else-if="alert.type === 'info'" class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 1 0 0 1-18 0 9 9 1 0 0 1 18 0z"></path>
            </svg>
            <svg v-else-if="alert.type === 'error'" class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 1 0 0 1-18 0 9 9 1 0 0 1 18 0z"></path>
            </svg>
        </div>
        <div class="alert-content">
            <div class="alert-message">{{ alert.message }}</div>
            <button v-if="alert.action" @click="handleAction" class="alert-action">
                {{ getActionText(alert.action) }}
            </button>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
    alert: {
        type: Object,
        required: true
    }
})

const alertClass = computed(() => {
    const baseClasses = 'border-l-4 p-4 mb-4 rounded-lg'
    const colorClasses = {
        warning: 'bg-yellow-50 border-yellow-400 text-yellow-800',
        info: 'bg-blue-50 border-blue-400 text-blue-800',
        error: 'bg-red-50 border-red-400 text-red-800',
    }
    return `${baseClasses} ${colorClasses[props.alert.type] || colorClasses.info}`
})

const getActionText = (action) => {
    const actionTexts = {
        'view-products': 'View Products',
        'update-rates': 'Update Rates',
        'setup-currency': 'Setup Currency',
        'refresh': 'Refresh'
    }
    return actionTexts[action] || action
}

const handleAction = () => {
    if (props.alert.action === 'refresh') {
        window.location.reload()
    } else {
        // Emitir evento o llamar a función específica
        console.log('Action:', props.alert.action)
    }
}
</script>

<style scoped>
.alert-card {
    @apply flex items-start space-x-3;
}

.alert-icon {
    @apply flex-shrink-0 mt-0.5;
}

.alert-content {
    @apply flex-1;
}

.alert-message {
    @apply text-sm font-medium;
}

.alert-action {
    @apply px-3 py-1 text-sm font-medium rounded-md bg-gray-100 text-gray-700 hover:bg-gray-200 transition-colors;
}
</style>
