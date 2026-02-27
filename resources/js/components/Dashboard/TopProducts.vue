<script setup>
import { ref, computed } from 'vue';
import { useI18n } from 'vue-i18n';

const { t } = useI18n();

const topProducts = ref([
    {
        id: 1,
        name: 'Wireless Noise-Canceling Headphones Pro',
        sales: 234,
        revenue: 70200,
        growth: 15.3,
        stock: 45,
        category: 'Electronics',
        rating: 4.8,
        image: '🎧'
    },
    {
        id: 2,
        name: 'Smart Watch Ultra 2024',
        sales: 189,
        revenue: 56700,
        growth: 8.7,
        stock: 23,
        category: 'Electronics',
        rating: 4.7,
        image: '⌚'
    },
    {
        id: 3,
        name: 'Premium Laptop Stand Adjustable',
        sales: 156,
        revenue: 23400,
        growth: -2.1,
        stock: 67,
        category: 'Accessories',
        rating: 4.5,
        image: '💻'
    },
    {
        id: 4,
        name: 'USB-C Hub 7-in-1 Premium',
        sales: 143,
        revenue: 21450,
        growth: 22.4,
        stock: 89,
        category: 'Accessories',
        rating: 4.6,
        image: '🔌'
    },
    {
        id: 5,
        name: 'Ergonomic Office Chair Deluxe',
        sales: 98,
        revenue: 19600,
        growth: 12.8,
        stock: 15,
        category: 'Furniture',
        rating: 4.9,
        image: '🪑'
    }
]);

const getStockStatus = (stock) => {
    if (stock <= 10) return { color: 'text-red-600', bg: 'bg-red-100', text: 'dashboard.low_stock' };
    if (stock <= 30) return { color: 'text-yellow-600', bg: 'bg-yellow-100', text: 'dashboard.medium_stock' };
    return { color: 'text-green-600', bg: 'bg-green-100', text: 'dashboard.in_stock' };
};

const getGrowthColor = (growth) => {
    return growth > 0 ? 'text-green-600' : 'text-red-600';
};
</script>

<template>
    <div class="space-y-6 mt-8">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-2xl font-bold text-gray-900 dark:text-white">{{ t('dashboard.top_products') }}</h3>
                <p class="text-gray-600 dark:text-gray-300 mt-1">{{ t('dashboard.best_performing_items') }}</p>
            </div>
            <button class="text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300 font-medium flex items-center space-x-1">
                <span>{{ t('dashboard.view_all') }}</span>
                <span>→</span>
            </button>
        </div>
        
        <!-- Products List -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700 p-6">
            <div class="space-y-4">
                <div v-for="(product, index) in topProducts" :key="product.id" 
                     class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700 rounded-xl hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors">
                    <!-- Rank and Product Info -->
                    <div class="flex items-center space-x-4">
                        <!-- Rank -->
                        <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-emerald-600 rounded-lg flex items-center justify-center text-white font-bold text-sm">
                            {{ index + 1 }}
                        </div>
                        
                        <!-- Product Image -->
                        <div class="w-12 h-12 bg-gray-200 dark:bg-gray-600 rounded-lg flex items-center justify-center text-2xl">
                            {{ product.image }}
                        </div>
                        
                        <!-- Product Details -->
                        <div>
                            <h4 class="font-medium text-gray-900 dark:text-white">{{ product.name }}</h4>
                            <div class="flex items-center space-x-3 text-sm text-gray-600 dark:text-gray-400">
                                <span>{{ product.sales }} {{ t('dashboard.sold') }}</span>
                                <span>•</span>
                                <span :class="getStockStatus(product.stock).color">
                                    {{ product.stock }} {{ t('dashboard.in_stock') }}
                                </span>
                                <span>•</span>
                                <span class="flex items-center">
                                    <span class="text-yellow-500">★</span>
                                    <span class="ml-1">{{ product.rating }}</span>
                                </span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Revenue and Growth -->
                    <div class="text-right">
                        <p class="font-semibold text-gray-900 dark:text-white">${{ product.revenue.toLocaleString() }}</p>
                        <p class="text-xs" :class="getGrowthColor(product.growth)">
                            {{ product.growth > 0 ? '↑' : '↓' }} {{ Math.abs(product.growth) }}% {{ t('dashboard.growth') }}
                        </p>
                        
                        <!-- Stock Status Badge -->
                        <div class="mt-1">
                            <span :class="`inline-flex items-center px-2 py-1 text-xs font-medium rounded-full ${getStockStatus(product.stock).bg} ${getStockStatus(product.stock).color}`">
                                {{ t(getStockStatus(product.stock).text) }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
