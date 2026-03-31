<script setup>
import { Link, useForm, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import { useTranslations } from '@/composables/useTranslations';

const props = defineProps({
    product: Object,
    categories: Array,
});

const { t } = useTranslations();

const form = useForm({
    name: props.product.name,
    description: props.product.description || '',
    sku: props.product.sku,
    slug: props.product.slug,
    base_price: props.product.base_price,
    sale_price: props.product.sale_price || '',
    base_currency: props.product.base_currency,
    manage_stock: props.product.manage_stock || false,
    stock_quantity: props.product.stock_quantity || 0,
    low_stock_threshold: props.product.low_stock_threshold || 5,
    status: props.product.status,
    is_featured: props.product.is_featured || false,
    is_on_sale: props.product.is_on_sale || false,
    category_id: props.product.category_id || '',
    tags: props.product.tags?.map(tag => tag.id) || [],
});

const isGeneratingSku = ref(false);
const isGeneratingSlug = ref(false);

// Generate SKU from name
const generateSku = () => {
    if (!form.name) return;
    
    isGeneratingSku.value = true;
    // Simulate API call to generate unique SKU
    setTimeout(() => {
        const baseSku = form.name
            .replace(/[^a-zA-Z0-9]/g, '')
            .toUpperCase()
            .substring(0, 8);
        form.sku = baseSku + Math.floor(Math.random() * 1000).toString().padStart(3, '0');
        isGeneratingSku.value = false;
    }, 500);
};

// Generate slug from name
const generateSlug = () => {
    if (!form.name) return;
    
    isGeneratingSlug.value = true;
    setTimeout(() => {
        form.slug = form.name
            .toLowerCase()
            .replace(/[^a-z0-9]+/g, '-')
            .replace(/^-+|-+$/g, '');
        isGeneratingSlug.value = false;
    }, 300);
};

// Submit form
const submit = () => {
    form.put(`/products/${props.product.id}`, {
        onSuccess: () => {
            router.visit(`/products/${props.product.id}`);
        },
    });
};

// Cancel and go back
const cancel = () => {
    router.visit(`/products/${props.product.id}`);
};
</script>

<template>
    <div class="max-w-4xl mx-auto">
        <div class="flex justify-between items-center mb-6">
            <div class="flex items-center space-x-4">
                <Link
                    :href="`/products/${product.id}`"
                    class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </Link>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ t('products.form.update') }}</h1>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        {{ t('products.management') }}
                    </p>
                </div>
            </div>
            <div class="flex space-x-3">
                <button
                    @click="cancel"
                    class="px-4 py-2 text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors"
                >
                    {{ t('products.form.cancel') }}
                </button>
                <button
                    @click="submit"
                    :disabled="form.processing"
                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                >
                    <span v-if="form.processing">{{ t('products.messages.loading') }}</span>
                    <span v-else>{{ t('products.form.update') }}</span>
                </button>
            </div>
        </div>

        <form @submit.prevent="submit" class="space-y-6">
                <!-- Basic Information -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <h2 class="text-lg font-medium text-gray-900 dark:text-white mb-4">{{ t('products.sections.basic_info') }}</h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                {{ t('products.form.name') }} *
                            </label>
                            <input
                                v-model="form.name"
                                type="text"
                                required
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                                :class="{ 'border-red-500': form.errors.name }"
                            />
                            <p v-if="form.errors.name" class="mt-1 text-sm text-red-600 dark:text-red-400">
                                {{ form.errors.name }}
                            </p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                {{ t('products.form.sku') }}
                            </label>
                            <div class="flex space-x-2">
                                <input
                                    v-model="form.sku"
                                    type="text"
                                    class="flex-1 px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                                    :class="{ 'border-red-500': form.errors.sku }"
                                />
                                <button
                                    type="button"
                                    @click="generateSku"
                                    :disabled="isGeneratingSku"
                                    class="px-3 py-2 text-sm bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 disabled:opacity-50"
                                >
                                    <span v-if="isGeneratingSku">...</span>
                                    <span v-else>Generate</span>
                                </button>
                            </div>
                            <p v-if="form.errors.sku" class="mt-1 text-sm text-red-600 dark:text-red-400">
                                {{ form.errors.sku }}
                            </p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                {{ t('products.form.slug') }}
                            </label>
                            <div class="flex space-x-2">
                                <input
                                    v-model="form.slug"
                                    type="text"
                                    class="flex-1 px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                                    :class="{ 'border-red-500': form.errors.slug }"
                                />
                                <button
                                    type="button"
                                    @click="generateSlug"
                                    :disabled="isGeneratingSlug"
                                    class="px-3 py-2 text-sm bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 disabled:opacity-50"
                                >
                                    <span v-if="isGeneratingSlug">...</span>
                                    <span v-else>Generate</span>
                                </button>
                            </div>
                            <p v-if="form.errors.slug" class="mt-1 text-sm text-red-600 dark:text-red-400">
                                {{ form.errors.slug }}
                            </p>
                        </div>
                        
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                {{ t('products.form.description') }}
                            </label>
                            <textarea
                                v-model="form.description"
                                rows="4"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                                :class="{ 'border-red-500': form.errors.description }"
                            ></textarea>
                            <p v-if="form.errors.description" class="mt-1 text-sm text-red-600 dark:text-red-400">
                                {{ form.errors.description }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Pricing -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <h2 class="text-lg font-medium text-gray-900 dark:text-white mb-4">{{ t('products.sections.pricing') }}</h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                {{ t('products.form.base_price') }} *
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <span class="text-gray-500 dark:text-gray-400">$</span>
                                </div>
                                <input
                                    v-model="form.base_price"
                                    type="number"
                                    step="0.01"
                                    min="0"
                                    required
                                    class="w-full pl-7 pr-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                                    :class="{ 'border-red-500': form.errors.base_price }"
                                />
                            </div>
                            <p v-if="form.errors.base_price" class="mt-1 text-sm text-red-600 dark:text-red-400">
                                {{ form.errors.base_price }}
                            </p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                {{ t('products.form.sale_price') }}
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <span class="text-gray-500 dark:text-gray-400">$</span>
                                </div>
                                <input
                                    v-model="form.sale_price"
                                    type="number"
                                    step="0.01"
                                    min="0"
                                    class="w-full pl-7 pr-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                                    :class="{ 'border-red-500': form.errors.sale_price }"
                                />
                            </div>
                            <p v-if="form.errors.sale_price" class="mt-1 text-sm text-red-600 dark:text-red-400">
                                {{ form.errors.sale_price }}
                            </p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Currency *
                            </label>
                            <select
                                v-model="form.base_currency"
                                required
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                                :class="{ 'border-red-500': form.errors.base_currency }"
                            >
                                <option value="USD">USD - US Dollar</option>
                                <option value="EUR">EUR - Euro</option>
                                <option value="GBP">GBP - British Pound</option>
                                <option value="JPY">JPY - Japanese Yen</option>
                                <option value="CAD">CAD - Canadian Dollar</option>
                                <option value="AUD">AUD - Australian Dollar</option>
                            </select>
                            <p v-if="form.errors.base_currency" class="mt-1 text-sm text-red-600 dark:text-red-400">
                                {{ form.errors.base_currency }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Stock Management -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <h2 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Stock Management</h2>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="flex items-center">
                                <input
                                    v-model="form.manage_stock"
                                    type="checkbox"
                                    class="rounded border-gray-300 text-blue-600 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700"
                                />
                                <span class="ml-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Enable stock management
                                </span>
                            </label>
                        </div>
                        
                        <div v-if="form.manage_stock" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Stock Quantity *
                                </label>
                                <input
                                    v-model="form.stock_quantity"
                                    type="number"
                                    min="0"
                                    required
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                                    :class="{ 'border-red-500': form.errors.stock_quantity }"
                                />
                                <p v-if="form.errors.stock_quantity" class="mt-1 text-sm text-red-600 dark:text-red-400">
                                    {{ form.errors.stock_quantity }}
                                </p>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Low Stock Threshold *
                                </label>
                                <input
                                    v-model="form.low_stock_threshold"
                                    type="number"
                                    min="1"
                                    required
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                                    :class="{ 'border-red-500': form.errors.low_stock_threshold }"
                                />
                                <p v-if="form.errors.low_stock_threshold" class="mt-1 text-sm text-red-600 dark:text-red-400">
                                    {{ form.errors.low_stock_threshold }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Status and Visibility -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <h2 class="text-lg font-medium text-gray-900 dark:text-white mb-4">{{ t('products.sections.status_visibility') }}</h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                {{ t('products.form.status') }} *
                            </label>
                            <select
                                v-model="form.status"
                                required
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                                :class="{ 'border-red-500': form.errors.status }"
                            >
                                <option value="draft">{{ t('products.status.draft') }}</option>
                                <option value="active">{{ t('products.status.active') }}</option>
                                <option value="inactive">{{ t('products.status.inactive') }}</option>
                            </select>
                            <p v-if="form.errors.status" class="mt-1 text-sm text-red-600 dark:text-red-400">
                                {{ form.errors.status }}
                            </p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                {{ t('products.form.category') }}
                            </label>
                            <select
                                v-model="form.category_id"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                                :class="{ 'border-red-500': form.errors.category_id }"
                            >
                                <option value="">{{ t('products.placeholders.no_category') }}</option>
                                <option v-for="category in categories" :key="category.id" :value="category.id">
                                    {{ category.name }}
                                </option>
                            </select>
                            <p v-if="form.errors.category_id" class="mt-1 text-sm text-red-600 dark:text-red-400">
                                {{ form.errors.category_id }}
                            </p>
                        </div>
                        
                        <div>
                            <label class="flex items-center">
                                <input
                                    v-model="form.is_featured"
                                    type="checkbox"
                                    class="rounded border-gray-300 text-blue-600 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700"
                                />
                                <span class="ml-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                                    {{ t('products.form.featured') }}
                                </span>
                            </label>
                        </div>
                        
                        <div>
                            <label class="flex items-center">
                                <input
                                    v-model="form.is_on_sale"
                                    type="checkbox"
                                    class="rounded border-gray-300 text-blue-600 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700"
                                />
                                <span class="ml-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                                    {{ t('products.form.on_sale') }}
                                </span>
                            </label>
                        </div>
                    </div>
                </div>
            </form>
    </div>
</template>
