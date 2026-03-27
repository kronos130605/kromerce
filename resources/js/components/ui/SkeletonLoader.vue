<script setup>
defineProps({
    type: {
        type: String,
        default: 'list', // 'list', 'card', 'table', 'stat'
        validator: (v) => ['list', 'card', 'table', 'stat', 'custom'].includes(v)
    },
    rows: {
        type: Number,
        default: 3
    },
    columns: {
        type: Number,
        default: 1
    },
    animated: {
        type: Boolean,
        default: true
    }
});
</script>

<template>
    <div :class="{ 'animate-pulse': animated }">
        <!-- List Layout -->
        <template v-if="type === 'list'">
            <div v-for="n in rows" :key="n" class="flex items-start space-x-3 py-3">
                <div v-if="$slots.icon" class="w-10 h-10 bg-gray-200 dark:bg-gray-700 rounded-xl flex-shrink-0"></div>
                <div class="flex-1 space-y-2">
                    <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-3/4"></div>
                    <div class="h-3 bg-gray-200 dark:bg-gray-700 rounded w-1/2"></div>
                </div>
                <div v-if="$slots.action" class="w-20 h-8 bg-gray-200 dark:bg-gray-700 rounded-lg flex-shrink-0"></div>
            </div>
        </template>

        <!-- Card Layout -->
        <template v-else-if="type === 'card'">
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700 p-6">
                <div v-for="n in rows" :key="n" class="flex items-center space-x-4 py-3">
                    <div class="w-12 h-12 bg-gray-200 dark:bg-gray-700 rounded-xl flex-shrink-0"></div>
                    <div class="flex-1 space-y-2">
                        <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-48"></div>
                        <div class="h-3 bg-gray-200 dark:bg-gray-700 rounded w-32"></div>
                    </div>
                    <div class="w-24 h-8 bg-gray-200 dark:bg-gray-700 rounded-lg flex-shrink-0"></div>
                </div>
            </div>
        </template>

        <!-- Table Layout -->
        <template v-else-if="type === 'table'">
            <div class="space-y-3">
                <!-- Header -->
                <div class="flex space-x-4 pb-3 border-b border-gray-200 dark:border-gray-700">
                    <div v-for="c in columns" :key="c" class="h-4 bg-gray-300 dark:bg-gray-600 rounded flex-1"></div>
                </div>
                <!-- Rows -->
                <div v-for="n in rows" :key="n" class="flex space-x-4 py-2">
                    <div v-for="c in columns" :key="c" class="h-3 bg-gray-200 dark:bg-gray-700 rounded flex-1"></div>
                </div>
            </div>
        </template>

        <!-- Stat Layout (for stats cards) -->
        <template v-else-if="type === 'stat'">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div v-for="n in rows" :key="n" class="bg-white dark:bg-gray-800 rounded-xl p-4 border border-gray-200 dark:border-gray-700">
                    <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-20 mb-3"></div>
                    <div class="h-8 bg-gray-300 dark:bg-gray-600 rounded w-24 mb-2"></div>
                    <div class="h-3 bg-gray-200 dark:bg-gray-700 rounded w-16"></div>
                </div>
            </div>
        </template>

        <!-- Custom Layout (using slots) -->
        <template v-else-if="type === 'custom'">
            <slot />
        </template>
    </div>
</template>
