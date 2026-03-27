<script setup>
import { ref, computed } from 'vue';
import { useI18n } from 'vue-i18n';
import { Link } from '@inertiajs/vue3';
import EmptyState from '@/components/ui/EmptyState.vue';
import SkeletonLoader from '@/components/ui/SkeletonLoader.vue';

const { t } = useI18n();

const props = defineProps({
    products: {
        type: Array,
        default: () => []
    },
    loading: {
        type: Boolean,
        default: false
    }
});

// Generate sparkline path for mini chart
const generateSparkline = (data) => {
    if (!data || data.length < 2) return '';
    const max = Math.max(...data);
    const min = Math.min(...data);
    const range = max - min || 1;
    const width = 60;
    const height = 20;

    return data.map((value, index) => {
        const x = (index / (data.length - 1)) * width;
        const y = height - ((value - min) / range) * height;
        return `${index === 0 ? 'M' : 'L'} ${x} ${y}`;
    }).join(' ');
};

const topProducts = computed(() => {
    return props.products.slice(0, 5).map(product => ({
        ...product,
        // Add mock sparkline data for demo - replace with real data
        salesTrend: product.salesTrend || [120, 145, 134, 189, 234, 198, 210].slice(0, 7)
    }));
});

const getStockStatus = (stock) => {
    if (stock <= 10) return {
        color: 'text-red-700 dark:text-red-300',
        bg: 'bg-red-100 dark:bg-red-900/50',
        border: 'border-red-200 dark:border-red-800',
        text: t('dashboard.critical_stock'),
        indicator: 'bg-red-500 animate-pulse'
    };
    if (stock <= 30) return {
        color: 'text-yellow-700 dark:text-yellow-300',
        bg: 'bg-yellow-100 dark:bg-yellow-900/50',
        border: 'border-yellow-200 dark:border-yellow-800',
        text: t('dashboard.low_stock'),
        indicator: 'bg-yellow-500'
    };
    return {
        color: 'text-green-700 dark:text-green-300',
        bg: 'bg-green-100 dark:bg-green-900/50',
        border: 'border-green-200 dark:border-green-800',
        text: t('dashboard.in_stock'),
        indicator: 'bg-green-500'
    };
};

const getGrowthColor = (growth) => {
    return growth > 0 ? 'text-emerald-600 dark:text-emerald-400' : growth < 0 ? 'text-red-600 dark:text-red-400' : 'text-gray-600 dark:text-gray-400';
};

const skeletonArray = Array(5).fill(null);
</script>

<template>
    <div class="space-y-6 mt-8">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-2xl font-bold text-gray-900 dark:text-white">{{ t('dashboard.top_products') }}</h3>
                <p class="text-gray-600 dark:text-gray-300 mt-1">{{ t('dashboard.best_performing_items') }}</p>
            </div>
            <Link href="/products" class="text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300 font-medium flex items-center space-x-1">
                <span>{{ t('dashboard.view_all') }}</span>
                <span>→</span>
            </Link>
        </div>

        <!-- Loading State -->
        <SkeletonLoader v-if="loading" type="card" :rows="3" />

        <!-- Empty State -->
        <EmptyState v-else-if="topProducts.length === 0"
                    variant="card"
                    icon="📦"
                    :title="t('quick_actions.no_products_yet')"
                    :description="t('quick_actions.start_adding_products')"
                    actionLink="/products?modal=create"
                    :actionText="t('quick_actions.add_first_product')"
                    actionIcon="➕" />

        <!-- Products List -->
        <div v-else class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700 p-6">
            <div class="space-y-4">
                <div v-for="(product, index) in topProducts" :key="product.id"
                     class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700 rounded-xl hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors group">
                    <!-- Rank and Product Info -->
                    <div class="flex items-center space-x-4 flex-1">
                        <!-- Rank -->
                        <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-emerald-600 rounded-lg flex items-center justify-center text-white font-bold text-sm flex-shrink-0">
                            {{ index + 1 }}
                        </div>

                        <!-- Product Image -->
                        <div class="w-12 h-12 bg-gray-200 dark:bg-gray-600 rounded-lg flex items-center justify-center text-2xl flex-shrink-0">
                            {{ product.image }}
                        </div>

                        <!-- Product Details -->
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center space-x-2">
                                <h4 class="font-medium text-gray-900 dark:text-white truncate">{{ product.name }}</h4>
                                <!-- Stock Indicator -->
                                <span :class="`w-2 h-2 rounded-full ${getStockStatus(product.stock).indicator}`"></span>
                            </div>
                            <div class="flex items-center space-x-3 text-sm text-gray-600 dark:text-gray-400 mt-1">
                                <span>{{ product.sales }} {{ t('common.sold') }}</span>
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

                            <!-- Mini Sparkline -->
                            <div class="mt-2 h-6 w-24">
                                <svg viewBox="0 0 60 20" class="w-full h-full" preserveAspectRatio="none">
                                    <path :d="generateSparkline(product.salesTrend)"
                                          stroke="#10b981"
                                          stroke-width="1.5"
                                          fill="none"
                                          stroke-linecap="round"/>
                                    <path :d="`${generateSparkline(product.salesTrend)} L 60 20 L 0 20 Z`"
                                          fill="rgba(16, 185, 129, 0.1)"/>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- Revenue, Growth and Actions -->
                    <div class="flex items-center space-x-4">
                        <div class="text-right">
                            <p class="font-semibold text-gray-900 dark:text-white">${{ product.revenue.toLocaleString() }}</p>
                            <p class="text-xs" :class="getGrowthColor(product.growth)">
                                {{ product.growth > 0 ? '↑' : '↓' }} {{ Math.abs(product.growth) }}% {{ t('dashboard.growth') }}
                            </p>
                        </div>

                        <!-- Edit Button -->
                        <Link :href="`/products/${product.id}?modal=edit`"
                              class="opacity-0 group-hover:opacity-100 transition-opacity p-2 text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 rounded-lg hover:bg-blue-50 dark:hover:bg-blue-900/30">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
