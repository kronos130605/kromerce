<template>
    <BusinessLayout>
        <div class="products-index">
            <!-- Header -->
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Products</h1>
                    <p class="text-gray-600 mt-1">Manage your product catalog</p>
                </div>
                <div class="flex gap-3">
                    <button
                        @click="exportProducts"
                        class="btn btn-outline"
                    >
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Export
                    </button>
                    <Link
                        href="/test-products-create"
                        class="btn btn-primary"
                    >
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Add Product
                    </Link>
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="p-2 bg-blue-100 rounded-lg">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 6H9a2 2 0 00-2 2v8a2 2 0 002 2h11a2 2 0 002-2V8a2 2 0 00-2-2z"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm text-gray-600">Total Products</p>
                            <p class="text-2xl font-bold text-gray-900">{{ statistics.total_products }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="p-2 bg-green-100 rounded-lg">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm text-gray-600">Active</p>
                            <p class="text-2xl font-bold text-gray-900">{{ statistics.active_products }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="p-2 bg-yellow-100 rounded-lg">
                            <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm text-gray-600">Low Stock</p>
                            <p class="text-2xl font-bold text-gray-900">{{ statistics.low_stock_products }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="p-2 bg-red-100 rounded-lg">
                            <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm text-gray-600">On Sale</p>
                            <p class="text-2xl font-bold text-gray-900">{{ statistics.on_sale_products }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Search and Filters -->
            <div class="bg-white rounded-lg shadow mb-6">
                <div class="p-4 border-b">
                    <div class="flex flex-col md:flex-row gap-4">
                        <!-- Search -->
                        <div class="flex-1">
                            <div class="relative">
                                <input
                                    v-model="filters.search"
                                    @input="debouncedSearch"
                                    type="text"
                                    placeholder="Search products..."
                                    class="w-full pl-10 pr-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                >
                                <svg class="absolute left-3 top-2.5 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </div>
                        </div>

                        <!-- View Toggle -->
                        <div class="flex items-center gap-2">
                            <button
                                @click="viewMode = 'grid'"
                                :class="['px-3 py-2 rounded', viewMode === 'grid' ? 'bg-blue-100 text-blue-600' : 'text-gray-600 hover:bg-gray-100']"
                            >
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path>
                                </svg>
                            </button>
                            <button
                                @click="viewMode = 'list'"
                                :class="['px-3 py-2 rounded', viewMode === 'list' ? 'bg-blue-100 text-blue-600' : 'text-gray-600 hover:bg-gray-100']"
                            >
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                                </svg>
                            </button>
                        </div>

                        <!-- Filters Toggle -->
                        <button
                            @click="showFilters = !showFilters"
                            class="px-4 py-2 border rounded-lg hover:bg-gray-50 flex items-center gap-2"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                            </svg>
                            Filters
                            <span v-if="activeFiltersCount > 0" class="bg-blue-500 text-white text-xs px-2 py-1 rounded-full">
                                {{ activeFiltersCount }}
                            </span>
                        </button>
                    </div>
                </div>

                <!-- Filters Panel -->
                <div v-show="showFilters" class="p-4 border-t">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <!-- Category Filter -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                            <select
                                v-model="filters.category_id"
                                @change="applyFilters"
                                class="w-full border rounded-lg px-3 py-2"
                            >
                                <option value="">All Categories</option>
                                <option v-for="category in categories" :key="category.id" :value="category.id">
                                    {{ category.name }}
                                </option>
                            </select>
                        </div>

                        <!-- Status Filter -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                            <select
                                v-model="filters.status"
                                @change="applyFilters"
                                class="w-full border rounded-lg px-3 py-2"
                            >
                                <option value="">All Status</option>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                                <option value="draft">Draft</option>
                            </select>
                        </div>

                        <!-- Stock Filter -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Stock Status</label>
                            <select
                                v-model="filters.stock_status"
                                @change="applyFilters"
                                class="w-full border rounded-lg px-3 py-2"
                            >
                                <option value="">All Stock</option>
                                <option value="instock">In Stock</option>
                                <option value="outofstock">Out of Stock</option>
                                <option value="lowstock">Low Stock</option>
                            </select>
                        </div>

                        <!-- Sale Filter -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Sale Status</label>
                            <select
                                v-model="filters.is_on_sale"
                                @change="applyFilters"
                                class="w-full border rounded-lg px-3 py-2"
                            >
                                <option value="">All Products</option>
                                <option value="1">On Sale</option>
                                <option value="0">Not on Sale</option>
                            </select>
                        </div>
                    </div>

                    <!-- Price Range -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Min Price</label>
                            <input
                                v-model="filters.min_price"
                                @input="debouncedSearch"
                                type="number"
                                step="0.01"
                                placeholder="0.00"
                                class="w-full border rounded-lg px-3 py-2"
                            >
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Max Price</label>
                            <input
                                v-model="filters.max_price"
                                @input="debouncedSearch"
                                type="number"
                                step="0.01"
                                placeholder="999.99"
                                class="w-full border rounded-lg px-3 py-2"
                            >
                        </div>
                    </div>

                    <!-- Clear Filters -->
                    <div class="mt-4 flex justify-end">
                        <button
                            @click="clearFilters"
                            class="text-sm text-gray-600 hover:text-gray-900"
                        >
                            Clear all filters
                        </button>
                    </div>
                </div>
            </div>

            <!-- Products Display -->
            <div v-if="loading" class="text-center py-12">
                <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-blue-500"></div>
                <p class="mt-2 text-gray-600">Loading products...</p>
            </div>

            <div v-else-if="products.data.length === 0" class="text-center py-12">
                <div class="inline-flex items-center justify-center w-16 h-16 bg-gray-100 rounded-full mb-4">
                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 6H9a2 2 0 00-2 2v8a2 2 0 002 2h11a2 2 0 002-2V8a2 2 0 00-2-2z"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-1">No products found</h3>
                <p class="text-gray-600 mb-4">Get started by creating your first product</p>
                <Link
                    :href="route('products.create')"
                    class="btn btn-primary"
                >
                    Add Product
                </Link>
            </div>

            <div v-else>
                <!-- Grid View -->
                <div v-if="viewMode === 'grid'" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                    <ProductCard
                        v-for="product in products.data"
                        :key="product.id"
                        :product="product"
                        @edit="editProduct"
                        @duplicate="duplicateProduct"
                        @delete="deleteProduct"
                    />
                </div>

                <!-- List View -->
                <div v-else class="bg-white rounded-lg shadow overflow-hidden">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">SKU</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stock</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                        <tr v-for="product in products.data" :key="product.id" class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        <img
                                            v-if="product.primary_image"
                                            :src="product.primary_image.url"
                                            :alt="product.name"
                                            class="h-10 w-10 rounded object-cover"
                                        />
                                        <div v-else class="h-10 w-10 bg-gray-200 rounded flex items-center justify-center">
                                            <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4 4m0 0l-4-4m4 4l4 4m-2-2h-8m-2 2h8"></path>
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">{{ product.name }}</div>
                                        <div class="text-sm text-gray-500">{{ product.short_description }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ product.sku }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ formatPrice(product.base_price, product.base_currency) }}
                                <span v-if="product.is_on_sale && product.base_sale_price" class="text-green-600 ml-1">
                                        {{ formatPrice(product.base_sale_price, product.base_currency) }}
                                    </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <span :class="getStockClass(product)">
                                        {{ product.manage_stock ? product.stock_quantity : '∞' }}
                                    </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                    <span :class="getStatusClass(product.status)" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full">
                                        {{ product.status }}
                                    </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex items-center space-x-2">
                                    <button
                                        @click="editProduct(product)"
                                        class="text-blue-600 hover:text-blue-900"
                                    >
                                        Edit
                                    </button>
                                    <button
                                        @click="duplicateProduct(product)"
                                        class="text-gray-600 hover:text-gray-900"
                                    >
                                        Duplicate
                                    </button>
                                    <button
                                        @click="deleteProduct(product)"
                                        class="text-red-600 hover:text-red-900"
                                    >
                                        Delete
                                    </button>
                                </div>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="mt-6 flex justify-between items-center">
                    <div class="text-sm text-gray-700">
                        Showing {{ products.from }} to {{ products.to }} of {{ products.total }} results
                    </div>
                    <div v-if="products.links">
                        <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px">
                            <template v-for="(link, index) in products.links" :key="index">
                                <button
                                    v-if="link.url"
                                    @click="goToPage(link.url)"
                                    :disabled="!link.url"
                                    :class="[
                                        'relative inline-flex items-center px-4 py-2 border text-sm font-medium',
                                        link.active
                                            ? 'z-10 bg-blue-50 border-blue-500 text-blue-600'
                                            : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50'
                                    ]"
                                    v-html="link.label"
                                />
                                <span
                                    v-else
                                    class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-gray-50 text-sm font-medium text-gray-700"
                                    v-html="link.label"
                                />
                            </template>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </BusinessLayout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import { debounce } from 'lodash'
import BusinessLayout from '@/Layouts/BusinessLayout.vue';
import ProductCard from '@/modules/products/components/ProductCard.vue';

const props = defineProps({
    products: Object,
    categories: Array,
    filters: Object,
    statistics: Object,
})

const loading = ref(false)
const showFilters = ref(false)
const viewMode = ref('grid')
const filters = ref({ ...props.filters })

// Computed properties
const activeFiltersCount = computed(() => {
    let count = 0
    if (filters.value.category_id) count++
    if (filters.value.status) count++
    if (filters.value.stock_status) count++
    if (filters.value.is_on_sale) count++
    if (filters.value.min_price) count++
    if (filters.value.max_price) count++
    return count
})

// Methods
const debouncedSearch = debounce(() => {
    applyFilters()
}, 300)

const applyFilters = () => {
    loading.value = true

    router.get(route('products.index'), filters.value, {
        preserveState: true,
        preserveScroll: true,
        onFinish: () => {
            loading.value = false
        }
    })
}

const clearFilters = () => {
    filters.value = {
        search: '',
        category_id: '',
        status: '',
        stock_status: '',
        is_on_sale: '',
        min_price: '',
        max_price: '',
    }
    applyFilters()
}

const goToPage = (url) => {
    loading.value = true
    router.get(url, filters.value, {
        preserveState: true,
        preserveScroll: true,
        onFinish: () => {
            loading.value = false
        }
    })
}

const editProduct = (product) => {
    router.get(route('products.edit', product))
}

const duplicateProduct = (product) => {
    router.post(route('products.duplicate', product), {}, {
        onSuccess: () => {
            // Show success message
        }
    })
}

const deleteProduct = (product) => {
    if (confirm(`Are you sure you want to delete "${product.name}"?`)) {
        router.delete(route('products.destroy', product), {
            onSuccess: () => {
                // Show success message
            }
        })
    }
}

const exportProducts = () => {
    // Implement export functionality
    window.location.href = route('products.export')
}

// Helper functions
const formatPrice = (price, currency) => {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: currency,
    }).format(price)
}

const getStatusClass = (status) => {
    switch (status) {
        case 'active':
            return 'bg-green-100 text-green-800'
        case 'inactive':
            return 'bg-gray-100 text-gray-800'
        case 'draft':
            return 'bg-yellow-100 text-yellow-800'
        default:
            return 'bg-gray-100 text-gray-800'
    }
}

const getStockClass = (product) => {
    if (!product.manage_stock) {
        return 'text-gray-600'
    }

    if (product.stock_quantity === 0) {
        return 'text-red-600 font-medium'
    }

    if (product.stock_quantity <= product.low_stock_threshold) {
        return 'text-yellow-600 font-medium'
    }

    return 'text-green-600'
}

onMounted(() => {
    // Initialize any additional functionality
})
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
