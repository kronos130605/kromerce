<script setup>
import { useDarkMode } from '@/composables/useDarkMode';

const props = defineProps({
    tabLabel: {
        type: String,
        required: true
    }
});

const { isDark } = useDarkMode();
</script>

<template>
    <div class="text-center py-12">
        <p :class="isDark ? 'text-gray-400' : 'text-gray-500'">
            {{ tabLabel }} coming soon...
        </p>
    </div>
</template>
