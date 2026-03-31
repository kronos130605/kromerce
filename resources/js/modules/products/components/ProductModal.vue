<script setup>
import { watch } from 'vue';
import { useTranslations } from '@/composables/useTranslations';
import ImageUploader from './ImageUploader.vue';
import CategorySelector from './CategorySelector.vue';

const { t } = useTranslations();

const props = defineProps({
    isOpen: { type: Boolean, required: true },
    isEditing: { type: Boolean, default: false },
    form: { type: Object, required: true },
    steps: { type: Array, required: true },
    currentStep: { type: Number, required: true },
    errors: { type: Object, default: () => ({}) },
    loading: { type: Boolean, default: false },
    categories: { type: Array, default: () => [] },
});

const emit = defineEmits([
    'close',
    'save',
    'next-step',
    'prev-step',
    'go-to-step',
    'update:form',
]);

// Lock body scroll when modal is open
watch(() => props.isOpen, (open) => {
    if (open) {
        document.body.style.overflow = 'hidden';
    } else {
        document.body.style.overflow = '';
    }
});

const handleClose = () => {
    if (!props.loading) {
        emit('close');
    }
};

const handleBackdropClick = (e) => {
    if (e.target === e.currentTarget) {
        handleClose();
    }
};

const updateForm = (field, value) => {
    emit('update:form', { ...props.form, [field]: value });
};

// Computed for current step
const currentStepData = props.steps[props.currentStep];
</script>

