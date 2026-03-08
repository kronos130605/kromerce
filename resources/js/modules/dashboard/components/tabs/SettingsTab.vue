<script setup>
import { useDarkMode } from '@/composables/useDarkMode';

const { isDark } = useDarkMode();
</script>

<template>
    <div class="space-y-6">
        <div class="flex justify-between items-center">
            <h3 class="text-lg font-medium" :class="isDark ? 'text-white' : 'text-gray-900'">
                Business Settings
            </h3>
        </div>
        
        <div class="rounded-2xl p-8 text-center border" :class="isDark ? 'bg-gray-800 border-gray-700' : 'bg-gray-50 border-gray-200'">
            <p :class="isDark ? 'text-gray-300' : 'text-gray-600'">
                Business settings and configuration coming soon...
            </p>
        </div>
    </div>
</template>
