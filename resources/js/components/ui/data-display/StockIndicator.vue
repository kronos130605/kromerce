<script setup>
import { computed } from 'vue';

const props = defineProps({
    stock: {
        type: Number,
        required: true
    },
    lowStockThreshold: {
        type: Number,
        default: 10
    },
    showLabel: {
        type: Boolean,
        default: true
    },
    showIcon: {
        type: Boolean,
        default: true
    },
    size: {
        type: String,
        default: 'md', // sm, md, lg
        validator: (value) => ['sm', 'md', 'lg'].includes(value)
    },
    variant: {
        type: String,
        default: 'badge', // badge, text, bar
        validator: (value) => ['badge', 'text', 'bar'].includes(value)
    }
});

const stockStatus = computed(() => {
    if (props.stock === 0) {
        return {
            status: 'out_of_stock',
            label: 'Out of Stock',
            color: 'red',
            icon: 'M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z',
            percentage: 0
        };
    }
    
    if (props.stock <= props.lowStockThreshold) {
        return {
            status: 'low_stock',
            label: `Low Stock (${props.stock})`,
            color: 'yellow',
            icon: 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z',
            percentage: (props.stock / props.lowStockThreshold) * 50
        };
    }
    
    return {
        status: 'in_stock',
        label: `In Stock (${props.stock})`,
        color: 'green',
        icon: 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
        percentage: 100
    };
});

const colorClasses = {
    badge: {
        green: 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400',
        yellow: 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400',
        red: 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400'
    },
    text: {
        green: 'text-green-600 dark:text-green-400',
        yellow: 'text-yellow-600 dark:text-yellow-400',
        red: 'text-red-600 dark:text-red-400'
    },
    bar: {
        green: 'bg-green-500',
        yellow: 'bg-yellow-500',
        red: 'bg-red-500'
    }
};

const sizeClasses = {
    sm: {
        badge: 'px-2 py-0.5 text-xs',
        text: 'text-xs',
        icon: 'w-3 h-3',
        bar: 'h-1.5'
    },
    md: {
        badge: 'px-2.5 py-1 text-xs',
        text: 'text-sm',
        icon: 'w-4 h-4',
        bar: 'h-2'
    },
    lg: {
        badge: 'px-3 py-1.5 text-sm',
        text: 'text-base',
        icon: 'w-5 h-5',
        bar: 'h-3'
    }
};
</script>

<template>
    <!-- Badge Variant -->
    <span 
        v-if="variant === 'badge'"
        :class="[
            'inline-flex items-center gap-1.5 rounded-full font-medium',
            colorClasses.badge[stockStatus.color],
            sizeClasses[size].badge
        ]"
    >
        <svg 
            v-if="showIcon"
            :class="sizeClasses[size].icon"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24"
        >
            <path 
                stroke-linecap="round" 
                stroke-linejoin="round" 
                stroke-width="2" 
                :d="stockStatus.icon"
            />
        </svg>
        <span v-if="showLabel">{{ stockStatus.label }}</span>
        <span v-else>{{ stock }}</span>
    </span>

    <!-- Text Variant -->
    <div 
        v-else-if="variant === 'text'"
        :class="[
            'inline-flex items-center gap-1.5 font-medium',
            colorClasses.text[stockStatus.color],
            sizeClasses[size].text
        ]"
    >
        <svg 
            v-if="showIcon"
            :class="sizeClasses[size].icon"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24"
        >
            <path 
                stroke-linecap="round" 
                stroke-linejoin="round" 
                stroke-width="2" 
                :d="stockStatus.icon"
            />
        </svg>
        <span v-if="showLabel">{{ stockStatus.label }}</span>
        <span v-else>{{ stock }}</span>
    </div>

    <!-- Bar Variant -->
    <div v-else-if="variant === 'bar'" class="w-full">
        <div class="flex items-center justify-between mb-1" v-if="showLabel">
            <span :class="['text-xs font-medium', colorClasses.text[stockStatus.color]]">
                {{ stockStatus.label }}
            </span>
            <span class="text-xs text-gray-500 dark:text-gray-400">
                {{ stock }} units
            </span>
        </div>
        <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden" :class="sizeClasses[size].bar">
            <div 
                :class="[
                    'transition-all duration-300',
                    colorClasses.bar[stockStatus.color],
                    sizeClasses[size].bar
                ]"
                :style="{ width: `${stockStatus.percentage}%` }"
            />
        </div>
    </div>
</template>
