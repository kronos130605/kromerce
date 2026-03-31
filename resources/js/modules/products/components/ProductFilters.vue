<script setup>
import { computed } from 'vue';
import PriceInput from '@/components/ui/forms/PriceInput.vue';

const props = defineProps({
    filters: {
        type: Object,
        required: true
    },
    categories: {
        type: Array,
        default: () => []
    },
    show: {
        type: Boolean,
        default: false
    }
});

const emit = defineEmits(['update:filter', 'clear', 'apply']);

const statusOptions = [
    { value: '', label: 'All Statuses' },
    { value: 'active', label: 'Active' },
    { value: 'inactive', label: 'Inactive' },
    { value: 'draft', label: 'Draft' }
];

const stockStatusOptions = [
    { value: '', label: 'All Stock' },
    { value: 'in_stock', label: 'In Stock' },
    { value: 'low', label: 'Low Stock' },
    { value: 'out', label: 'Out of Stock' }
];

const updateFilter = (key, value) => {
    emit('update:filter', key, value);
};
</script>

<template>
    <Transition
        enter-active-class="transition ease-out duration-200"
        enter-from-class="opacity-0 -translate-y-2"
        enter-to-class="opacity-100 translate-y-0"
        leave-active-class="transition ease-in duration-150"
        leave-from-class="opacity-100 translate-y-0"
        leave-to-class="opacity-0 -translate-y-2"
    >
        <div v-if="show" class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6 mb-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                    Filters
                </h3>
                <button
                    @click="emit('clear')"
                    class="text-sm text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300"
                >
                    Clear All
                </button>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- Category Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Category
                    </label>
                    <select
                        :value="filters.category_id"
                        @change="updateFilter('category_id', $event.target.value)"
                        class="block w-full rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 px-3 py-2"
                    >
                        <option value="">All Categories</option>
                        <option
                            v-for="category in categories"
                            :key="category.id"
                            :value="category.id"
                        >
                            {{ category.name }}
                        </option>
                    </select>
                </div>

                <!-- Status Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Status
                    </label>
                    <select
                        :value="filters.status"
                        @change="updateFilter('status', $event.target.value)"
                        class="block w-full rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 px-3 py-2"
                    >
                        <option
                            v-for="option in statusOptions"
                            :key="option.value"
                            :value="option.value"
                        >
                            {{ option.label }}
                        </option>
                    </select>
                </div>

                <!-- Stock Status Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Stock Status
                    </label>
                    <select
                        :value="filters.stock_status"
                        @change="updateFilter('stock_status', $event.target.value)"
                        class="block w-full rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 px-3 py-2"
                    >
                        <option
                            v-for="option in stockStatusOptions"
                            :key="option.value"
                            :value="option.value"
                        >
                            {{ option.label }}
                        </option>
                    </select>
                </div>

                <!-- Featured Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Featured
                    </label>
                    <select
                        :value="filters.featured"
                        @change="updateFilter('featured', $event.target.value)"
                        class="block w-full rounded-lg border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 px-3 py-2"
                    >
                        <option value="">All Products</option>
                        <option value="1">Featured Only</option>
                        <option value="0">Not Featured</option>
                    </select>
                </div>
            </div>

            <!-- Price Range -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                <PriceInput
                    :model-value="filters.min_price"
                    @update:model-value="updateFilter('min_price', $event)"
                    label="Min Price"
                    placeholder="0.00"
                />
                <PriceInput
                    :model-value="filters.max_price"
                    @update:model-value="updateFilter('max_price', $event)"
                    label="Max Price"
                    placeholder="0.00"
                />
            </div>
        </div>
    </Transition>
</template>
