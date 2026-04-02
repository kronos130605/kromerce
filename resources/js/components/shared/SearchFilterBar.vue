<script setup>
import { computed } from 'vue';

const props = defineProps({
    searchValue: {
        type: String,
        default: ''
    },
    searchPlaceholder: {
        type: String,
        default: 'Search...'
    },
    hasActiveFilters: {
        type: Boolean,
        default: false
    },
    activeFilterCount: {
        type: Number,
        default: 0
    },
    showFiltersButton: {
        type: Boolean,
        default: true
    },
    filtersButtonLabel: {
        type: String,
        default: 'Filters'
    },
    quickFilters: {
        type: Array,
        default: () => []
    },
    activeQuickFilter: {
        type: String,
        default: null
    }
});

const emit = defineEmits([
    'search',
    'toggle-filters',
    'quick-filter'
]);

const handleSearchInput = (event) => {
    emit('search', event.target.value);
};

const handleToggleFilters = () => {
    emit('toggle-filters');
};

const handleQuickFilter = (filter) => {
    emit('quick-filter', filter);
};

const getQuickFilterClasses = (filter) => {
    const isActive = props.activeQuickFilter === filter.key;
    const baseClasses = 'px-3 py-2 text-sm rounded-lg transition-colors';
    
    if (isActive) {
        return `${baseClasses} ${filter.activeClass || 'bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400'}`;
    }
    return `${baseClasses} bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600`;
};
</script>

<template>
    <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-4">
        <div class="flex flex-col sm:flex-row gap-4">
            <!-- Search -->
            <div class="flex-1">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <input
                        :value="searchValue"
                        @input="handleSearchInput"
                        type="text"
                        :placeholder="searchPlaceholder"
                        class="block w-full pl-10 pr-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    />
                </div>
            </div>

            <!-- Filter Toggle Button -->
            <button
                v-if="showFiltersButton"
                @click="handleToggleFilters"
                :class="[
                    'inline-flex items-center gap-2 px-4 py-2 rounded-lg border transition-colors',
                    hasActiveFilters
                        ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400'
                        : 'border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-600'
                ]"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                </svg>
                <span>{{ filtersButtonLabel }}</span>
                <span v-if="activeFilterCount > 0" class="inline-flex items-center justify-center w-5 h-5 text-xs font-bold text-white bg-blue-600 rounded-full">
                    {{ activeFilterCount }}
                </span>
            </button>

            <!-- Quick Filters -->
            <div v-if="quickFilters.length > 0" class="flex gap-2">
                <button
                    v-for="filter in quickFilters"
                    :key="filter.key"
                    @click="handleQuickFilter(filter)"
                    :class="getQuickFilterClasses(filter)"
                >
                    {{ filter.label }}
                </button>
            </div>
        </div>
    </div>
</template>
