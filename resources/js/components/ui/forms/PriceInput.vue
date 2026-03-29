<script setup>
import { ref, computed, watch } from 'vue';

const props = defineProps({
    modelValue: {
        type: [String, Number],
        default: ''
    },
    currency: {
        type: String,
        default: 'USD'
    },
    label: {
        type: String,
        default: ''
    },
    placeholder: {
        type: String,
        default: '0.00'
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
    min: {
        type: Number,
        default: 0
    },
    max: {
        type: Number,
        default: null
    },
    step: {
        type: Number,
        default: 0.01
    },
    showCurrency: {
        type: Boolean,
        default: true
    }
});

const emit = defineEmits(['update:modelValue', 'blur', 'focus']);

const inputRef = ref(null);
const isFocused = ref(false);

const currencySymbols = {
    USD: '$',
    EUR: '€',
    GBP: '£',
    JPY: '¥',
    CAD: 'C$',
    AUD: 'A$',
    MXN: 'MX$'
};

const currencySymbol = computed(() => currencySymbols[props.currency] || props.currency);

const formattedValue = computed({
    get() {
        if (!props.modelValue) return '';
        const num = parseFloat(props.modelValue);
        if (isNaN(num)) return '';
        return num.toFixed(2);
    },
    set(value) {
        const cleaned = value.replace(/[^0-9.]/g, '');
        const num = parseFloat(cleaned);
        
        if (cleaned === '' || cleaned === '.') {
            emit('update:modelValue', '');
            return;
        }
        
        if (isNaN(num)) return;
        
        if (props.max !== null && num > props.max) {
            emit('update:modelValue', props.max.toString());
            return;
        }
        
        if (num < props.min) {
            emit('update:modelValue', props.min.toString());
            return;
        }
        
        emit('update:modelValue', cleaned);
    }
});

const handleFocus = (event) => {
    isFocused.value = true;
    emit('focus', event);
};

const handleBlur = (event) => {
    isFocused.value = false;
    
    if (props.modelValue) {
        const num = parseFloat(props.modelValue);
        if (!isNaN(num)) {
            emit('update:modelValue', num.toFixed(2));
        }
    }
    
    emit('blur', event);
};

const focus = () => {
    inputRef.value?.focus();
};

defineExpose({ focus });
</script>

<template>
    <div class="price-input">
        <!-- Label -->
        <label 
            v-if="label" 
            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1"
        >
            {{ label }}
            <span v-if="required" class="text-red-500">*</span>
        </label>

        <!-- Input Container -->
        <div class="relative">
            <!-- Currency Symbol -->
            <div 
                v-if="showCurrency"
                class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none"
            >
                <span class="text-gray-500 dark:text-gray-400 text-sm font-medium">
                    {{ currencySymbol }}
                </span>
            </div>

            <!-- Input -->
            <input
                ref="inputRef"
                v-model="formattedValue"
                type="text"
                inputmode="decimal"
                :placeholder="placeholder"
                :disabled="disabled"
                :required="required"
                :min="min"
                :max="max"
                :step="step"
                @focus="handleFocus"
                @blur="handleBlur"
                :class="[
                    'block w-full rounded-lg border-gray-300 dark:border-gray-600',
                    'bg-white dark:bg-gray-700',
                    'text-gray-900 dark:text-white',
                    'placeholder-gray-400 dark:placeholder-gray-500',
                    'focus:ring-2 focus:ring-blue-500 focus:border-blue-500',
                    'disabled:bg-gray-100 dark:disabled:bg-gray-800',
                    'disabled:text-gray-500 dark:disabled:text-gray-400',
                    'disabled:cursor-not-allowed',
                    'transition-colors duration-200',
                    showCurrency ? 'pl-8' : 'pl-3',
                    'pr-3 py-2',
                    error ? 'border-red-500 focus:ring-red-500 focus:border-red-500' : ''
                ]"
            />

            <!-- Currency Code -->
            <div 
                v-if="showCurrency"
                class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none"
            >
                <span class="text-gray-400 dark:text-gray-500 text-xs font-medium">
                    {{ currency }}
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
