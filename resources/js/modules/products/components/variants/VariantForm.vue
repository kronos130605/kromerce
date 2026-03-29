<script setup>
import { computed } from 'vue';
import PriceInput from '@/components/ui/forms/PriceInput.vue';

const props = defineProps({
    modelValue: {
        type: Object,
        required: true
    },
    errors: {
        type: Object,
        default: () => ({})
    },
    attributes: {
        type: Array,
        default: () => []
    },
    loading: {
        type: Boolean,
        default: false
    }
});

const emit = defineEmits(['update:modelValue', 'save', 'cancel']);

const form = computed({
    get: () => props.modelValue,
    set: (value) => emit('update:modelValue', value)
});

const updateField = (field, value) => {
    emit('update:modelValue', { ...form.value, [field]: value });
};

const handleSubmit = () => {
    emit('save');
};
</script>

<template>
    <form @submit.prevent="handleSubmit" class="space-y-6">
        <!-- Basic Info -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- SKU -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    SKU <span class="text-red-500">*</span>
                </label>
                <input
                    :value="form.sku"
                    @input="updateField('sku', $event.target.value)"
                    type="text"
                    required
                    placeholder="PROD-VAR-001"
                    :class="[
                        'block w-full rounded-lg border-gray-300 dark:border-gray-600',
                        'bg-white dark:bg-gray-700',
                        'text-gray-900 dark:text-white',
                        'focus:ring-2 focus:ring-blue-500 focus:border-blue-500',
                        'px-3 py-2',
                        errors.sku ? 'border-red-500' : ''
                    ]"
                />
                <p v-if="errors.sku" class="mt-1 text-sm text-red-600 dark:text-red-400">
                    {{ errors.sku }}
                </p>
            </div>

            <!-- Name -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Variant Name
                </label>
                <input
                    :value="form.name"
                    @input="updateField('name', $event.target.value)"
                    type="text"
                    placeholder="e.g., Large / Red"
                    :class="[
                        'block w-full rounded-lg border-gray-300 dark:border-gray-600',
                        'bg-white dark:bg-gray-700',
                        'text-gray-900 dark:text-white',
                        'focus:ring-2 focus:ring-blue-500 focus:border-blue-500',
                        'px-3 py-2'
                    ]"
                />
            </div>
        </div>

        <!-- Pricing -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- Price -->
            <PriceInput
                :model-value="form.price"
                @update:model-value="updateField('price', $event)"
                label="Price"
                required
                :error="errors.price"
            />

            <!-- Sale Price -->
            <PriceInput
                :model-value="form.sale_price"
                @update:model-value="updateField('sale_price', $event)"
                label="Sale Price"
                :error="errors.sale_price"
            />
        </div>

        <!-- Stock & Weight -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- Stock Quantity -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Stock Quantity <span class="text-red-500">*</span>
                </label>
                <input
                    :value="form.stock_quantity"
                    @input="updateField('stock_quantity', parseInt($event.target.value) || 0)"
                    type="number"
                    min="0"
                    required
                    :class="[
                        'block w-full rounded-lg border-gray-300 dark:border-gray-600',
                        'bg-white dark:bg-gray-700',
                        'text-gray-900 dark:text-white',
                        'focus:ring-2 focus:ring-blue-500 focus:border-blue-500',
                        'px-3 py-2',
                        errors.stock_quantity ? 'border-red-500' : ''
                    ]"
                />
                <p v-if="errors.stock_quantity" class="mt-1 text-sm text-red-600 dark:text-red-400">
                    {{ errors.stock_quantity }}
                </p>
            </div>

            <!-- Weight -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Weight (kg)
                </label>
                <input
                    :value="form.weight"
                    @input="updateField('weight', $event.target.value)"
                    type="number"
                    step="0.01"
                    min="0"
                    placeholder="0.00"
                    :class="[
                        'block w-full rounded-lg border-gray-300 dark:border-gray-600',
                        'bg-white dark:bg-gray-700',
                        'text-gray-900 dark:text-white',
                        'focus:ring-2 focus:ring-blue-500 focus:border-blue-500',
                        'px-3 py-2'
                    ]"
                />
            </div>
        </div>

        <!-- Attributes -->
        <div v-if="attributes.length > 0">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                Attributes
            </label>
            <div class="space-y-3">
                <div v-for="attribute in attributes" :key="attribute.id" class="flex items-center gap-4">
                    <span class="text-sm text-gray-600 dark:text-gray-400 w-24">
                        {{ attribute.name }}:
                    </span>
                    <select
                        :value="form.attribute_values.includes(attribute.id) ? attribute.id : ''"
                        @change="updateField('attribute_values', [...form.attribute_values.filter(id => id !== attribute.id), $event.target.value])"
                        :class="[
                            'flex-1 rounded-lg border-gray-300 dark:border-gray-600',
                            'bg-white dark:bg-gray-700',
                            'text-gray-900 dark:text-white',
                            'focus:ring-2 focus:ring-blue-500 focus:border-blue-500',
                            'px-3 py-2'
                        ]"
                    >
                        <option value="">Select {{ attribute.name }}</option>
                        <option
                            v-for="value in attribute.values"
                            :key="value.id"
                            :value="value.id"
                        >
                            {{ value.value }}
                        </option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Options -->
        <div class="flex items-center gap-6">
            <!-- Is Default -->
            <label class="flex items-center gap-2 cursor-pointer">
                <input
                    :checked="form.is_default"
                    @change="updateField('is_default', $event.target.checked)"
                    type="checkbox"
                    class="rounded border-gray-300 text-blue-600 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700"
                />
                <span class="text-sm text-gray-700 dark:text-gray-300">
                    Set as default variant
                </span>
            </label>

            <!-- Is Active -->
            <label class="flex items-center gap-2 cursor-pointer">
                <input
                    :checked="form.is_active"
                    @change="updateField('is_active', $event.target.checked)"
                    type="checkbox"
                    class="rounded border-gray-300 text-blue-600 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700"
                />
                <span class="text-sm text-gray-700 dark:text-gray-300">
                    Active
                </span>
            </label>
        </div>

        <!-- Actions -->
        <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-200 dark:border-gray-700">
            <button
                type="button"
                @click="emit('cancel')"
                :disabled="loading"
                class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors disabled:opacity-50"
            >
                Cancel
            </button>
            <button
                type="submit"
                :disabled="loading"
                class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors disabled:opacity-50 flex items-center gap-2"
            >
                <svg v-if="loading" class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                </svg>
                {{ loading ? 'Saving...' : 'Save Variant' }}
            </button>
        </div>
    </form>
</template>
