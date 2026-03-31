<script setup>
import { ref, computed } from 'vue';

const props = defineProps({
    modelValue: {
        type: String,
        default: 'USD'
    },
    label: {
        type: String,
        default: ''
    },
    error: {
        type: String,
        default: ''
    },
    disabled: {
        type: Boolean,
        default: false
    },
    required: {
        type: Boolean,
        default: false
    },
    showFlag: {
        type: Boolean,
        default: true
    },
    showSymbol: {
        type: Boolean,
        default: true
    },
    currencies: {
        type: Array,
        default: () => null
    }
});

const emit = defineEmits(['update:modelValue']);

const defaultCurrencies = [
    { code: 'USD', name: 'US Dollar', symbol: '$', flag: '🇺🇸' },
    { code: 'EUR', name: 'Euro', symbol: '€', flag: '🇪🇺' },
    { code: 'GBP', name: 'British Pound', symbol: '£', flag: '🇬🇧' },
    { code: 'JPY', name: 'Japanese Yen', symbol: '¥', flag: '🇯🇵' },
    { code: 'CAD', name: 'Canadian Dollar', symbol: 'C$', flag: '🇨🇦' },
    { code: 'AUD', name: 'Australian Dollar', symbol: 'A$', flag: '🇦🇺' },
    { code: 'CHF', name: 'Swiss Franc', symbol: 'CHF', flag: '🇨🇭' },
    { code: 'CNY', name: 'Chinese Yuan', symbol: '¥', flag: '🇨🇳' },
    { code: 'MXN', name: 'Mexican Peso', symbol: 'MX$', flag: '🇲🇽' },
    { code: 'BRL', name: 'Brazilian Real', symbol: 'R$', flag: '🇧🇷' },
    { code: 'INR', name: 'Indian Rupee', symbol: '₹', flag: '🇮🇳' },
    { code: 'KRW', name: 'South Korean Won', symbol: '₩', flag: '🇰🇷' }
];

const availableCurrencies = computed(() => props.currencies || defaultCurrencies);

const selectedCurrency = computed(() => {
    return availableCurrencies.value.find(c => c.code === props.modelValue) || availableCurrencies.value[0];
});

const updateValue = (code) => {
    emit('update:modelValue', code);
};
</script>

<template>
    <div class="currency-selector">
        <!-- Label -->
        <label 
            v-if="label" 
            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1"
        >
            {{ label }}
            <span v-if="required" class="text-red-500">*</span>
        </label>

        <!-- Select -->
        <div class="relative">
            <select
                :value="modelValue"
                @change="updateValue($event.target.value)"
                :disabled="disabled"
                :required="required"
                :class="[
                    'block w-full rounded-lg border-gray-300 dark:border-gray-600',
                    'bg-white dark:bg-gray-700',
                    'text-gray-900 dark:text-white',
                    'focus:ring-2 focus:ring-blue-500 focus:border-blue-500',
                    'disabled:bg-gray-100 dark:disabled:bg-gray-800',
                    'disabled:text-gray-500 dark:disabled:text-gray-400',
                    'disabled:cursor-not-allowed',
                    'transition-colors duration-200',
                    'pl-3 pr-10 py-2',
                    error ? 'border-red-500 focus:ring-red-500 focus:border-red-500' : ''
                ]"
            >
                <option 
                    v-for="currency in availableCurrencies" 
                    :key="currency.code"
                    :value="currency.code"
                >
                    <template v-if="showFlag">{{ currency.flag }}</template>
                    {{ currency.code }}
                    <template v-if="showSymbol">- {{ currency.symbol }}</template>
                    - {{ currency.name }}
                </option>
            </select>

            <!-- Selected Currency Display (Optional) -->
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
                    <template v-if="showFlag">{{ selectedCurrency.flag }}</template>
                    <template v-if="showSymbol"> {{ selectedCurrency.symbol }}</template>
                </span>
            </div>
        </div>

        <!-- Error Message -->
        <p v-if="error" class="mt-1 text-sm text-red-600 dark:text-red-400">
            {{ error }}
        </p>

        <!-- Helper Text Slot -->
        <slot name="helper" />
    </div>
</template>
