<script setup>
import { useDarkMode } from '@/composables/useDarkMode';
import { Link } from '@inertiajs/vue3';

const { isDark } = useDarkMode();
</script>

<template>
    <div class="space-y-6">
        <div class="flex justify-between items-center">
            <h3 class="text-lg font-medium" :class="isDark ? 'text-white' : 'text-gray-900'">
                Product Management
            </h3>
            <div class="flex space-x-3">
                <Link
                    href="/test-products"
                    class="inline-flex items-center px-4 py-2 bg-gray-900 dark:bg-gray-700 text-white rounded-xl hover:bg-gray-800 dark:hover:bg-gray-600 transition-all duration-200 shadow-md hover:shadow-lg"
                >
                    Manage Products
                </Link>
                <Link
                    href="/test-products-create"
                    class="inline-flex items-center px-4 py-2 bg-blue-600 dark:bg-blue-500 text-white rounded-xl hover:bg-blue-700 dark:hover:bg-blue-600 transition-all duration-200 shadow-md hover:shadow-lg"
                >
                    Add New Product
                </Link>
            </div>
        </div>
        
        <div class="rounded-2xl p-8 text-center border" :class="isDark ? 'bg-gray-800 border-gray-700' : 'bg-gray-50 border-gray-200'">
            <p :class="isDark ? 'text-gray-300' : 'text-gray-600'">
                Click "Manage Products" to view your product catalog
            </p>
        </div>
    </div>
</template>
