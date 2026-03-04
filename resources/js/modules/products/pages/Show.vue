<template>
    <div class="products-show">
        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <div class="flex items-center gap-4">
                <Link
                    :href="route('products.index')"
                    class="text-gray-600 hover:text-gray-900"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                </Link>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">{{ product.name }}</h1>
                    <p class="text-gray-600 mt-1">Product Details</p>
                </div>
            </div>
            <div class="flex gap-3">
                <button
                    @click="duplicateProduct"
                    class="btn btn-outline"
                >
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                    </svg>
                    Duplicate
                </button>
                <Link
                    :href="route('products.edit', product)"
                    class="btn btn-primary"
                >
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                    Edit Product
                </Link>
            </div>
        </div>

        <!-- Product Overview Card -->
        <div class="bg-white rounded-lg shadow-lg overflow-hidden mb-6">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 p-6">
                <!-- Product Images -->
                <div>
                    <div class="aspect-square bg-gray-100 rounded-lg overflow-hidden mb-4">
                        <img
                            v-if="primaryImage"
                            :src="primaryImage.url"
                            :alt="product.name"
                            class="w-full h-full object-cover"
                        >
                        <div v-else class="w-full h-full flex items-center justify-center">
                            <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4 4m0 0l-4-4m4 4l4 4m-2-2h-8m-2 2h8"></path>
                            </svg>
                        </div>
                    </div>
                    
                    <!-- Image Gallery -->
                    <div v-if="product.images && product.images.length > 1" class="grid grid-cols-4 gap-2">
                        <div
                            v-for="(image, index) in product.images"
                            :key="image.id"
                            class="aspect-square bg-gray-100 rounded overflow-hidden cursor-pointer hover:opacity-75"
                            @click="selectedImageIndex = index"
                        >
                            <img
                                :src="image.url"
                                :alt="image.alt || `Image ${index + 1}`"
                                class="w-full h-full object-cover"
                            >
                        </div>
                    </div>
                </div>
                
                <!-- Product Information -->
                <div>
                    <!-- Status Badges -->
                    <div class="flex flex-wrap gap-2 mb-4">
                        <span :class="getStatusClass(product.status)" class="px-2 py-1 rounded-full text-xs font-medium">
                            {{ product.status }}
                        </span>
                        <span v-if="product.featured" class="bg-yellow-100 text-yellow-800 px-2 py-1 rounded-full text-xs font-medium">
                            Featured
                        </span>
                        <span v-if="product.is_on_sale" class="bg-red-100 text-red-800 px-2 py-1 rounded-full text-xs font-medium">
                            On Sale
                        </span>
                        <span v-if="isLowStock" class="bg-orange-100 text-orange-800 px-2 py-1 rounded-full text-xs font-medium">
                            Low Stock
                        </span>
                    </div>
                    
                    <!-- SKU and Barcode -->
                    <div class="grid grid-cols-2 gap-4 mb-6">
                        <div>
                            <p class="text-sm text-gray-600">SKU</p>
                            <p class="font-medium text-gray-900">{{ product.sku }}</p>
                        </div>
                        <div v-if="product.barcode">
                            <p class="text-sm text-gray-600">Barcode</p>
                            <p class="font-medium text-gray-900">{{ product.barcode }}</p>
                        </div>
                    </div>
                    
                    <!-- Description -->
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Description</h3>
                        <div v-if="product.description" class="text-gray-700 whitespace-pre-wrap">
                            {{ product.description }}
                        </div>
                        <p v-else class="text-gray-500 italic">No description provided</p>
                    </div>
                    
                    <!-- Categories and Tags -->
                    <div class="mb-6">
                        <div v-if="product.categories && product.categories.length > 0" class="mb-4">
                            <h3 class="text-sm font-semibold text-gray-900 mb-2">Categories</h3>
                            <div class="flex flex-wrap gap-2">
                                <span
                                    v-for="category in product.categories"
                                    :key="category.id"
                                    class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-gray-100 text-gray-800"
                                >
                                    {{ category.name }}
                                </span>
                            </div>
                        </div>
                        
                        <div v-if="product.tags && product.tags.length > 0">
                            <h3 class="text-sm font-semibold text-gray-900 mb-2">Tags</h3>
                            <div class="flex flex-wrap gap-2">
                                <span
                                    v-for="tag in product.tags"
                                    :key="tag.id"
                                    class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-blue-100 text-blue-800"
                                >
                                    {{ tag.name }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabs -->
        <div class="bg-white rounded-lg shadow">
            <div class="border-b">
                <nav class="flex -mb-px">
                    <button
                        v-for="tab in tabs"
                        :key="tab.key"
                        @click="activeTab = tab.key"
                        :class="[
                            'px-6 py-3 text-sm font-medium border-b-2',
                            activeTab === tab.key
                                ? 'border-blue-500 text-blue-600'
                                : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
                        ]"
                    >
                        {{ tab.label }}
                    </button>
                </nav>
            </div>
            
            <div class="p-6">
                <!-- Pricing Tab -->
                <div v-show="activeTab === 'pricing'">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Pricing Information</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Current Prices -->
                        <div class="bg-gray-50 rounded-lg p-4">
                            <h4 class="font-medium text-gray-900 mb-3">Current Prices</h4>
                            <div class="space-y-2">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Regular Price:</span>
                                    <span class="font-medium">{{ formatPrice(product.base_price, product.base_currency) }}</span>
                                </div>
                                <div v-if="product.is_on_sale && product.base_sale_price" class="flex justify-between">
                                    <span class="text-gray-600">Sale Price:</span>
                                    <span class="font-medium text-green-600">{{ formatPrice(product.base_sale_price, product.base_currency) }}</span>
                                </div>
                                <div v-if="product.cost_price" class="flex justify-between">
                                    <span class="text-gray-600">Cost Price:</span>
                                    <span class="font-medium">{{ formatPrice(product.cost_price, product.base_currency) }}</span>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Sale Settings -->
                        <div v-if="product.is_on_sale" class="bg-blue-50 rounded-lg p-4">
                            <h4 class="font-medium text-gray-900 mb-3">Sale Settings</h4>
                            <div class="space-y-2">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Sale Type:</span>
                                    <span class="font-medium capitalize">{{ product.sale_type }}</span>
                                </div>
                                <div v-if="product.sale_discount" class="flex justify-between">
                                    <span class="text-gray-600">Discount:</span>
                                    <span class="font-medium">
                                        {{ product.sale_discount }}{{ product.sale_type === 'percentage' ? '%' : '' }}
                                    </span>
                                </div>
                                <div v-if="product.sale_start_date" class="flex justify-between">
                                    <span class="text-gray-600">Start Date:</span>
                                    <span class="font-medium">{{ formatDate(product.sale_start_date) }}</span>
                                </div>
                                <div v-if="product.sale_end_date" class="flex justify-between">
                                    <span class="text-gray-600">End Date:</span>
                                    <span class="font-medium">{{ formatDate(product.sale_end_date) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Calculated Prices -->
                    <div v-if="calculatedPrices" class="mt-6">
                        <h4 class="font-medium text-gray-900 mb-3">Prices in Other Currencies</h4>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div
                                v-for="(price, currency) in calculatedPrices"
                                :key="currency"
                                class="bg-gray-50 rounded-lg p-3"
                            >
                                <div class="text-sm text-gray-600">{{ currency }}</div>
                                <div class="font-medium text-gray-900">{{ formatPrice(price, currency) }}</div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Inventory Tab -->
                <div v-show="activeTab === 'inventory'">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Inventory Information</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Stock Status -->
                        <div class="bg-gray-50 rounded-lg p-4">
                            <h4 class="font-medium text-gray-900 mb-3">Stock Status</h4>
                            <div class="space-y-2">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Stock Management:</span>
                                    <span class="font-medium">{{ product.manage_stock ? 'Enabled' : 'Disabled' }}</span>
                                </div>
                                <div v-if="product.manage_stock" class="flex justify-between">
                                    <span class="text-gray-600">Stock Quantity:</span>
                                    <span class="font-medium">{{ product.stock_quantity }}</span>
                                </div>
                                <div v-if="product.manage_stock" class="flex justify-between">
                                    <span class="text-gray-600">Low Stock Threshold:</span>
                                    <span class="font-medium">{{ product.low_stock_threshold }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Stock Status:</span>
                                    <span class="font-medium capitalize">{{ product.stock_status }}</span>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Stock Indicator -->
                        <div v-if="product.manage_stock" class="bg-gray-50 rounded-lg p-4">
                            <h4 class="font-medium text-gray-900 mb-3">Stock Level</h4>
                            <div class="mb-2">
                                <div class="w-full bg-gray-200 rounded-full h-4">
                                    <div
                                        :class="getStockBarClass()"
                                        :style="{ width: getStockPercentage() + '%' }"
                                        class="h-4 rounded-full transition-all duration-300"
                                    ></div>
                                </div>
                            </div>
                            <p class="text-sm text-gray-600">
                                {{ getStockStatusText() }}
                            </p>
                        </div>
                    </div>
                </div>
                
                <!-- Shipping Tab -->
                <div v-show="activeTab === 'shipping'">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Shipping Information</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Shipping Settings -->
                        <div class="bg-gray-50 rounded-lg p-4">
                            <h4 class="font-medium text-gray-900 mb-3">Shipping Settings</h4>
                            <div class="space-y-2">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Virtual Product:</span>
                                    <span class="font-medium">{{ product.virtual ? 'Yes' : 'No' }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Downloadable:</span>
                                    <span class="font-medium">{{ product.downloadable ? 'Yes' : 'No' }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Free Shipping:</span>
                                    <span class="font-medium">{{ product.free_shipping ? 'Yes' : 'No' }}</span>
                                </div>
                                <div v-if="product.shipping_class" class="flex justify-between">
                                    <span class="text-gray-600">Shipping Class:</span>
                                    <span class="font-medium">{{ product.shipping_class }}</span>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Dimensions -->
                        <div v-if="!product.virtual && (product.weight || product.length || product.width || product.height)" class="bg-gray-50 rounded-lg p-4">
                            <h4 class="font-medium text-gray-900 mb-3">Dimensions & Weight</h4>
                            <div class="space-y-2">
                                <div v-if="product.weight" class="flex justify-between">
                                    <span class="text-gray-600">Weight:</span>
                                    <span class="font-medium">{{ product.weight }} kg</span>
                                </div>
                                <div v-if="product.length" class="flex justify-between">
                                    <span class="text-gray-600">Length:</span>
                                    <span class="font-medium">{{ product.length }} cm</span>
                                </div>
                                <div v-if="product.width" class="flex justify-between">
                                    <span class="text-gray-600">Width:</span>
                                    <span class="font-medium">{{ product.width }} cm</span>
                                </div>
                                <div v-if="product.height" class="flex justify-between">
                                    <span class="text-gray-600">Height:</span>
                                    <span class="font-medium">{{ product.height }} cm</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Variants Tab -->
                <div v-show="activeTab === 'variants'">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Product Variants</h3>
                    
                    <div v-if="product.variants && product.variants.length > 0">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">SKU</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Attributes</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Price</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Stock</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr v-for="variant in product.variants" :key="variant.id">
                                        <td class="px-6 py-4 text-sm text-gray-900">{{ variant.sku }}</td>
                                        <td class="px-6 py-4 text-sm text-gray-900">{{ variant.getAttributesString() }}</td>
                                        <td class="px-6 py-4 text-sm text-gray-900">{{ formatPrice(variant.getCurrentPrice(), product.base_currency) }}</td>
                                        <td class="px-6 py-4 text-sm text-gray-900">{{ variant.stock_quantity }}</td>
                                        <td class="px-6 py-4 text-sm text-gray-900">{{ variant.status }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div v-else class="text-center py-8 text-gray-500">
                        No variants available for this product
                    </div>
                </div>
                
                <!-- History Tab -->
                <div v-show="activeTab === 'history'">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Price History</h3>
                    
                    <div v-if="product.price_history && product.price_history.length > 0">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Currency</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Old Price</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">New Price</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Reason</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr v-for="history in product.price_history" :key="history.id">
                                        <td class="px-6 py-4 text-sm text-gray-900">{{ formatDate(history.created_at) }}</td>
                                        <td class="px-6 py-4 text-sm text-gray-900">{{ history.currency }}</td>
                                        <td class="px-6 py-4 text-sm text-gray-900">{{ formatPrice(history.old_price, history.currency) }}</td>
                                        <td class="px-6 py-4 text-sm text-gray-900">{{ formatPrice(history.new_price, history.currency) }}</td>
                                        <td class="px-6 py-4 text-sm text-gray-900 capitalize">{{ history.change_reason }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div v-else class="text-center py-8 text-gray-500">
                        No price history available for this product
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { router, Link } from '@inertiajs/vue3'

const props = defineProps({
    product: Object,
    calculatedPrices: Object,
})

const activeTab = ref('pricing')
const selectedImageIndex = ref(0)

const tabs = [
    { key: 'pricing', label: 'Pricing' },
    { key: 'inventory', label: 'Inventory' },
    { key: 'shipping', label: 'Shipping' },
    { key: 'variants', label: 'Variants' },
    { key: 'history', label: 'History' },
]

// Computed
const primaryImage = computed(() => {
    return props.product.images?.find(img => img.is_primary) || props.product.images?.[selectedImageIndex.value] || props.product.images?.[0]
})

const isLowStock = computed(() => {
    if (!props.product.manage_stock) return false
    return props.product.stock_quantity <= props.product.low_stock_threshold && props.product.stock_quantity > 0
})

// Methods
const duplicateProduct = () => {
    router.post(route('products.duplicate', props.product), {}, {
        onSuccess: () => {
            // Show success message
        }
    })
}

const formatPrice = (price, currency) => {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: currency,
    }).format(price)
}

const formatDate = (date) => {
    return new Date(date).toLocaleDateString()
}

const getStatusClass = (status) => {
    switch (status) {
        case 'active':
            return 'bg-green-100 text-green-800'
        case 'inactive':
            return 'bg-gray-100 text-gray-800'
        case 'draft':
            return 'bg-yellow-100 text-yellow-800'
        default:
            return 'bg-gray-100 text-gray-800'
    }
}

const getStockStatusText = () => {
    if (!props.product.manage_stock) return 'Unlimited'
    
    if (props.product.stock_quantity === 0) return 'Out of Stock'
    if (props.product.stock_quantity <= props.product.low_stock_threshold) return `Low Stock (${props.product.stock_quantity})`
    
    return `In Stock (${props.product.stock_quantity})`
}

const getStockBarClass = () => {
    if (!props.product.manage_stock) return 'bg-gray-400'
    
    if (props.product.stock_quantity === 0) return 'bg-red-500'
    if (props.product.stock_quantity <= props.product.low_stock_threshold) return 'bg-yellow-500'
    
    return 'bg-green-500'
}

const getStockPercentage = () => {
    if (!props.product.manage_stock) return 100
    
    const maxStock = props.product.low_stock_threshold * 4
    return Math.min((props.product.stock_quantity / maxStock) * 100, 100)
}
</script>

<style scoped>
.btn {
    @apply inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2;
}

.btn-primary {
    @apply text-white bg-blue-600 hover:bg-blue-700 focus:ring-blue-500;
}

.btn-outline {
    @apply text-gray-700 bg-white border-gray-300 hover:bg-gray-50 focus:ring-blue-500;
}
</style>
