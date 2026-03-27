<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    icon: {
        type: String,
        default: '📭'
    },
    title: {
        type: String,
        required: true
    },
    description: {
        type: String,
        default: ''
    },
    actionLink: {
        type: String,
        default: null
    },
    actionText: {
        type: String,
        default: ''
    },
    actionIcon: {
        type: String,
        default: '→'
    },
    variant: {
        type: String,
        default: 'default', // 'default', 'compact', 'card'
        validator: (v) => ['default', 'compact', 'card'].includes(v)
    }
});
</script>

<template>
    <div :class="[
        'text-center',
        variant === 'default' && 'py-12',
        variant === 'compact' && 'py-6',
        variant === 'card' && 'bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700 p-12'
    ]">
        <!-- Icon -->
        <div :class="[
            'bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-4',
            variant === 'compact' ? 'w-12 h-12' : 'w-20 h-20'
        ]">
            <span :class="variant === 'compact' ? 'text-2xl' : 'text-4xl'">{{ icon }}</span>
        </div>

        <!-- Title -->
        <h3 :class="[
            'font-semibold text-gray-900 dark:text-white mb-2',
            variant === 'compact' ? 'text-base' : 'text-lg'
        ]">
            {{ title }}
        </h3>

        <!-- Description -->
        <p v-if="description" :class="[
            'text-gray-500 dark:text-gray-400 mb-6',
            variant === 'compact' ? 'text-sm max-w-xs' : 'max-w-sm mx-auto'
        ]">
            {{ description }}
        </p>

        <!-- Action Button -->
        <Link v-if="actionLink" 
              :href="actionLink"
              :class="[
                  'inline-flex items-center rounded-lg transition-colors',
                  variant === 'compact' 
                      ? 'px-3 py-1.5 text-sm bg-blue-100 text-blue-700 hover:bg-blue-200 dark:bg-blue-900 dark:text-blue-200' 
                      : 'px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white'
              ]">
            <span v-if="actionIcon" class="mr-2">{{ actionIcon }}</span>
            {{ actionText }}
        </Link>

        <!-- Slot for custom content -->
        <slot name="extra" />
    </div>
</template>
