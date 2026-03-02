<template>
    <div class="product-list">
        <div v-if="products.length === 0" class="empty-state">
            <div class="empty-icon">
                <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 6H9a2 2 0 0 12 12m11 11a9 5.5 12 7.5a4 4h3a2 2 0 0 0m6 11 11a10 10 2 0 0 0m2 13a10 10 2 0 0 0m6 11a11 11 11a1 2 2 0 0 m0 4h6a2 2 0 0 0"></path>
                </svg>
            </div>
            <p class="empty-message">No products found</p>
        </div>
        
        <div v-else class="product-grid">
            <div v-for="product in products" :key="product.id" class="product-item">
                <div class="product-image">
                    <img v-if="product.image" :src="product.image" :alt="product.name" />
                    <div v-else class="image-placeholder">
                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4 4m0 0l-4-4m4 4l4 4m-2-2h-8m-2 2h8"></path>
                        </svg>
                    </div>
                </div>
                <div class="product-info">
                    <h4 class="product-name">{{ product.name }}</h4>
                    <p class="product-sku">SKU: {{ product.sku }}</p>
                    <p class="product-price">{{ formatPrice(product.base_price, product.base_currency) }}</p>
                    <div class="product-meta">
                        <span class="status" :class="product.status">{{ product.status }}</span>
                        <span class="created">{{ formatDate(product.created_at) }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
    products: {
        type: Array,
        required: true
    },
    compact: {
        type: Boolean,
        default: false
    }
})

const formatPrice = (price, currency) => {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: currency,
    }).format(price)
}

const formatDate = (date) => {
    return new Date(date).toLocaleDateString()
}
</script>

<style scoped>
.product-list {
    @apply space-y-4;
}

.empty-state {
    @apply text-center py-8;
}

.empty-icon {
    @apply mx-auto mb-4;
}

.empty-message {
    @apply text-gray-500;
}

.product-grid {
    @apply grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4;
}

.product-item {
    @apply bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow;
}

.product-image {
    @apply h-48 bg-gray-200 relative;
}

.product-image img {
    @apply w-full h-full object-cover;
}

.image-placeholder {
    @apply w-full h-full flex items-center justify-center bg-gray-100;
}

.product-info {
    @apply p-4;
}

.product-name {
    @apply font-semibold text-gray-900 mb-1;
}

.product-sku {
    @apply text-sm text-gray-500 mb-2;
}

.product-price {
    @apply text-lg font-bold text-gray-900 mb-2;
}

.product-meta {
    @apply flex justify-between items-center text-xs;
}

.status {
    @apply px-2 py-1 rounded-full font-medium;
}

.status.active {
    @apply bg-green-100 text-green-800;
}

.status.inactive {
    @apply bg-gray-100 text-gray-800;
}

.status.draft {
    @apply bg-yellow-100 text-yellow-800;
}

.created {
    @apply text-gray-500;
}
</style>
