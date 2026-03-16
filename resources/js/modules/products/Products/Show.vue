<script setup>
import { Link } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    product: Object,
});

// Format currency
const formatCurrency = (amount, currency = 'USD') => {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: currency,
    }).format(amount);
};

// Get status badge class
const getStatusClass = (status) => {
    const classes = {
        active: 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
        inactive: 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200',
        draft: 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
    };
    return classes[status] || 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200';
};

// Check if product is low stock
const isLowStock = computed(() => {
    return props.product.manage_stock && props.product.stock_quantity <= props.product.low_stock_threshold;
});

// Check if product is out of stock
const isOutOfStock = computed(() => {
    return props.product.manage_stock && props.product.stock_quantity === 0;
});

// Get stock status class
const getStockStatusClass = () => {
    if (isOutOfStock.value) return 'text-red-600 dark:text-red-400';
    if (isLowStock.value) return 'text-yellow-600 dark:text-yellow-400';
    return 'text-green-600 dark:text-green-400';
};

// Get stock status text
const getStockStatusText = () => {
    if (isOutOfStock.value) return 'Out of Stock';
    if (isLowStock.value) return 'Low Stock';
    return 'In Stock';
};
</script>

<template>
    <div class="max-w-7xl mx-auto">
        <div class="flex justify-between items-center mb-6">
            <div class="flex items-center space-x-4">
                <Link
                    href="/products"
                    class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </Link>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ product.name }}</h1>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        SKU: {{ product.sku }}
                    </p>
                </div>
            </div>
            <div class="flex space-x-3">
                <Link
                    :href="`/products/${product.id}/edit`"
                    class="inline-flex items-center px-4 py-2 bg-blue-600 dark:bg-blue-500 text-white rounded-xl hover:bg-blue-700 dark:hover:bg-blue-600 transition-all duration-200 shadow-md hover:shadow-lg"
                >
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    Edit Product
                </Link>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Main Content -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Product Image -->
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                        <h2 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Product Image</h2>
                        <div class="aspect-w-16 aspect-h-9">
                            <div class="bg-gray-200 dark:bg-gray-700 rounded-lg overflow-hidden">
                                <img
                                    v-if="product.image"
                                    :src="product.image"
                                    :alt="product.name"
                                    class="w-full h-64 object-cover"
                                />
                                <div v-else class="w-full h-64 flex items-center justify-center">
                                    <svg class="w-12 h-12 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Product Details -->
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                        <h2 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Product Details</h2>
                        
                        <div class="space-y-4">
                            <div>
                                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Description</h3>
                                <p class="mt-1 text-gray-900 dark:text-white">
                                    {{ product.description || 'No description provided' }}
                                </p>
                            </div>
                            
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Category</h3>
                                    <p class="mt-1 text-gray-900 dark:text-white">
                                        {{ product.category?.name || 'No category' }}
                                    </p>
                                </div>
                                <div>
                                    <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Status</h3>
                                    <div class="mt-1">
                                        <span :class="`inline-flex px-2 py-1 text-xs font-semibold rounded-full ${getStatusClass(product.status)}`">
                                            {{ product.status }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Created</h3>
                                    <p class="mt-1 text-gray-900 dark:text-white">
                                        {{ new Date(product.created_at).toLocaleDateString() }}
                                    </p>
                                </div>
                                <div>
                                    <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Last Updated</h3>
                                    <p class="mt-1 text-gray-900 dark:text-white">
                                        {{ new Date(product.updated_at).toLocaleDateString() }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tags -->
                    <div v-if="product.tags && product.tags.length > 0" class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                        <h2 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Tags</h2>
                        <div class="flex flex-wrap gap-2">
                            <span
                                v-for="tag in product.tags"
                                :key="tag.id"
                                class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200"
                            >
                                {{ tag.name }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="space-y-6">
                    <!-- Pricing -->
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                        <h2 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Pricing</h2>
                        
                        <div class="space-y-3">
                            <div>
                                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Base Price</h3>
                                <p class="mt-1 text-2xl font-bold text-gray-900 dark:text-white">
                                    {{ formatCurrency(product.base_price, product.base_currency) }}
                                </p>
                            </div>
                            
                            <div v-if="product.sale_price">
                                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Sale Price</h3>
                                <p class="mt-1 text-xl font-bold text-green-600 dark:text-green-400">
                                    {{ formatCurrency(product.sale_price, product.base_currency) }}
                                </p>
                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    Save {{ formatCurrency(product.base_price - product.sale_price, product.base_currency) }}
                                </p>
                            </div>
                            
                            <div>
                                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Currency</h3>
                                <p class="mt-1 text-gray-900 dark:text-white">{{ product.base_currency }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Stock Information -->
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                        <h2 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Stock Information</h2>
                        
                        <div class="space-y-3">
                            <div>
                                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Stock Status</h3>
                                <p :class="`mt-1 font-medium ${getStockStatusClass()}`">
                                    {{ getStockStatusText() }}
                                </p>
                            </div>
                            
                            <div v-if="product.manage_stock">
                                <div>
                                    <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Current Stock</h3>
                                    <p class="mt-1 text-lg font-semibold text-gray-900 dark:text-white">
                                        {{ product.stock_quantity }} units
                                    </p>
                                </div>
                                
                                <div>
                                    <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Low Stock Threshold</h3>
                                    <p class="mt-1 text-gray-900 dark:text-white">
                                        {{ product.low_stock_threshold }} units
                                    </p>
                                </div>
                            </div>
                            
                            <div v-else>
                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    Stock management is disabled for this product
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Product Flags -->
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                        <h2 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Product Flags</h2>
                        
                        <div class="space-y-3">
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Featured</span>
                                <span
                                    :class="{
                                        'inline-flex px-2 py-1 text-xs font-semibold rounded-full': true,
                                        'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200': product.is_featured,
                                        'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200': !product.is_featured
                                    }"
                                >
                                    {{ product.is_featured ? 'Yes' : 'No' }}
                                </span>
                            </div>
                            
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">On Sale</span>
                                <span
                                    :class="{
                                        'inline-flex px-2 py-1 text-xs font-semibold rounded-full': true,
                                        'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200': product.is_on_sale,
                                        'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200': !product.is_on_sale
                                    }"
                                >
                                    {{ product.is_on_sale ? 'Yes' : 'No' }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                        <h2 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Actions</h2>
                        
                        <div class="space-y-3">
                            <Link
                                :href="`/products/${product.id}/edit`"
                                class="w-full inline-flex items-center justify-center px-4 py-2 bg-blue-600 dark:bg-blue-500 text-white rounded-lg hover:bg-blue-700 dark:hover:bg-blue-600 transition-colors"
                            >
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                                Edit Product
                            </Link>
                            
                            <button
                                @click="duplicateProduct(product.id)"
                                class="w-full inline-flex items-center justify-center px-4 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors"
                            >
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                </svg>
                                Duplicate Product
                            </button>
                            
                            <button
                                @click="deleteProduct(product.id)"
                                class="w-full inline-flex items-center justify-center px-4 py-2 bg-red-100 dark:bg-red-900 text-red-700 dark:text-red-300 rounded-lg hover:bg-red-200 dark:hover:bg-red-800 transition-colors"
                            >
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                                Delete Product
                            </button>
                        </div>
                    </div>
                </div>
        </div>
    </div>
</template>
