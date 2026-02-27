<template>
    <div class="space-y-6 mt-8">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-2xl font-bold text-gray-900 dark:text-white">{{ t('dashboard.featured_stores') }}</h3>
                <p class="text-gray-600 dark:text-gray-300 mt-1">{{ t('dashboard.trending_shops') }}</p>
            </div>
            <button class="text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300 font-medium flex items-center space-x-1">
                <span>{{ t('dashboard.view_all') }}</span>
                <span>→</span>
            </button>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <div v-for="store in featuredStores" :key="store.id" 
                 class="group bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-100 dark:border-gray-700 p-6 hover:shadow-xl transition-all duration-300 hover:-translate-y-1">
                
                <div class="relative">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-16 h-16 bg-gradient-to-br from-blue-100 to-purple-100 dark:from-blue-900 dark:to-purple-900 rounded-xl flex items-center justify-center text-2xl group-hover:scale-110 transition-transform">
                            {{ store.logo }}
                        </div>
                        <div class="flex flex-col items-end space-y-1">
                            <span v-if="store.discount" 
                                  class="px-2 py-1 bg-red-500 text-white text-xs font-bold rounded-full">
                                -{{ store.discount }}%
                            </span>
                            <span v-if="store.isNew" 
                                  class="px-2 py-1 bg-green-500 text-white text-xs font-bold rounded-full">
                                {{ t('dashboard.new') }}
                            </span>
                        </div>
                    </div>
                    
                    <h4 class="font-semibold text-gray-900 dark:text-white mb-1 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">
                        {{ store.name }}
                    </h4>
                    
                    <p class="text-sm text-gray-600 dark:text-gray-300 mb-3">{{ store.category }}</p>
                    
                    <!-- Store Stats -->
                    <div class="space-y-2 mb-4">
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-500 dark:text-gray-400">{{ t('dashboard.followers') }}</span>
                            <span class="font-medium text-gray-900 dark:text-white">{{ store.followers.toLocaleString() }}</span>
                        </div>
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-500 dark:text-gray-400">{{ t('dashboard.response_time') }}</span>
                            <span class="font-medium text-gray-900 dark:text-white">{{ store.responseTime }}</span>
                        </div>
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-500 dark:text-gray-400">{{ t('dashboard.sustainability') }}</span>
                            <div class="flex items-center space-x-1">
                                <div class="flex">
                                    <span v-for="i in 5" :key="i" 
                                          :class="i <= store.sustainability ? 'text-green-500' : 'text-gray-300 dark:text-gray-600'">
                                        🌱
                                    </span>
                                </div>
                                <span class="text-xs text-gray-500 dark:text-gray-400">({{ store.sustainability }}/5)</span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Rating -->
                    <div class="flex items-center space-x-2 mb-4">
                        <div class="flex items-center">
                            <span class="text-yellow-500">★</span>
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300 ml-1">{{ store.rating }}</span>
                        </div>
                        <span class="text-xs text-gray-500 dark:text-gray-400">({{ store.reviewCount }} {{ t('dashboard.reviews') }})</span>
                    </div>
                    
                    <button class="w-full bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white py-2 rounded-lg font-medium transition-all duration-200 transform hover:scale-105">
                        {{ t('dashboard.visit_store') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue';
import { useI18n } from 'vue-i18n';

const { t } = useI18n();

const featuredStores = ref([
    {
        id: 1,
        name: 'TechZone',
        logo: '💻',
        category: 'Electronics & Gadgets',
        discount: 20,
        isNew: false,
        followers: 45678,
        responseTime: '< 1h',
        sustainability: 4,
        rating: 4.7,
        reviewCount: 2341
    },
    {
        id: 2,
        name: 'EcoLiving',
        logo: '🌱',
        category: 'Sustainable Products',
        discount: null,
        isNew: true,
        followers: 12890,
        responseTime: '< 2h',
        sustainability: 5,
        rating: 4.9,
        reviewCount: 567
    },
    {
        id: 3,
        name: 'FashionHub',
        logo: '👗',
        category: 'Clothing & Accessories',
        discount: 30,
        isNew: false,
        followers: 89234,
        responseTime: '< 30min',
        sustainability: 3,
        rating: 4.5,
        reviewCount: 3421
    },
    {
        id: 4,
        name: 'HomeEssentials',
        logo: '🏠',
        category: 'Home & Garden',
        discount: 15,
        isNew: false,
        followers: 23456,
        responseTime: '< 1h',
        sustainability: 4,
        rating: 4.6,
        reviewCount: 1892
    }
]);
</script>
