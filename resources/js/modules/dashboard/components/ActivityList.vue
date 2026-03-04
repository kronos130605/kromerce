<template>
    <div class="activity-list">
        <h3 class="activity-title">Recent Activity</h3>
        <div v-if="activities.recentPriceChanges.length === 0 && activities.newProducts.length === 0" class="empty-state">
            <div class="empty-icon">
                <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3v-4m0 4l-3-3m3 3l3 3m-3-3l-3-3m3 3l3 3m-3-3l-3-3"></path>
                </svg>
            </div>
            <p class="empty-message">No recent activity</p>
        </div>

        <div v-else class="activity-grid">
            <!-- Price Changes -->
            <div v-if="activities.recentPriceChanges.length > 0" class="activity-section">
                <h4 class="section-title">Price Changes</h4>
                <div class="activity-items">
                    <div v-for="change in activities.recentPriceChanges" :key="change.id" class="activity-item price-change">
                        <div class="activity-icon">
                            <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11v2a4 4 0 0 0 8 0m0 0v-2a4 4 0 0 0-8 0m0 0v2a4 4 0 0 0 8 0m-4-4v2m8 0v-2a4 4 0 0 0-8 0"></path>
                            </svg>
                        </div>
                        <div class="activity-content">
                            <div class="activity-header">
                                <span class="product-name">{{ change.product_name }}</span>
                                <span class="change-reason">{{ change.change_reason }}</span>
                            </div>
                            <div class="activity-details">
                                <span class="old-price">Old: {{ formatPrice(change.old_price, change.currency) }}</span>
                                <span class="new-price">New: {{ formatPrice(change.new_price, change.currency) }}</span>
                                <span class="change-date">{{ formatDate(change.created_at) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- New Products -->
            <div v-if="activities.newProducts.length > 0" class="activity-section">
                <h4 class="section-title">New Products</h4>
                <div class="activity-items">
                    <div v-for="product in activities.newProducts" :key="product.id" class="activity-item new-product">
                        <div class="activity-icon">
                            <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0V6m0 0V6m0 0V6m0 0V6m0 0V6"></path>
                            </svg>
                        </div>
                        <div class="activity-content">
                            <div class="activity-header">
                                <span class="product-name">{{ product.name }}</span>
                                <span class="action">New Product</span>
                            </div>
                            <div class="activity-details">
                                <span class="product-sku">SKU: {{ product.sku }}</span>
                                <span class="product-price">{{ formatPrice(product.base_price, product.base_currency) }}</span>
                                <span class="creation-date">{{ formatDate(product.created_at) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Stock Updates -->
            <div v-if="activities.stockUpdates.length > 0" class="activity-section">
                <h4 class="section-title">Stock Updates</h4>
                <div class="activity-items">
                    <div v-for="product in activities.stockUpdates" :key="product.id" class="activity-item stock-update">
                        <div class="activity-icon">
                            <svg class="w-5 h-5 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 6H9a2 2 0 0 12 12m11 11a9 5.5 12 7.5a4 4h3a2 2 0 0 0m6 11 11a10 10 2 0 0 0m2 13a10 10 2 0 0 0m6 11a11 11 11a1 2 2 0 0 m0 4h6a2 2 0 0 0"></path>
                            </svg>
                        </div>
                        <div class="activity-content">
                            <div class="activity-header">
                                <span class="product-name">{{ product.name }}</span>
                                <span class="action">Low Stock Alert</span>
                            </div>
                            <div class="activity-details">
                                <span class="stock-quantity">Stock: {{ product.stock_quantity }}</span>
                                <span class="threshold">Threshold: {{ product.low_stock_threshold }}</span>
                                <span class="update-date">{{ formatDate(product.updated_at) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>

const props = defineProps({
    activities: {
        type: Object,
        required: true
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
.activity-list {
    @apply space-y-6;
}

.activity-title {
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

.activity-grid {
    @apply space-y-6;
}

.activity-section {
    @apply bg-white rounded-lg shadow-sm p-4;
}

.section-title {
    @apply text-base font-medium text-gray-900 mb-3;
}

.activity-items {
    @apply space-y-3;
}

.activity-item {
    @apply flex items-start space-x-3 p-3 bg-gray-50 rounded-lg;
}

.activity-icon {
    @apply flex-shrink-0 mt-1;
}

.activity-content {
    @apply flex-1;
}

.activity-header {
    @apply flex justify-between items-center mb-1;
}

.product-name {
    @apply font-medium text-gray-900;
}

.action {
    @apply text-xs px-2 py-1 rounded-full font-medium;
}

.price-change .action {
    @apply bg-green-100 text-green-800;
}

.new-product .action {
    @apply bg-blue-100 text-blue-800;
}

.stock-update .action {
    @apply bg-yellow-100 text-yellow-800;
}

.activity-details {
    @apply flex flex-wrap gap-2 text-xs text-gray-600;
}

.old-price {
    @apply line-through text-gray-500;
}

.new-price {
    @apply font-medium text-green-600;
}

.product-sku {
    @apply text-gray-500;
}

.product-price {
    @apply font-medium text-gray-900;
}

.stock-quantity {
    @apply text-red-600;
}

.threshold {
    @apply text-gray-500;
}

.update-date {
    @apply text-gray-500;
}

.creation-date {
    @apply text-gray-500;
}
</style>
