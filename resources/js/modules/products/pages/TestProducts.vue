<template>
    <div class="test-products">
        <div class="bg-white rounded-lg shadow p-6">
            <h1 class="text-2xl font-bold text-gray-900 mb-4">Products List - Working!</h1>
            <p class="text-gray-600 mb-4">This is a test page for products functionality.</p>
            
            <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                <h3 class="text-lg font-semibold text-green-800 mb-2">✅ Success!</h3>
                <p class="text-green-700">The products route is working correctly with Inertia.</p>
            </div>
            
            <div class="mt-6">
                <Link href="/dashboard" class="btn btn-outline">
                    ← Back to Dashboard
                </Link>
                <Link href="/test-products-create" class="btn btn-primary ml-3">
                    Add New Product →
                </Link>
            </div>
        </div>
    </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'
</script>

<style scoped>
.btn {
    @apply inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2;
}

.btn-primary {
    @apply text-white bg-blue-600 hover:bg-blue-700 focus:ring-blue-500;
}

.btn-outline {
    @apply text-gray-700 bg-white border-gray-300 hover:bg-gray-50 focus:ring-blue-500;
}
</style>