<template>
    <Teleport to="body">
        <Transition name="modal">
            <div
                v-if="isOpen"
                class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6"
                @click="handleBackdropClick"
            >
                <!-- Backdrop -->
                <div class="absolute inset-0 bg-black/50 backdrop-blur-sm transition-opacity" />

                <!-- Modal -->
                <div
                    class="relative w-full max-w-4xl max-h-[90vh] bg-white dark:bg-gray-900 rounded-xl shadow-2xl flex flex-col overflow-hidden"
                    @click.stop
                >
                    <!-- Header -->
                    <header class="flex items-center justify-between px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50">
                        <div>
                            <h2 class="text-xl font-semibold text-gray-900 dark:text-white">
                                {{ isEditing ? t('products.edit.title') : t('products.create.title') }}
                            </h2>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">
                                {{ isEditing ? t('products.edit.subtitle') : t('products.create.subtitle') }}
                            </p>
                        </div>
                        <button
                            @click="handleClose"
                            :disabled="loading"
                            class="p-2 rounded-lg text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors disabled:opacity-50"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </header>

                    <!-- Progress Steps -->
                    <div class="px-6 py-4 bg-white dark:bg-gray-900 border-b border-gray-200 dark:border-gray-700">
                        <div class="flex items-center justify-between">
                            <button
                                v-for="(step, index) in steps"
                                :key="step.id"
                                @click="emit('go-to-step', index)"
                                class="flex-1 flex items-center group"
                                :disabled="loading"
                            >
                                <!-- Step circle -->
                                <div
                                    :class="[
                                        'w-10 h-10 rounded-full flex items-center justify-center text-sm font-medium transition-all duration-200',
                                        currentStep === index
                                            ? 'bg-blue-600 text-white ring-4 ring-blue-100 dark:ring-blue-900'
                                            : currentStep > index
                                                ? 'bg-green-500 text-white'
                                                : 'bg-gray-200 dark:bg-gray-700 text-gray-600 dark:text-gray-400',
                                    ]"
                                >
                                    <svg
                                        v-if="currentStep > index"
                                        class="w-5 h-5"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                    <span v-else>{{ index + 1 }}</span>
                                </div>

                                <!-- Step title -->
                                <span
                                    :class="[
                                        'ml-3 text-sm font-medium hidden sm:block transition-colors',
                                        currentStep === index
                                            ? 'text-blue-600 dark:text-blue-400'
                                            : currentStep > index
                                                ? 'text-green-600 dark:text-green-400'
                                                : 'text-gray-500 dark:text-gray-400',
                                    ]"
                                >
                                    {{ t(step.titleKey) }}
                                </span>

                                <!-- Connector line -->
                                <div
                                    v-if="index < steps.length - 1"
                                    :class="[
                                        'flex-1 h-0.5 mx-4 transition-colors',
                                        currentStep > index
                                            ? 'bg-green-500'
                                            : 'bg-gray-200 dark:bg-gray-700',
                                    ]"
                                />
                            </button>
                        </div>
                    </div>

                    <!-- Content -->
                    <div class="flex-1 overflow-y-auto p-6 bg-white dark:bg-gray-900">
                        <!-- Step 1: Basic Info -->
                        <div v-if="currentStep === 0" class="space-y-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    {{ t('products.fields.name') }} *
                                </label>
                                <input
                                    :value="form.name"
                                    @input="updateForm('name', $event.target.value)"
                                    type="text"
                                    class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    :class="{ 'border-red-500': errors.name }"
                                    :placeholder="t('products.placeholders.name')"
                                />
                                <p v-if="errors.name" class="mt-1 text-sm text-red-600">{{ errors.name }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    {{ t('products.fields.description') }}
                                </label>
                                <textarea
                                    :value="form.description"
                                    @input="updateForm('description', $event.target.value)"
                                    rows="4"
                                    class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none"
                                    :placeholder="t('products.placeholders.description')"
                                />
                            </div>

                            <div>
                                <CategorySelector
                                    :categories="props.categories"
                                    v-model="form.category_ids"
                                    :label="t('products.fields.categories')"
                                />
                            </div>
                        </div>

                        <!-- Step 2: Pricing & Stock -->
                        <div v-if="currentStep === 1" class="space-y-6">
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        {{ t('products.fields.price') }} *
                                    </label>
                                    <div class="relative">
                                        <span class="absolute left-3 top-2 text-gray-500">$</span>
                                        <input
                                            :value="form.base_price"
                                            @input="updateForm('base_price', $event.target.value)"
                                            type="number"
                                            step="0.01"
                                            min="0"
                                            class="w-full pl-8 pr-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                            :class="{ 'border-red-500': errors.base_price }"
                                        />
                                    </div>
                                    <p v-if="errors.base_price" class="mt-1 text-sm text-red-600">{{ errors.base_price }}</p>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        {{ t('products.fields.compare_price') }}
                                    </label>
                                    <div class="relative">
                                        <span class="absolute left-3 top-2 text-gray-500">$</span>
                                        <input
                                            :value="form.sale_price"
                                            @input="updateForm('sale_price', $event.target.value)"
                                            type="number"
                                            step="0.01"
                                            min="0"
                                            class="w-full pl-8 pr-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                        />
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        {{ t('products.fields.cost') }}
                                    </label>
                                    <div class="relative">
                                        <span class="absolute left-3 top-2 text-gray-500">$</span>
                                        <input
                                            :value="form.cost"
                                            @input="updateForm('cost', $event.target.value)"
                                            type="number"
                                            step="0.01"
                                            min="0"
                                            class="w-full pl-8 pr-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                        />
                                    </div>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        {{ t('products.fields.sku') }}
                                    </label>
                                    <input
                                        :value="form.sku"
                                        @input="updateForm('sku', $event.target.value)"
                                        type="text"
                                        class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    />
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        {{ t('products.fields.barcode') }}
                                    </label>
                                    <input
                                        :value="form.barcode"
                                        @input="updateForm('barcode', $event.target.value)"
                                        type="text"
                                        class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    />
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        {{ t('products.fields.currency') }}
                                    </label>
                                    <select
                                        :value="form.base_currency"
                                        @change="updateForm('base_currency', $event.target.value)"
                                        class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    >
                                        <option value="USD">USD - US Dollar</option>
                                        <option value="EUR">EUR - Euro</option>
                                        <option value="GBP">GBP - British Pound</option>
                                        <option value="JPY">JPY - Japanese Yen</option>
                                        <option value="CAD">CAD - Canadian Dollar</option>
                                        <option value="AUD">AUD - Australian Dollar</option>
                                    </select>
                                </div>
                            </div>

                            <div class="border-t border-gray-200 dark:border-gray-700 pt-6">
                                <h3 class="text-sm font-medium text-gray-900 dark:text-white mb-4">
                                    {{ t('products.sections.stock_management') }}
                                </h3>

                                <div class="space-y-4">
                                    <div class="flex items-center">
                                        <input
                                            :checked="form.manage_stock"
                                            @change="updateForm('manage_stock', $event.target.checked)"
                                            type="checkbox"
                                            id="manage_stock"
                                            class="w-4 h-4 text-blue-600 rounded border-gray-300 focus:ring-blue-500"
                                        />
                                        <label for="manage_stock" class="ml-2 text-sm text-gray-700 dark:text-gray-300">
                                            {{ t('products.fields.manage_stock') }}
                                        </label>
                                    </div>

                                    <div v-if="form.manage_stock" class="grid grid-cols-1 sm:grid-cols-2 gap-4 ml-6">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                                {{ t('products.fields.stock_quantity') }}
                                            </label>
                                            <input
                                                :value="form.stock_quantity"
                                                @input="updateForm('stock_quantity', parseInt($event.target.value) || 0)"
                                                type="number"
                                                min="0"
                                                class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                            />
                                        </div>

                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                                {{ t('products.fields.low_stock_threshold') }}
                                            </label>
                                            <input
                                                :value="form.low_stock_threshold"
                                                @input="updateForm('low_stock_threshold', parseInt($event.target.value) || 0)"
                                                type="number"
                                                min="0"
                                                class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                            />
                                        </div>
                                    </div>

                                    <div class="flex items-center ml-6" v-if="form.manage_stock">
                                        <input
                                            :checked="form.allow_backorders"
                                            @change="updateForm('allow_backorders', $event.target.checked)"
                                            type="checkbox"
                                            id="allow_backorders"
                                            class="w-4 h-4 text-blue-600 rounded border-gray-300 focus:ring-blue-500"
                                        />
                                        <label for="allow_backorders" class="ml-2 text-sm text-gray-700 dark:text-gray-300">
                                            {{ t('products.fields.allow_backorders') }}
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    {{ t('products.fields.status') }}
                                </label>
                                <select
                                    :value="form.status"
                                    @change="updateForm('status', $event.target.value)"
                                    class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                >
                                    <option value="draft">{{ t('products.status.draft') }}</option>
                                    <option value="active">{{ t('products.status.active') }}</option>
                                    <option value="inactive">{{ t('products.status.inactive') }}</option>
                                </select>
                            </div>
                        </div>

                        <!-- Step 3: Images -->
                        <div v-if="currentStep === 2" class="space-y-6">
                            <ImageUploader
                                v-model="form.images"
                                :product-id="form.id"
                                :max-images="10"
                            />
                        </div>

                        <!-- Step 4: SEO -->
                        <div v-if="currentStep === 3" class="space-y-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    {{ t('products.fields.seo_title') }}
                                </label>
                                <input
                                    :value="form.seo_title"
                                    @input="updateForm('seo_title', $event.target.value)"
                                    type="text"
                                    class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    :placeholder="form.name"
                                />
                                <p class="mt-1 text-xs text-gray-500">
                                    {{ t('products.seo.title_hint') }}
                                </p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    {{ t('products.fields.seo_description') }}
                                </label>
                                <textarea
                                    :value="form.seo_description"
                                    @input="updateForm('seo_description', $event.target.value)"
                                    rows="3"
                                    class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none"
                                />
                                <p class="mt-1 text-xs text-gray-500">
                                    {{ t('products.seo.description_hint') }}
                                </p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    {{ t('products.fields.seo_keywords') }}
                                </label>
                                <input
                                    :value="form.seo_keywords"
                                    @input="updateForm('seo_keywords', $event.target.value)"
                                    type="text"
                                    class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    :placeholder="t('products.seo.keywords_placeholder')"
                                />
                                <p class="mt-1 text-xs text-gray-500">
                                    {{ t('products.seo.keywords_hint') }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Footer -->
                    <footer class="flex items-center justify-between px-6 py-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50">
                        <button
                            @click="emit('prev-step')"
                            :disabled="currentStep === 0 || loading"
                            class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                        >
                            {{ t('common.previous') }}
                        </button>

                        <div class="flex items-center space-x-3">
                            <button
                                @click="handleClose"
                                :disabled="loading"
                                class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white transition-colors"
                            >
                                {{ t('common.cancel') }}
                            </button>

                            <button
                                v-if="currentStep < steps.length - 1"
                                @click="emit('next-step')"
                                :disabled="loading"
                                class="px-6 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors disabled:opacity-50"
                            >
                                {{ t('common.next') }}
                            </button>

                            <button
                                v-else
                                @click="emit('save')"
                                :disabled="loading"
                                class="px-6 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 transition-colors disabled:opacity-50 flex items-center"
                            >
                                <svg
                                    v-if="loading"
                                    class="animate-spin -ml-1 mr-2 h-4 w-4 text-white"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                >
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z" />
                                </svg>
                                {{ isEditing ? t('common.save_changes') : t('common.create') }}
                            </button>
                        </div>
                    </footer>
                </div>
            </div>
        </Transition>
    </Teleport>
</template>

<style scoped>
.modal-enter-active,
.modal-leave-active {
    transition: opacity 0.3s ease;
}

.modal-enter-from,
.modal-leave-to {
    opacity: 0;
}

.modal-enter-active .relative,
.modal-leave-active .relative {
    transition: transform 0.3s ease, opacity 0.3s ease;
}

.modal-enter-from .relative,
.modal-leave-to .relative {
    transform: scale(0.95);
    opacity: 0;
}
</style>
