<script setup>
import { computed } from 'vue';
import StatusBadge from '@/components/ui/data-display/StatusBadge.vue';
import StockIndicator from '@/components/ui/data-display/StockIndicator.vue';

const props = defineProps({
    variants: {
        type: Array,
        default: () => []
    },
    loading: {
        type: Boolean,
        default: false
    }
});

const emit = defineEmits(['edit', 'delete', 'set-default']);

const hasVariants = computed(() => props.variants.length > 0);

const getAttributeDisplay = (variant) => {
    if (!variant.attribute_values || variant.attribute_values.length === 0) {
        return 'No attributes';
    }
    return variant.attribute_values
        .map(av => `${av.attribute?.name}: ${av.value}`)
        .join(', ');
};
</script>

<template>
    <div class="variant-list">
        <!-- Loading State -->
        <div v-if="loading" class="flex items-center justify-center py-12">
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
        </div>

        <!-- Empty State -->
        <div v-else-if="!hasVariants" class="text-center py-12 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01" />
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No variants</h3>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                Get started by creating a new variant.
            </p>
        </div>

        <!-- Variants Table -->
        <div v-else class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 dark:bg-gray-900/50 border-b border-gray-200 dark:border-gray-700">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">
                                Variant
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">
                                Attributes
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">
                                Price
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">
                                Stock
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">
                                Status
                            </th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        <tr
                            v-for="variant in variants"
                            :key="variant.id"
                            class="hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors"
                        >
                            <!-- Variant Info -->
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-2">
                                    <div>
                                        <p class="text-sm font-medium text-gray-900 dark:text-white">
                                            {{ variant.name || variant.sku }}
                                        </p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">
                                            SKU: {{ variant.sku }}
                                        </p>
                                        <StatusBadge
                                            v-if="variant.is_default"
                                            status="default"
                                            size="sm"
                                            class="mt-1"
                                        />
                                    </div>
                                </div>
                            </td>

                            <!-- Attributes -->
                            <td class="px-4 py-3">
                                <p class="text-sm text-gray-900 dark:text-white">
                                    {{ getAttributeDisplay(variant) }}
                                </p>
                            </td>

                            <!-- Price -->
                            <td class="px-4 py-3">
                                <div>
                                    <p class="text-sm font-medium text-gray-900 dark:text-white">
                                        ${{ parseFloat(variant.price).toFixed(2) }}
                                    </p>
                                    <p v-if="variant.sale_price" class="text-xs text-green-600 dark:text-green-400">
                                        Sale: ${{ parseFloat(variant.sale_price).toFixed(2) }}
                                    </p>
                                </div>
                            </td>

                            <!-- Stock -->
                            <td class="px-4 py-3">
                                <StockIndicator
                                    :stock="variant.stock_quantity"
                                    :low-stock-threshold="10"
                                    size="sm"
                                />
                            </td>

                            <!-- Status -->
                            <td class="px-4 py-3">
                                <StatusBadge
                                    :status="variant.is_active ? 'active' : 'inactive'"
                                    type="product"
                                    size="sm"
                                />
                            </td>

                            <!-- Actions -->
                            <td class="px-4 py-3">
                                <div class="flex items-center justify-end gap-1">
                                    <button
                                        v-if="!variant.is_default"
                                        @click="emit('set-default', variant.id)"
                                        title="Set as default"
                                        class="p-1.5 rounded-lg text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/30 transition-colors"
                                    >
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                                        </svg>
                                    </button>
                                    <button
                                        @click="emit('edit', variant)"
                                        title="Edit variant"
                                        class="p-1.5 rounded-lg text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/30 transition-colors"
                                    >
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </button>
                                    <button
                                        @click="emit('delete', variant.id)"
                                        title="Delete variant"
                                        class="p-1.5 rounded-lg text-gray-400 hover:text-red-600 dark:hover:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/30 transition-colors"
                                    >
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>
