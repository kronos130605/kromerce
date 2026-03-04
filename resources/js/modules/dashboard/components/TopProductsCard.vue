<template>
    <div class="top-products-card">
        <h3 class="card-title">{{ title }}</h3>
        <div v-if="products.length === 0" class="empty-state">
            <div class="empty-icon">
                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6H2m7 7V10a2 2 0 0 0 2 2h5.586a1 1 0 0 0 .707.293l-6.414 6.414a1 1 0 0 0 .707-.293l-6.415-6.586a1 1 0 0 0-.707-.293l-6.586 6.586a1 1 0 0 0-.707-.293l-6.586-6.586z"></path>
                </svg>
            </div>
            <p class="empty-message">No products found</p>
        </div>

        <div v-else class="products-list">
            <div v-for="(product, index) in products" :key="product.id" class="product-item">
                <div class="product-rank">
                    <span class="rank-number">{{ index + 1 }}</span>
                    <div class="rank-indicator" :class="getRankClass(index)"></div>
                </div>
                <div class="product-info">
                    <h4 class="product-name">{{ product.name }}</h4>
                    <p class="product-metric">{{ getMetricValue(product) }}</p>
                    <p class="product-sku">SKU: {{ product.sku }}</p>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>

const props = defineProps({
    title: {
        type: String,
        required: true
    },
    products: {
        type: Array,
        required: true
    },
    metric: {
        type: String,
        default: 'revenue'
    }
})

const getMetricValue = (product) => {
    switch (props.metric) {
        case 'revenue':
            return `$${product.base_price || 0}`
        case 'views':
            return `${Math.floor(Math.random() * 1000)} views`
        case 'sales':
            return `${Math.floor(Math.random() * 100)} sales`
        default:
            return product.base_price || 0
    }
}

const getRankClass = (index) => {
    const classes = ['rank-first', 'rank-second', 'rank-third', 'rank-fourth', 'rank-fifth']
    return classes[index] || 'rank-default'
}
</script>

<style scoped>
.top-products-card {
    @apply bg-white rounded-lg shadow-md p-6;
}

.card-title {
    @apply text-lg font-semibold text-gray-900 mb-4;
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

.products-list {
    @apply space-y-3;
}

.product-item {
    @apply flex items-center space-x-4 p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors;
}

.product-rank {
    @apply flex-shrink-0 text-center;
}

.rank-number {
    @apply text-lg font-bold text-gray-700;
}

.rank-indicator {
    @apply w-2 h-2 rounded-full mx-auto mb-1;
}

.product-info {
    @apply flex-1;
}

.product-name {
    @apply font-medium text-gray-900;
}

.product-metric {
    @apply text-sm font-medium text-gray-600;
}

.product-sku {
    @apply text-xs text-gray-500;
}
</style>
