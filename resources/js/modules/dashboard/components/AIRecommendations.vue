<template>
    <div class="space-y-6 mt-8">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-2xl font-bold text-gray-900 dark:text-white flex items-center space-x-2">
                    <span>🤖</span>
                    <span>{{ t('dashboard.ai_picks_for_you') }}</span>
                </h3>
                <p class="text-gray-600 dark:text-gray-300 mt-1">{{ t('dashboard.personalized_recommendations') }}</p>
            </div>
            <button class="text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300 font-medium flex items-center space-x-1">
                <span>{{ t('dashboard.refresh') }}</span>
                <span>↻</span>
            </button>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <div v-for="product in aiRecommendations" :key="product.id" 
                 class="group bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-100 dark:border-gray-700 p-6 hover:shadow-xl transition-all duration-300 hover:-translate-y-1">
                
                <!-- AI Score Badge -->
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center space-x-2">
                        <div class="bg-gradient-to-r from-green-500 to-emerald-500 text-white px-3 py-1 rounded-full text-xs font-bold flex items-center space-x-1">
                            <span>AI</span>
                            <span>{{ product.aiScore }}%</span>
                        </div>
                        <div v-if="product.isEcoFriendly" class="bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200 px-2 py-1 rounded-full text-xs font-semibold">
                            🌱 Eco
                        </div>
                    </div>
                    <div class="flex items-center space-x-1">
                        <button @click="toggleWishlist(product)" 
                                class="p-2 rounded-full hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                            <span :class="product.inWishlist ? 'text-red-500' : 'text-gray-400'">♥</span>
                        </button>
                    </div>
                </div>
                
                <!-- Product Image -->
                <div class="relative mb-4">
                    <div class="w-full h-48 bg-gradient-to-br from-gray-100 to-gray-200 dark:from-gray-700 dark:to-gray-800 rounded-xl flex items-center justify-center text-4xl group-hover:scale-105 transition-transform">
                        {{ product.icon }}
                    </div>
                    <div v-if="product.discount" 
                         class="absolute top-2 left-2 bg-red-500 text-white px-2 py-1 rounded-full text-xs font-bold">
                        -{{ product.discount }}%
                    </div>
                </div>
                
                <!-- Product Info -->
                <div class="space-y-2">
                    <div class="text-xs text-gray-500 dark:text-gray-400">{{ product.store }}</div>
                    <h4 class="font-semibold text-gray-900 dark:text-white group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">
                        {{ product.name }}
                    </h4>
                    
                    <!-- AI Reason -->
                    <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-2">
                        <div class="text-xs text-blue-700 dark:text-blue-300 font-medium mb-1">{{ t('dashboard.why_recommended') }}:</div>
                        <div class="text-xs text-blue-600 dark:text-blue-400">{{ product.aiReason }}</div>
                    </div>
                    
                    <!-- Rating and Reviews -->
                    <div class="flex items-center space-x-2">
                        <div class="flex items-center">
                            <span class="text-yellow-500">★</span>
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300 ml-1">{{ product.rating }}</span>
                        </div>
                        <span class="text-xs text-gray-500 dark:text-gray-400">({{ product.reviews }} {{ t('dashboard.reviews') }})</span>
                    </div>
                    
                    <!-- Price and Actions -->
                    <div class="flex items-center justify-between pt-2">
                        <div>
                            <div class="flex items-center space-x-2">
                                <span class="text-2xl font-bold text-gray-900 dark:text-white">${{ product.price }}</span>
                                <span v-if="product.originalPrice" class="text-sm text-gray-500 dark:text-gray-400 line-through">
                                    ${{ product.originalPrice }}
                                </span>
                            </div>
                        </div>
                        <button @click="addToCart(product)" 
                                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors flex items-center space-x-1">
                            <span>🛒</span>
                            <span>{{ t('dashboard.add_to_cart') }}</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue';
import { useI18n } from 'vue-i18n';

const { t } = useI18n();

const aiRecommendations = ref([
    {
        id: 1,
        name: 'Wireless Noise-Canceling Headphones Pro',
        store: 'TechZone',
        price: 299.99,
        originalPrice: 399.99,
        discount: 25,
        rating: 4.8,
        reviews: 1247,
        icon: '🎧',
        aiScore: 95,
        aiReason: 'Based on your previous electronics purchases and preference for premium audio quality',
        isEcoFriendly: true,
        inWishlist: false
    },
    {
        id: 2,
        name: 'Smart Fitness Watch Ultra',
        store: 'FitGear',
        price: 189.99,
        originalPrice: 249.99,
        discount: 24,
        rating: 4.6,
        reviews: 892,
        icon: '⌚',
        aiScore: 88,
        aiReason: 'Matches your interest in health tracking and outdoor activities',
        isEcoFriendly: false,
        inWishlist: true
    },
    {
        id: 3,
        name: 'Organic Cotton Yoga Mat Premium',
        store: 'EcoLiving',
        price: 79.99,
        rating: 4.9,
        reviews: 567,
        icon: '🧘',
        aiScore: 92,
        aiReason: 'Perfect for your wellness routine and commitment to sustainable products',
        isEcoFriendly: true,
        inWishlist: false
    }
]);

const toggleWishlist = (product) => {
    product.inWishlist = !product.inWishlist;
    // Emit event or call API
};

const addToCart = (product) => {
    // Emit event or call API
    console.log('Added to cart:', product.name);
};
</script>
