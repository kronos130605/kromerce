<script setup>
import { ref } from 'vue';
import { useI18n } from 'vue-i18n';

const { t } = useI18n();

const featuredProducts = ref([
    {
        id: 1,
        name: 'Wireless Headphones',
        description: 'Premium noise-canceling headphones with exceptional sound quality',
        price: 89.99,
        originalPrice: 119.99,
        discount: 25,
        rating: 4.8,
        reviews: 234,
        icon: '🎧'
    },
    {
        id: 2,
        name: 'Smart Watch Pro',
        description: 'Advanced fitness tracking and health monitoring',
        price: 349.99,
        originalPrice: 499.99,
        discount: 30,
        rating: 4.9,
        reviews: 189,
        icon: '⌚'
    },
    {
        id: 3,
        name: 'Designer Sunglasses',
        description: 'Stylish UV protection with premium materials',
        price: 189.99,
        originalPrice: 237.49,
        discount: 20,
        rating: 4.7,
        reviews: 156,
        icon: '👕'
    },
    {
        id: 4,
        name: 'Laptop Stand',
        description: 'Ergonomic aluminum design for better posture',
        price: 449.99,
        originalPrice: 529.99,
        discount: 15,
        rating: 4.6,
        reviews: 89,
        icon: '💻'
    }
]);

const addToCart = (product) => {
    console.log('Adding to cart:', product.name);
    // Aquí iría la lógica para añadir al carrito
};
</script>

<template>
    <div class="space-y-8">
        <div class="flex items-center justify-between mb-8">
            <div>
                <h2 class="text-3xl font-bold text-gray-900 dark:text-white">{{ t('dashboard.featured_products') }}</h2>
                <p class="text-gray-600 dark:text-gray-300">{{ t('dashboard.handpicked_items') }}</p>
            </div>
            <button class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white font-medium">
                {{ t('dashboard.view_all_products') }} →
            </button>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
            <div 
                v-for="product in featuredProducts" 
                :key="product.id"
                class="group cursor-pointer"
            >
                <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden hover:shadow-lg transition-all duration-300 hover:border-gray-300 dark:hover:border-gray-600">
                    <div class="aspect-square bg-gray-100 dark:bg-gray-700 flex items-center justify-center p-8">
                        <span class="text-4xl">{{ product.icon }}</span>
                    </div>
                    <div class="p-6">
                        <div class="flex items-start justify-between mb-4">
                            <div>
                                <span class="inline-flex items-center px-2 py-1 bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200 text-xs font-medium rounded-full">
                                    -{{ product.discount }}%
                                </span>
                            </div>
                            <div class="text-right">
                                <span class="text-2xl font-bold text-gray-900 dark:text-white">${{ product.price }}</span>
                                <span class="text-sm text-gray-500 dark:text-gray-400 line-through">${{ product.originalPrice }}</span>
                            </div>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">{{ product.name }}</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-300 mb-4">{{ product.description }}</p>
                        <div class="flex items-center space-x-4">
                            <div class="flex items-center">
                                <span class="text-yellow-500">⭐</span>
                                <span class="text-sm text-gray-600 dark:text-gray-300">{{ product.rating }}</span>
                                <span class="text-gray-400 dark:text-gray-500">({{ product.reviews }} {{ t('dashboard.reviews') }})</span>
                            </div>
                            <button 
                                @click="addToCart(product)"
                                class="px-4 py-2 bg-black dark:bg-white dark:text-black text-white dark:text-black text-sm font-medium rounded hover:bg-gray-800 dark:hover:bg-gray-200 transition-colors"
                            >
                                {{ t('dashboard.add_to_cart') }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
