<script setup>
import { computed } from 'vue';

const props = defineProps({
    status: {
        type: String,
        required: true
    },
    type: {
        type: String,
        default: 'default', // default, product, order, payment, shipping, user
        validator: (value) => ['default', 'product', 'order', 'payment', 'shipping', 'user'].includes(value)
    },
    size: {
        type: String,
        default: 'md', // sm, md, lg
        validator: (value) => ['sm', 'md', 'lg'].includes(value)
    },
    dot: {
        type: Boolean,
        default: false
    },
    icon: {
        type: String,
        default: null
    }
});

const statusConfig = {
    // Product statuses
    product: {
        active: { color: 'green', label: 'Active', icon: 'M5 13l4 4L19 7' },
        inactive: { color: 'gray', label: 'Inactive', icon: 'M6 18L18 6M6 6l12 12' },
        draft: { color: 'yellow', label: 'Draft', icon: 'M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z' },
        archived: { color: 'gray', label: 'Archived', icon: 'M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4' },
        pending: { color: 'yellow', label: 'Pending', icon: 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z' },
        approved: { color: 'green', label: 'Approved', icon: 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z' },
        rejected: { color: 'red', label: 'Rejected', icon: 'M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z' }
    },
    // Order statuses
    order: {
        pending: { color: 'yellow', label: 'Pending', icon: 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z' },
        processing: { color: 'blue', label: 'Processing', icon: 'M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15' },
        completed: { color: 'green', label: 'Completed', icon: 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z' },
        cancelled: { color: 'red', label: 'Cancelled', icon: 'M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z' },
        refunded: { color: 'purple', label: 'Refunded', icon: 'M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6' },
        on_hold: { color: 'orange', label: 'On Hold', icon: 'M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z' }
    },
    // Payment statuses
    payment: {
        paid: { color: 'green', label: 'Paid', icon: 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z' },
        unpaid: { color: 'red', label: 'Unpaid', icon: 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1' },
        partial: { color: 'yellow', label: 'Partial', icon: 'M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z' },
        refunded: { color: 'purple', label: 'Refunded', icon: 'M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6' },
        failed: { color: 'red', label: 'Failed', icon: 'M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z' }
    },
    // Shipping statuses
    shipping: {
        pending: { color: 'yellow', label: 'Pending', icon: 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z' },
        shipped: { color: 'blue', label: 'Shipped', icon: 'M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4' },
        in_transit: { color: 'blue', label: 'In Transit', icon: 'M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0' },
        delivered: { color: 'green', label: 'Delivered', icon: 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z' },
        returned: { color: 'orange', label: 'Returned', icon: 'M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6' }
    },
    // User statuses
    user: {
        active: { color: 'green', label: 'Active', icon: 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z' },
        inactive: { color: 'gray', label: 'Inactive', icon: 'M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z' },
        suspended: { color: 'red', label: 'Suspended', icon: 'M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636' },
        pending: { color: 'yellow', label: 'Pending', icon: 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z' }
    },
    // Default statuses
    default: {
        success: { color: 'green', label: 'Success', icon: 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z' },
        error: { color: 'red', label: 'Error', icon: 'M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z' },
        warning: { color: 'yellow', label: 'Warning', icon: 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z' },
        info: { color: 'blue', label: 'Info', icon: 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z' }
    }
};

const colorClasses = {
    green: 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400',
    red: 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400',
    yellow: 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400',
    blue: 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400',
    gray: 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
    purple: 'bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-400',
    orange: 'bg-orange-100 text-orange-800 dark:bg-orange-900/30 dark:text-orange-400'
};

const sizeClasses = {
    sm: 'px-2 py-0.5 text-xs',
    md: 'px-2.5 py-1 text-xs',
    lg: 'px-3 py-1.5 text-sm'
};

const config = computed(() => {
    const typeConfig = statusConfig[props.type] || statusConfig.default;
    const normalizedStatus = props.status.toLowerCase().replace(/\s+/g, '_');
    return typeConfig[normalizedStatus] || { 
        color: 'gray', 
        label: props.status,
        icon: null
    };
});

const badgeClasses = computed(() => {
    return [
        'inline-flex items-center gap-1.5 rounded-full font-medium capitalize',
        colorClasses[config.value.color],
        sizeClasses[props.size]
    ].join(' ');
});
</script>

<template>
    <span :class="badgeClasses">
        <!-- Dot Indicator -->
        <span 
            v-if="dot" 
            class="w-1.5 h-1.5 rounded-full"
            :class="`bg-current`"
        />
        
        <!-- Icon -->
        <svg 
            v-else-if="icon || config.icon"
            class="w-3.5 h-3.5"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24"
        >
            <path 
                stroke-linecap="round" 
                stroke-linejoin="round" 
                stroke-width="2" 
                :d="icon || config.icon"
            />
        </svg>
        
        <!-- Label -->
        <span>{{ config.label }}</span>
    </span>
</template>
