<script setup>
import { computed } from 'vue';
import { 
    FunnelIcon, 
    ChevronDownIcon, 
    ChevronUpIcon,
    MagnifyingGlassIcon
} from '@heroicons/vue/24/outline';

// Color presets for quick filters
const colorPresets = {
    blue: 'bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400',
    green: 'bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400',
    yellow: 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-600 dark:text-yellow-400',
    red: 'bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400',
    purple: 'bg-purple-100 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400',
    pink: 'bg-pink-100 dark:bg-pink-900/30 text-pink-600 dark:text-pink-400',
    gray: 'bg-gray-100 dark:bg-gray-900/30 text-gray-600 dark:text-gray-400',
    orange: 'bg-orange-100 dark:bg-orange-900/30 text-orange-600 dark:text-orange-400',
    teal: 'bg-teal-100 dark:bg-teal-900/30 text-teal-600 dark:text-teal-400',
    cyan: 'bg-cyan-100 dark:bg-cyan-900/30 text-cyan-600 dark:text-cyan-400',
};

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
    filtersOpen: {
        type: Boolean,
        default: false
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
    const baseClasses = 'inline-flex items-center justify-center w-9 h-9 rounded-lg transition-colors';
    const color = filter.color || 'blue';
    const activeClasses = colorPresets[color] || colorPresets.blue;
    
    if (isActive) {
        return `${baseClasses} ${activeClasses}`;
    }
    return `${baseClasses} bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400 hover:bg-gray-200 dark:hover:bg-gray-600`;
};
</script>

<template>
    <div class="flex flex-wrap items-center gap-2">
        <!-- Search Input (compact style) -->
        <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <MagnifyingGlassIcon class="h-5 w-5 text-gray-400" />
            </div>
            <input
                :value="searchValue"
                @input="handleSearchInput"
                type="text"
                :placeholder="searchPlaceholder"
                class="w-[180px] sm:w-[220px] pl-10 pr-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors text-sm"
            />
        </div>

        <!-- Filter Toggle Button -->
        <button
            v-if="showFiltersButton"
            @click="handleToggleFilters"
            :class="[
                'inline-flex items-center justify-center w-9 h-9 rounded-lg border transition-colors relative',
                hasActiveFilters
                    ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400'
                    : 'border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700'
            ]"
            :aria-expanded="filtersOpen"
        >
            <FunnelIcon class="w-5 h-5" />
            <!-- Arrow indicator -->
            <span class="absolute -bottom-0.5 -right-0.5 bg-white dark:bg-gray-800 rounded-full p-0.5">
                <ChevronDownIcon 
                    v-if="!filtersOpen"
                    class="w-3 h-3" 
                />
                <ChevronUpIcon 
                    v-else
                    class="w-3 h-3" 
                />
            </span>
            <!-- Badge -->
            <span 
                v-if="activeFilterCount > 0" 
                class="absolute -top-1.5 -right-1.5 inline-flex items-center justify-center min-w-[18px] h-[18px] text-[10px] font-bold text-white bg-blue-600 rounded-full px-1"
            >
                {{ activeFilterCount > 9 ? '9+' : activeFilterCount }}
            </span>
        </button>

        <!-- Quick Filters -->
        <button
            v-for="filter in quickFilters"
            :key="filter.key"
            @click="handleQuickFilter(filter)"
            :class="getQuickFilterClasses(filter)"
            :title="filter.label"
        >
            <component :is="filter.icon" class="w-5 h-5" />
        </button>
    </div>
</template>
