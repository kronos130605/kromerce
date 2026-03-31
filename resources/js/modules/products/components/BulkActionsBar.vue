<script setup>
import { ref } from 'vue';
import { useTranslations } from '@/composables/useTranslations';

const { t } = useTranslations();

const props = defineProps({
    selectionCount: {
        type: Number,
        required: true
    },
    isProcessing: {
        type: Boolean,
        default: false
    }
});

const emit = defineEmits([
    'clear-selection',
    'bulk-activate',
    'bulk-deactivate',
    'bulk-draft',
    'bulk-delete',
    'bulk-export'
]);

const showMenu = ref(false);
</script>

<template>
    <Transition
        enter-active-class="transition ease-out duration-200"
        enter-from-class="opacity-0 translate-y-2"
        enter-to-class="opacity-100 translate-y-0"
        leave-active-class="transition ease-in duration-150"
        leave-from-class="opacity-100 translate-y-0"
        leave-to-class="opacity-0 translate-y-2"
    >
        <div
            v-if="selectionCount > 0"
            class="fixed bottom-6 left-1/2 -translate-x-1/2 z-40"
        >
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-2xl border border-gray-200 dark:border-gray-700 px-6 py-4">
                <div class="flex items-center gap-6">
                    <!-- Selection Info -->
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-900 dark:text-white">
                                {{ t('products.bulk_actions.selected', { count: selectionCount }) }}
                            </p>
                            <button
                                @click="emit('clear-selection')"
                                class="text-xs text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300"
                            >
                                {{ t('products.bulk_actions.clear_selection') }}
                            </button>
                        </div>
                    </div>

                    <!-- Divider -->
                    <div class="h-10 w-px bg-gray-200 dark:bg-gray-700"></div>

                    <!-- Quick Actions -->
                    <div class="flex items-center gap-2">
                        <!-- Activate -->
                        <button
                            @click="emit('bulk-activate')"
                            :disabled="isProcessing"
                            :title="t('products.bulk_actions.activate')"
                            class="p-2 rounded-lg text-gray-600 dark:text-gray-400 hover:text-green-600 dark:hover:text-green-400 hover:bg-green-50 dark:hover:bg-green-900/30 transition-colors disabled:opacity-50"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </button>

                        <!-- Deactivate -->
                        <button
                            @click="emit('bulk-deactivate')"
                            :disabled="isProcessing"
                            :title="t('products.bulk_actions.deactivate')"
                            class="p-2 rounded-lg text-gray-600 dark:text-gray-400 hover:text-yellow-600 dark:hover:text-yellow-400 hover:bg-yellow-50 dark:hover:bg-yellow-900/30 transition-colors disabled:opacity-50"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </button>

                        <!-- Draft -->
                        <button
                            @click="emit('bulk-draft')"
                            :disabled="isProcessing"
                            :title="t('products.bulk_actions.move_to_draft')"
                            class="p-2 rounded-lg text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/30 transition-colors disabled:opacity-50"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                        </button>

                        <!-- Divider -->
                        <div class="h-6 w-px bg-gray-200 dark:bg-gray-700 mx-1"></div>

                        <!-- Export -->
                        <button
                            @click="emit('bulk-export')"
                            :disabled="isProcessing"
                            :title="t('products.bulk_actions.export')"
                            class="p-2 rounded-lg text-gray-600 dark:text-gray-400 hover:text-purple-600 dark:hover:text-purple-400 hover:bg-purple-50 dark:hover:bg-purple-900/30 transition-colors disabled:opacity-50"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </button>

                        <!-- Delete -->
                        <button
                            @click="emit('bulk-delete')"
                            :disabled="isProcessing"
                            :title="t('products.bulk_actions.delete')"
                            class="p-2 rounded-lg text-gray-600 dark:text-gray-400 hover:text-red-600 dark:hover:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/30 transition-colors disabled:opacity-50"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                        </button>
                    </div>

                    <!-- Processing Indicator -->
                    <div v-if="isProcessing" class="flex items-center gap-2 ml-2">
                        <div class="animate-spin rounded-full h-5 w-5 border-b-2 border-blue-600"></div>
                        <span class="text-sm text-gray-600 dark:text-gray-400">{{ t('products.bulk_actions.processing') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </Transition>
</template>
