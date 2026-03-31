<script setup>
import { onMounted } from 'vue';
import { useVariants } from '../../composables/useVariants.js';
import VariantList from './VariantList.vue';
import VariantForm from './VariantForm.vue';
import StockIndicator from '@/components/ui/data-display/StockIndicator.vue';

const props = defineProps({
    productId: {
        type: String,
        required: true
    },
    attributes: {
        type: Array,
        default: () => []
    }
});

const {
    variants,
    loading,
    isModalOpen,
    isEditing,
    form,
    errors,
    hasVariants,
    totalStock,
    lowestPrice,
    highestPrice,
    fetchVariants,
    openCreate,
    openEdit,
    closeModal,
    save,
    deleteVariant,
    setDefaultVariant
} = useVariants(props.productId);

onMounted(() => {
    fetchVariants();
});

const handleSave = async () => {
    const success = await save();
    if (success) {
        await fetchVariants();
    }
};

const handleDelete = async (variantId) => {
    if (confirm('Are you sure you want to delete this variant?')) {
        const success = await deleteVariant(variantId);
        if (success) {
            await fetchVariants();
        }
    }
};
</script>

<template>
    <div class="variant-manager">
        <!-- Header -->
        <div class="flex items-center justify-between mb-6">
            <div>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                    Product Variants
                </h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                    Manage different variations of this product
                </p>
            </div>
            <button
                @click="openCreate"
                class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Add Variant
            </button>
        </div>

        <!-- Stats Cards -->
        <div v-if="hasVariants" class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <!-- Total Variants -->
            <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Total Variants</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">
                            {{ variants.length }}
                        </p>
                    </div>
                    <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01" />
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Total Stock -->
            <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Total Stock</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">
                            {{ totalStock }}
                        </p>
                    </div>
                    <StockIndicator 
                        :stock="totalStock" 
                        :show-label="false"
                        size="lg"
                        variant="text"
                    />
                </div>
            </div>

            <!-- Price Range -->
            <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Price Range</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">
                            ${{ lowestPrice?.toFixed(2) }} - ${{ highestPrice?.toFixed(2) }}
                        </p>
                    </div>
                    <div class="w-12 h-12 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Variants List -->
        <VariantList
            :variants="variants"
            :loading="loading"
            @edit="openEdit"
            @delete="handleDelete"
            @set-default="setDefaultVariant"
        />

        <!-- Variant Form Modal -->
        <Teleport to="body">
            <div
                v-if="isModalOpen"
                class="fixed inset-0 z-50 overflow-y-auto"
                @click.self="closeModal"
            >
                <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
                    <!-- Overlay -->
                    <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75 dark:bg-gray-900 dark:bg-opacity-75" @click="closeModal" />

                    <!-- Modal -->
                    <div class="relative inline-block w-full max-w-2xl p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-white dark:bg-gray-800 shadow-xl rounded-2xl">
                        <!-- Header -->
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                                {{ isEditing ? 'Edit Variant' : 'Create Variant' }}
                            </h3>
                            <button
                                @click="closeModal"
                                class="text-gray-400 hover:text-gray-500 dark:hover:text-gray-300"
                            >
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        <!-- Form -->
                        <VariantForm
                            v-model="form"
                            :errors="errors"
                            :attributes="attributes"
                            :loading="loading"
                            @save="handleSave"
                            @cancel="closeModal"
                        />
                    </div>
                </div>
            </div>
        </Teleport>
    </div>
</template>
