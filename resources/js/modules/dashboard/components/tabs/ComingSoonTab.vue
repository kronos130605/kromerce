<script setup>
const props = defineProps({
    tabLabel: {
        type: String,
        required: true
    }
});
// No imports needed - using Tailwind dark: classes
</script>

<template>
    <div class="text-center py-12">
        <p class="text-gray-500 dark:text-gray-400">
            {{ tabLabel }} coming soon...
        </p>
    </div>
</template>
