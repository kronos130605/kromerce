<template>
    <div class="product-card bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow duration-200">
        <!-- Product Image -->
        <div class="relative h-48 bg-gray-100">
            <img
                v-if="primaryImage"
                :src="primaryImage.url"
                :alt="product.name"
                class="w-full h-full object-cover"
                @error="imageError = true"
            >
            <div v-else class="w-full h-full flex items-center justify-center bg-gray-100">
                <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4 4m0 0l-4-4m4 4l4 4m-2-2h-8m-2 2h8"></path>
                </svg>
            </div>
            
            <!-- Status Badges -->
            <div class="absolute top-2 left-2 flex flex-col gap-1">
                <span v-if="product.is_on_sale" class="bg-red-500 text-white text-xs px-2 py-1 rounded-full font-medium">
                    Sale
                </span>
                <span v-if="product.featured" class="bg-yellow-500 text-white text-xs px-2 py-1 rounded-full font-medium">
                    Featured
                </span>
                <span v-if="isLowStock" class="bg-orange-500 text-white text-xs px-2 py-1 rounded-full font-medium">
                    Low Stock
                </span>
            </div>
            
            <!-- Quick Actions -->
            <div class="absolute top-2 right-2 opacity-0 hover:opacity-100 transition-opacity">
                <div class="flex flex-col gap-1">
                    <button
                        @click="$emit('edit', product)"
                        class="p-1.5 bg-white rounded-full shadow-md hover:bg-blue-50 text-blue-600"
                        title="Edit product"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                    </button>
                    <button
                        @click="$emit('duplicate', product)"
                        class="p-1.5 bg-white rounded-full shadow-md hover:bg-gray-50 text-gray-600"
                        title="Duplicate product"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Product Info -->
        <div class="p-4">
            <!-- Product Name -->
            <h3 class="font-semibold text-gray-900 text-lg mb-1 line-clamp-2">
                {{ product.name }}
            </h3>
            
            <!-- SKU -->
            <p class="text-sm text-gray-500 mb-2">
                SKU: {{ product.sku }}
            </p>
            
            <!-- Short Description -->
            <p v-if="product.short_description" class="text-sm text-gray-600 mb-3 line-clamp-2">
                {{ product.short_description }}
            </p>
            
            <!-- Price -->
            <div class="mb-3">
                <div class="flex items-baseline gap-2">
                    <span class="text-xl font-bold text-gray-900">
                        {{ formatPrice(currentPrice, product.base_currency) }}
                    </span>
                    <span
                        v-if="product.is_on_sale && product.base_sale_price && product.base_sale_price !== product.base_price"
                        class="text-sm text-gray-500 line-through"
                    >
                        {{ formatPrice(product.base_price, product.base_currency) }}
                    </span>
                </div>
                <div v-if="product.is_on_sale" class="text-sm text-green-600 font-medium">
                    Save {{ calculateSavings() }}%
                </div>
            </div>
            
            <!-- Stock Status -->
            <div class="mb-3">
                <div class="flex items-center justify-between text-sm">
                    <span class="text-gray-600">Stock:</span>
                    <span :class="getStockStatusClass()">
                        {{ getStockStatusText() }}
                    </span>
                </div>
                <div v-if="product.manage_stock && product.stock_quantity > 0" class="mt-1">
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div
                            :class="getStockBarClass()"
                            :style="{ width: getStockPercentage() + '%' }"
                            class="h-2 rounded-full transition-all duration-300"
                        ></div>
                    </div>
                </div>
            </div>
            
            <!-- Categories -->
            <div v-if="product.categories && product.categories.length > 0" class="mb-3">
                <div class="flex flex-wrap gap-1">
                    <span
                        v-for="category in product.categories.slice(0, 2)"
                        :key="category.id"
                        class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-gray-100 text-gray-800"
                    >
                        {{ category.name }}
                    </span>
                    <span
                        v-if="product.categories.length > 2"
                        class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-gray-100 text-gray-800"
                    >
                        +{{ product.categories.length - 2 }}
                    </span>
                </div>
            </div>
            
            <!-- Status -->
            <div class="flex items-center justify-between">
                <span :class="getStatusClass()" class="px-2 py-1 rounded-full text-xs font-medium">
                    {{ product.status }}
                </span>
                
                <!-- Action Buttons -->
                <div class="flex items-center gap-2">
                    <button
                        @click="$emit('edit', product)"
                        class="text-blue-600 hover:text-blue-800 text-sm font-medium"
                    >
                        Edit
                    </button>
                    <button
                        @click="$emit('delete', product)"
                        class="text-red-600 hover:text-red-800 text-sm font-medium"
                    >
                        Delete
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
    product: {
        type: Object,
        required: true
    }
})

const emit = defineEmits(['edit', 'duplicate', 'delete'])

const imageError = ref(false)

// Computed properties
const primaryImage = computed(() => {
    if (imageError.value) return null
    return props.product.images?.find(img => img.is_primary) || props.product.images?.[0]
})

const currentPrice = computed(() => {
    if (props.product.is_on_sale && props.product.base_sale_price) {
        return props.product.base_sale_price
    }
    return props.product.base_price
})

const isLowStock = computed(() => {
    if (!props.product.manage_stock) return false
    return props.product.stock_quantity <= props.product.low_stock_threshold && props.product.stock_quantity > 0
})

// Methods
const formatPrice = (price, currency) => {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: currency,
    }).format(price)
}

const calculateSavings = () => {
    if (!props.product.is_on_sale || !props.product.base_sale_price) return 0
    const savings = ((props.product.base_price - props.product.base_sale_price) / props.product.base_price) * 100
    return Math.round(savings)
}

const getStockStatusText = () => {
    if (!props.product.manage_stock) return 'Unlimited'
    
    if (props.product.stock_quantity === 0) return 'Out of Stock'
    if (props.product.stock_quantity <= props.product.low_stock_threshold) return `Low (${props.product.stock_quantity})`
    
    return `In Stock (${props.product.stock_quantity})`
}

const getStockStatusClass = () => {
    if (!props.product.manage_stock) return 'text-gray-600'
    
    if (props.product.stock_quantity === 0) return 'text-red-600 font-medium'
    if (props.product.stock_quantity <= props.product.low_stock_threshold) return 'text-yellow-600 font-medium'
    
    return 'text-green-600 font-medium'
}

const getStockPercentage = () => {
    if (!props.product.manage_stock || !props.product.low_stock_threshold) return 100
    
    // Calculate percentage based on low stock threshold
    const maxStock = props.product.low_stock_threshold * 4 // Show full bar at 4x low stock threshold
    return Math.min((props.product.stock_quantity / maxStock) * 100, 100)
}

const getStockBarClass = () => {
    if (!props.product.manage_stock) return 'bg-gray-400'
    
    if (props.product.stock_quantity === 0) return 'bg-red-500'
    if (props.product.stock_quantity <= props.product.low_stock_threshold) return 'bg-yellow-500'
    
    return 'bg-green-500'
}

const getStatusClass = () => {
    switch (props.product.status) {
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
</script>

<style scoped>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
