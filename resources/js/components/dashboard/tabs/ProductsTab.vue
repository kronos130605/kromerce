<script setup>
import { Link } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    statistics: {
        type: Object,
        default: () => ({})
    },
    recentProducts: {
        type: Array,
        default: () => []
    },
    lowStockProducts: {
        type: Array,
        default: () => []
    }
});

const statsCards = computed(() => [
    {
        title: 'Total Products',
        value: props.statistics.total_products || 0,
        icon: 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4',
        color: 'blue'
    },
    {
        title: 'Active Products',
        value: props.statistics.active_products || 0,
        icon: 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
        color: 'green'
    },
    {
        title: 'Low Stock',
        value: props.statistics.low_stock || 0,
        icon: 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z',
        color: 'yellow'
    },
    {
        title: 'Out of Stock',
        value: props.statistics.out_of_stock || 0,
        icon: 'M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
        color: 'red'
    }
]);
</script>

<template>
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex justify-between items-center">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                Product Overview
            </h3>
            <div class="flex space-x-3">
                <Link
                    href="/products"
                    class="inline-flex items-center px-4 py-2 bg-gray-900 dark:bg-gray-700 text-white rounded-xl hover:bg-gray-800 dark:hover:bg-gray-600 transition-all duration-200 shadow-md hover:shadow-lg"
                >
                    Manage Products
                </Link>
                <Link
                    href="/products/create"
                    class="inline-flex items-center px-4 py-2 bg-blue-600 dark:bg-blue-500 text-white rounded-xl hover:bg-blue-700 dark:hover:bg-blue-600 transition-all duration-200 shadow-md hover:shadow-lg"
                >
                    Add New Product
                </Link>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <div
                v-for="stat in statsCards"
                :key="stat.title"
                class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700"
            >
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div
                            :class="`p-3 rounded-lg bg-${stat.color}-100 dark:bg-${stat.color}-900`"
                        >
                            <svg
                                class="w-6 h-6 text-gray-600 dark:text-gray-300"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    :d="stat.icon"
                                />
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">
                            {{ stat.title }}
                        </p>
                        <p class="text-2xl font-semibold text-gray-900 dark:text-white">
                            {{ stat.value }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Products & Low Stock Alert -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Recent Products -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                    <h4 class="text-lg font-medium text-gray-900 dark:text-white">
                        Recent Products
                    </h4>
                </div>
                <div class="p-6">
                    <div v-if="recentProducts.length === 0" class="text-center py-8">
                        <p class="text-gray-500 dark:text-gray-400">
                            No products found
                        </p>
                    </div>
                    <div v-else class="space-y-4">
                        <div
                            v-for="product in recentProducts.slice(0, 5)"
                            :key="product.id"
                            class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg"
                        >
                            <div class="flex-1">
                                <p class="font-medium text-gray-900 dark:text-white">
                                    {{ product.name }}
                                </p>
                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    SKU: {{ product.sku }}
                                </p>
                            </div>
                            <div class="text-right">
                                <p class="font-medium text-gray-900 dark:text-white">
                                    ${{ product.base_price }}
                                </p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                    {{ product.base_currency }}
                                </p>
                            </div>
                        </div>
                    </div>
                    <div v-if="recentProducts.length > 5" class="mt-4 text-center">
                        <Link
                            href="/products"
                            class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 text-sm font-medium"
                        >
                            View all products →
                        </Link>
                    </div>
                </div>
            </div>

            <!-- Low Stock Alert -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                    <h4 class="text-lg font-medium text-gray-900 dark:text-white">
                        Low Stock Alert
                    </h4>
                </div>
                <div class="p-6">
                    <div v-if="lowStockProducts.length === 0" class="text-center py-8">
                        <p class="text-green-600 dark:text-green-400">
                            All products have sufficient stock
                        </p>
                    </div>
                    <div v-else class="space-y-4">
                        <div
                            v-for="product in lowStockProducts.slice(0, 5)"
                            :key="product.id"
                            class="flex items-center justify-between p-3 bg-yellow-50 dark:bg-yellow-900/20 rounded-lg border border-yellow-200 dark:border-yellow-800"
                        >
                            <div class="flex-1">
                                <p class="font-medium text-gray-900 dark:text-white">
                                    {{ product.name }}
                                </p>
                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    SKU: {{ product.sku }}
                                </p>
                            </div>
                            <div class="text-right">
                                <p class="font-medium text-yellow-600 dark:text-yellow-400">
                                    {{ product.stock_quantity }} left
                                </p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                    Threshold: {{ product.low_stock_threshold }}
                                </p>
                            </div>
                        </div>
                    </div>
                    <div v-if="lowStockProducts.length > 5" class="mt-4 text-center">
                        <Link
                            href="/products?filter=low_stock"
                            class="text-yellow-600 dark:text-yellow-400 hover:text-yellow-800 dark:hover:text-yellow-300 text-sm font-medium"
                        >
                            View all low stock products →
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
