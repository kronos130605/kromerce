<script setup>
import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';

// Import dashboard components
import CustomerHero from '@/components/Dashboard/CustomerHero.vue';
import FeaturedProducts from '@/components/Dashboard/FeaturedProducts.vue';
import ProductCategories from '@/components/Dashboard/ProductCategories.vue';
import FlashSaleBanner from '@/components/Dashboard/FlashSaleBanner.vue';
import QuickActions from '@/components/Dashboard/QuickActions.vue';

const page = usePage();
const user = computed(() => page.props.auth.user);

// Check user role
const isCustomer = computed(() => {
    const roles = user.value?.roles || [];
    return roles.some(role => role.name === 'customer') || user.value?.role === 'customer';
});

const isBusinessOwner = computed(() => {
    const roles = user.value?.roles || [];
    return roles.some(role => role.name === 'business_owner') || user.value?.role === 'business_owner';
});

const isSuperAdmin = computed(() => {
    const roles = user.value?.roles || [];
    return roles.some(role => role.name === 'super_admin') || user.value?.role === 'super_admin';
});
</script>

<template>
    <Head title="Dashboard" />
    
    <AuthenticatedLayout>
        <!-- Customer Dashboard Content -->
        <div v-if="isCustomer">
            <CustomerHero />
            
            <div class="px-4 sm:px-6 lg:px-8 py-12">
                <div class="max-w-7xl mx-auto space-y-16">
                    <FeaturedProducts />
                    <ProductCategories />
                    <FlashSaleBanner />
                    <QuickActions />
                </div>
            </div>
        </div>

        <!-- Business Dashboard Content -->
        <div v-else-if="isBusinessOwner">
            <div class="relative">
                <div class="absolute inset-0 bg-gradient-to-r from-emerald-50 via-blue-50 to-purple-50 -z-10 rounded-2xl"></div>
                <div class="relative px-8 py-6">
                    <div class="flex justify-between items-start">
                        <div class="space-y-2">
                            <div class="flex items-center space-x-3">
                                <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
                                <h1 class="text-3xl font-bold bg-gradient-to-r from-gray-900 to-gray-700 bg-clip-text text-transparent">
                                    Business Dashboard
                                </h1>
                            </div>
                            <p class="text-gray-600 text-lg">Manage your business and track performance</p>
                            <div class="flex items-center space-x-4 text-sm">
                                <span class="text-gray-500">Last 30 days performance</span>
                                <span class="px-2 py-1 bg-green-100 text-green-700 rounded-full font-medium">
                                    ↑ 23.5% growth
                                </span>
                            </div>
                        </div>
                        
                        <div v-if="currentTenant" class="text-right space-y-2">
                            <div class="inline-flex items-center px-4 py-2 bg-white rounded-xl shadow-sm border border-gray-200">
                                <div class="w-2 h-2 bg-blue-500 rounded-full mr-2"></div>
                                <span class="font-medium text-gray-900">{{ currentTenant.name }}</span>
                            </div>
                            <p class="text-sm text-gray-500 font-mono">{{ currentTenant.slug }}.kromerce.test</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="px-4 sm:px-6 lg:px-8 py-6">
                <div class="mx-auto max-w-7xl space-y-8">
                    
                    <!-- Enhanced Stats Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                        <!-- Revenue Card -->
                        <div class="group relative bg-white rounded-2xl shadow-lg border border-gray-100 p-6 hover:shadow-xl transition-all duration-300 hover:-translate-y-1">
                            <div class="absolute inset-0 bg-gradient-to-br from-emerald-50 to-teal-50 rounded-2xl opacity-0 group-hover:opacity-100 transition-opacity"></div>
                            <div class="relative">
                                <div class="flex items-center justify-between mb-4">
                                    <div class="w-12 h-12 bg-gradient-to-br from-emerald-500 to-teal-600 rounded-xl flex items-center justify-center shadow-lg">
                                        <span class="text-2xl">💰</span>
                                    </div>
                                    <span class="text-xs font-medium text-emerald-600 bg-emerald-50 px-2 py-1 rounded-full">
                                        +23.5%
                                    </span>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-600 mb-1">Total Revenue</p>
                                    <p class="text-3xl font-bold text-gray-900">$125,430</p>
                                    <p class="text-xs text-gray-500 mt-2">↑ $23,430 from last month</p>
                                </div>
                            </div>
                        </div>

                        <!-- Orders Card -->
                        <div class="group relative bg-white rounded-2xl shadow-lg border border-gray-100 p-6 hover:shadow-xl transition-all duration-300 hover:-translate-y-1">
                            <div class="absolute inset-0 bg-gradient-to-br from-blue-50 to-indigo-50 rounded-2xl opacity-0 group-hover:opacity-100 transition-opacity"></div>
                            <div class="relative">
                                <div class="flex items-center justify-between mb-4">
                                    <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center shadow-lg">
                                        <span class="text-2xl">📦</span>
                                    </div>
                                    <span class="text-xs font-medium text-blue-600 bg-blue-50 px-2 py-1 rounded-full">
                                        +18.2%
                                    </span>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-600 mb-1">Total Orders</p>
                                    <p class="text-3xl font-bold text-gray-900">3,421</p>
                                    <p class="text-xs text-gray-500 mt-2">↑ 527 from last month</p>
                                </div>
                            </div>
                        </div>

                        <!-- Products Card -->
                        <div class="group relative bg-white rounded-2xl shadow-lg border border-gray-100 p-6 hover:shadow-xl transition-all duration-300 hover:-translate-y-1">
                            <div class="absolute inset-0 bg-gradient-to-br from-purple-50 to-pink-50 rounded-2xl opacity-0 group-hover:opacity-100 transition-opacity"></div>
                            <div class="relative">
                                <div class="flex items-center justify-between mb-4">
                                    <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-pink-600 rounded-xl flex items-center justify-center shadow-lg">
                                        <span class="text-2xl">🎯</span>
                                    </div>
                                    <span class="text-xs font-medium text-purple-600 bg-purple-50 px-2 py-1 rounded-full">
                                        +12.1%
                                    </span>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-600 mb-1">Products</p>
                                    <p class="text-3xl font-bold text-gray-900">156</p>
                                    <p class="text-xs text-gray-500 mt-2">↑ 17 new products</p>
                                </div>
                            </div>
                        </div>

                        <!-- Customers Card -->
                        <div class="group relative bg-white rounded-2xl shadow-lg border border-gray-100 p-6 hover:shadow-xl transition-all duration-300 hover:-translate-y-1">
                            <div class="absolute inset-0 bg-gradient-to-br from-amber-50 to-orange-50 rounded-2xl opacity-0 group-hover:opacity-100 transition-opacity"></div>
                            <div class="relative">
                                <div class="flex items-center justify-between mb-4">
                                    <div class="w-12 h-12 bg-gradient-to-br from-amber-500 to-orange-600 rounded-xl flex items-center justify-center shadow-lg">
                                        <span class="text-2xl">👥</span>
                                    </div>
                                    <span class="text-xs font-medium text-amber-600 bg-amber-50 px-2 py-1 rounded-full">
                                        +8.4%
                                    </span>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-600 mb-1">Customers</p>
                                    <p class="text-3xl font-bold text-gray-900">1,289</p>
                                    <p class="text-xs text-gray-500 mt-2">↑ 101 new customers</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Actions & Top Products -->
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                        <!-- Quick Actions -->
                        <div class="lg:col-span-2 space-y-6">
                            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-8">
                                <div class="flex items-center justify-between mb-6">
                                    <div>
                                        <h3 class="text-xl font-bold text-gray-900">Quick Actions</h3>
                                        <p class="text-sm text-gray-600 mt-1">Manage your business efficiently</p>
                                    </div>
                                    <div class="w-2 h-2 bg-blue-500 rounded-full animate-pulse"></div>
                                </div>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <a href="#products" class="group relative overflow-hidden rounded-xl border border-gray-200 p-6 hover:border-gray-300 hover:shadow-lg transition-all duration-300">
                                        <div class="absolute inset-0 bg-gradient-to-br from-emerald-500 to-emerald-600 opacity-0 group-hover:opacity-5 transition-opacity"></div>
                                        <div class="relative flex items-start space-x-4">
                                            <div class="w-12 h-12 bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-xl flex items-center justify-center flex-shrink-0 shadow-md">
                                                <span class="text-white text-xl">📦</span>
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <h4 class="text-base font-semibold text-gray-900 group-hover:text-blue-600 transition-colors mb-1">
                                                    Product Catalog
                                                </h4>
                                                <p class="text-sm text-gray-600">Manage your inventory and products</p>
                                            </div>
                                            <svg class="w-5 h-5 text-gray-400 group-hover:text-gray-600 group-hover:translate-x-1 transition-all flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                            </svg>
                                        </div>
                                    </a>
                                    
                                    <a href="#orders" class="group relative overflow-hidden rounded-xl border border-gray-200 p-6 hover:border-gray-300 hover:shadow-lg transition-all duration-300">
                                        <div class="absolute inset-0 bg-gradient-to-br from-blue-500 to-blue-600 opacity-0 group-hover:opacity-5 transition-opacity"></div>
                                        <div class="relative flex items-start space-x-4">
                                            <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center flex-shrink-0 shadow-md">
                                                <span class="text-white text-xl">🛒</span>
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <h4 class="text-base font-semibold text-gray-900 group-hover:text-blue-600 transition-colors mb-1">
                                                    Order Management
                                                </h4>
                                                <p class="text-sm text-gray-600">Process and track customer orders</p>
                                            </div>
                                            <svg class="w-5 h-5 text-gray-400 group-hover:text-gray-600 group-hover:translate-x-1 transition-all flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                            </svg>
                                        </div>
                                    </a>

                                    <a href="#analytics" class="group relative overflow-hidden rounded-xl border border-gray-200 p-6 hover:border-gray-300 hover:shadow-lg transition-all duration-300">
                                        <div class="absolute inset-0 bg-gradient-to-br from-violet-500 to-violet-600 opacity-0 group-hover:opacity-5 transition-opacity"></div>
                                        <div class="relative flex items-start space-x-4">
                                            <div class="w-12 h-12 bg-gradient-to-br from-violet-500 to-violet-600 rounded-xl flex items-center justify-center flex-shrink-0 shadow-md">
                                                <span class="text-white text-xl">📈</span>
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <h4 class="text-base font-semibold text-gray-900 group-hover:text-blue-600 transition-colors mb-1">
                                                    Marketing Tools
                                                </h4>
                                                <p class="text-sm text-gray-600">Campaigns and promotions</p>
                                            </div>
                                            <svg class="w-5 h-5 text-gray-400 group-hover:text-gray-600 group-hover:translate-x-1 transition-all flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                            </svg>
                                        </div>
                                    </a>

                                    <a href="#settings" class="group relative overflow-hidden rounded-xl border border-gray-200 p-6 hover:border-gray-300 hover:shadow-lg transition-all duration-300">
                                        <div class="absolute inset-0 bg-gradient-to-br from-amber-500 to-amber-600 opacity-0 group-hover:opacity-5 transition-opacity"></div>
                                        <div class="relative flex items-start space-x-4">
                                            <div class="w-12 h-12 bg-gradient-to-br from-amber-500 to-amber-600 rounded-xl flex items-center justify-center flex-shrink-0 shadow-md">
                                                <span class="text-white text-xl">💡</span>
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <h4 class="text-base font-semibold text-gray-900 group-hover:text-blue-600 transition-colors mb-1">
                                                    Business Insights
                                                </h4>
                                                <p class="text-sm text-gray-600">Advanced analytics and reports</p>
                                            </div>
                                            <svg class="w-5 h-5 text-gray-400 group-hover:text-gray-600 group-hover:translate-x-1 transition-all flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                            </svg>
                                        </div>
                                    </a>
                                </div>
                            </div>

                            <!-- Top Products -->
                            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-8">
                                <div class="flex items-center justify-between mb-6">
                                    <div>
                                        <h3 class="text-xl font-bold text-gray-900">Top Products</h3>
                                        <p class="text-sm text-gray-600 mt-1">Best performing items this month</p>
                                    </div>
                                    <button class="text-blue-600 hover:text-blue-700 font-medium">View all →</button>
                                </div>
                                
                                <div class="space-y-4">
                                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl hover:bg-gray-100 transition-colors">
                                        <div class="flex items-center space-x-4">
                                            <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-purple-600 rounded-lg flex items-center justify-center text-white font-bold text-sm">
                                                1
                                            </div>
                                            <div>
                                                <h4 class="font-medium text-gray-900">Wireless Headphones</h4>
                                                <p class="text-sm text-gray-600">234 sold • 45 in stock</p>
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <p class="font-semibold text-gray-900">$70,200</p>
                                            <p class="text-xs text-green-600">↑ 15.3%</p>
                                        </div>
                                    </div>
                                    
                                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl hover:bg-gray-100 transition-colors">
                                        <div class="flex items-center space-x-4">
                                            <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-purple-600 rounded-lg flex items-center justify-center text-white font-bold text-sm">
                                                2
                                            </div>
                                            <div>
                                                <h4 class="font-medium text-gray-900">Smart Watch Pro</h4>
                                                <p class="text-sm text-gray-600">189 sold • 23 in stock</p>
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <p class="font-semibold text-gray-900">$56,700</p>
                                            <p class="text-xs text-green-600">↑ 8.7%</p>
                                        </div>
                                    </div>
                                    
                                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl hover:bg-gray-100 transition-colors">
                                        <div class="flex items-center space-x-4">
                                            <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-purple-600 rounded-lg flex items-center justify-center text-white font-bold text-sm">
                                                3
                                            </div>
                                            <div>
                                                <h4 class="font-medium text-gray-900">Laptop Stand</h4>
                                                <p class="text-sm text-gray-600">156 sold • 67 in stock</p>
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <p class="font-semibold text-gray-900">$23,400</p>
                                            <p class="text-xs text-red-600">↓ 2.1%</p>
                                        </div>
                                    </div>
                                    
                                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl hover:bg-gray-100 transition-colors">
                                        <div class="flex items-center space-x-4">
                                            <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-purple-600 rounded-lg flex items-center justify-center text-white font-bold text-sm">
                                                4
                                            </div>
                                            <div>
                                                <h4 class="font-medium text-gray-900">USB-C Hub</h4>
                                                <p class="text-sm text-gray-600">143 sold • 89 in stock</p>
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <p class="font-semibold text-gray-900">$21,450</p>
                                            <p class="text-xs text-green-600">↑ 22.4%</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Recent Orders -->
                        <div class="lg:col-span-1">
                            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-8 sticky top-6">
                                <div class="flex items-center justify-between mb-6">
                                    <div>
                                        <h3 class="text-xl font-bold text-gray-900">Recent Orders</h3>
                                        <p class="text-sm text-gray-600 mt-1">Latest customer orders</p>
                                    </div>
                                    <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
                                </div>
                                
                                <div class="space-y-4 max-h-96 overflow-y-auto">
                                    <div class="flex items-start space-x-3 p-3 rounded-lg hover:bg-gray-50 transition-colors">
                                        <div class="w-10 h-10 bg-gradient-to-br from-blue-100 to-purple-100 rounded-xl flex items-center justify-center flex-shrink-0 group-hover:scale-110 transition-transform">
                                            <span class="text-lg">📦</span>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-medium text-gray-900">ORD-2024-001</p>
                                            <p class="text-sm text-gray-600">John Doe</p>
                                            <div class="flex items-center justify-between mt-1">
                                                <span class="text-xs text-gray-500">2024-01-20</span>
                                                <span class="px-2 py-1 text-xs font-medium rounded-full bg-yellow-100 text-yellow-800">
                                                    processing
                                                </span>
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <p class="font-semibold text-gray-900">$299.99</p>
                                            <p class="text-xs text-gray-500">3 items</p>
                                        </div>
                                    </div>
                                    
                                    <div class="flex items-start space-x-3 p-3 rounded-lg hover:bg-gray-50 transition-colors">
                                        <div class="w-10 h-10 bg-gradient-to-br from-blue-100 to-purple-100 rounded-xl flex items-center justify-center flex-shrink-0 group-hover:scale-110 transition-transform">
                                            <span class="text-lg">📦</span>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-medium text-gray-900">ORD-2024-002</p>
                                            <p class="text-sm text-gray-600">Jane Smith</p>
                                            <div class="flex items-center justify-between mt-1">
                                                <span class="text-xs text-gray-500">2024-01-20</span>
                                                <span class="px-2 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-800">
                                                    shipped
                                                </span>
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <p class="font-semibold text-gray-900">$189.99</p>
                                            <p class="text-xs text-gray-500">2 items</p>
                                        </div>
                                    </div>
                                    
                                    <div class="flex items-start space-x-3 p-3 rounded-lg hover:bg-gray-50 transition-colors">
                                        <div class="w-10 h-10 bg-gradient-to-br from-blue-100 to-purple-100 rounded-xl flex items-center justify-center flex-shrink-0 group-hover:scale-110 transition-transform">
                                            <span class="text-lg">📦</span>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-medium text-gray-900">ORD-2024-003</p>
                                            <p class="text-sm text-gray-600">Bob Johnson</p>
                                            <div class="flex items-center justify-between mt-1">
                                                <span class="text-xs text-gray-500">2024-01-19</span>
                                                <span class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">
                                                    delivered
                                                </span>
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <p class="font-semibold text-gray-900">$449.99</p>
                                            <p class="text-xs text-gray-500">1 item</p>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="mt-6 pt-6 border-t border-gray-200">
                                    <a href="#" class="flex items-center justify-center w-full py-2 text-sm font-medium text-blue-600 hover:text-blue-700 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors">
                                        View all orders
                                        <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                        </svg>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Admin Dashboard Content -->
        <div v-else-if="isSuperAdmin">
            <div class="relative">
                <div class="absolute inset-0 bg-gradient-to-r from-purple-50 via-red-50 to-orange-50 -z-10 rounded-2xl"></div>
                <div class="relative px-8 py-6">
                    <div class="flex justify-between items-start">
                        <div class="space-y-2">
                            <div class="flex items-center space-x-3">
                                <div class="w-2 h-2 bg-red-500 rounded-full animate-pulse"></div>
                                <h1 class="text-3xl font-bold bg-gradient-to-r from-gray-900 to-gray-700 bg-clip-text text-transparent">
                                    Super Admin Dashboard
                                </h1>
                            </div>
                            <p class="text-gray-600 text-lg">Platform administration and oversight</p>
                            <div class="flex items-center space-x-4 text-sm">
                                <span class="text-gray-500">System performance</span>
                                <span class="px-2 py-1 bg-green-100 text-green-700 rounded-full font-medium">
                                    99.9% uptime
                                </span>
                            </div>
                        </div>
                        
                        <div class="flex items-center space-x-3">
                            <div class="text-right">
                                <p class="text-sm text-gray-500">Platform Status</p>
                                <p class="text-xl font-bold text-green-600">All Systems Operational</p>
                            </div>
                            <div class="w-12 h-12 bg-gradient-to-br from-green-500 to-emerald-600 rounded-xl flex items-center justify-center shadow-lg">
                                <span class="text-white text-xl">✅</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="px-4 sm:px-6 lg:px-8 py-6">
                <div class="mx-auto max-w-7xl space-y-8">
                    
                    <!-- Enhanced System Stats -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6">
                        <!-- Users Card -->
                        <div class="group relative bg-white rounded-2xl shadow-lg border border-gray-100 p-6 hover:shadow-xl transition-all duration-300 hover:-translate-y-1">
                            <div class="absolute inset-0 bg-gradient-to-br from-purple-50 to-pink-50 rounded-2xl opacity-0 group-hover:opacity-100 transition-opacity"></div>
                            <div class="relative">
                                <div class="flex items-center justify-between mb-4">
                                    <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-pink-600 rounded-xl flex items-center justify-center shadow-lg">
                                        <span class="text-2xl">👥</span>
                                    </div>
                                    <span class="text-xs font-medium text-purple-600 bg-purple-50 px-2 py-1 rounded-full">
                                        +15.2%
                                    </span>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-600 mb-1">Total Users</p>
                                    <p class="text-3xl font-bold text-gray-900">15,420</p>
                                    <p class="text-xs text-gray-500 mt-2">↑ 2,034 this month</p>
                                </div>
                            </div>
                        </div>

                        <!-- Tenants Card -->
                        <div class="group relative bg-white rounded-2xl shadow-lg border border-gray-100 p-6 hover:shadow-xl transition-all duration-300 hover:-translate-y-1">
                            <div class="absolute inset-0 bg-gradient-to-br from-blue-50 to-indigo-50 rounded-2xl opacity-0 group-hover:opacity-100 transition-opacity"></div>
                            <div class="relative">
                                <div class="flex items-center justify-between mb-4">
                                    <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center shadow-lg">
                                        <span class="text-2xl">🏢</span>
                                    </div>
                                    <span class="text-xs font-medium text-blue-600 bg-blue-50 px-2 py-1 rounded-full">
                                        +8.7%
                                    </span>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-600 mb-1">Tenants</p>
                                    <p class="text-3xl font-bold text-gray-900">234</p>
                                    <p class="text-xs text-gray-500 mt-2">↑ 19 this month</p>
                                </div>
                            </div>
                        </div>

                        <!-- Revenue Card -->
                        <div class="group relative bg-white rounded-2xl shadow-lg border border-gray-100 p-6 hover:shadow-xl transition-all duration-300 hover:-translate-y-1">
                            <div class="absolute inset-0 bg-gradient-to-br from-emerald-50 to-teal-50 rounded-2xl opacity-0 group-hover:opacity-100 transition-opacity"></div>
                            <div class="relative">
                                <div class="flex items-center justify-between mb-4">
                                    <div class="w-12 h-12 bg-gradient-to-br from-emerald-500 to-teal-600 rounded-xl flex items-center justify-center shadow-lg">
                                        <span class="text-2xl">💰</span>
                                    </div>
                                    <span class="text-xs font-medium text-emerald-600 bg-emerald-50 px-2 py-1 rounded-full">
                                        +28.4%
                                    </span>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-600 mb-1">Revenue</p>
                                    <p class="text-3xl font-bold text-gray-900">$2.4M</p>
                                    <p class="text-xs text-gray-500 mt-2">↑ $543k this month</p>
                                </div>
                            </div>
                        </div>

                        <!-- Active Stores Card -->
                        <div class="group relative bg-white rounded-2xl shadow-lg border border-gray-100 p-6 hover:shadow-xl transition-all duration-300 hover:-translate-y-1">
                            <div class="absolute inset-0 bg-gradient-to-br from-amber-50 to-orange-50 rounded-2xl opacity-0 group-hover:opacity-100 transition-opacity"></div>
                            <div class="relative">
                                <div class="flex items-center justify-between mb-4">
                                    <div class="w-12 h-12 bg-gradient-to-br from-amber-500 to-orange-600 rounded-xl flex items-center justify-center shadow-lg">
                                        <span class="text-2xl">🏪</span>
                                    </div>
                                    <span class="text-xs font-medium text-amber-600 bg-amber-50 px-2 py-1 rounded-full">
                                        Active
                                    </span>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-600 mb-1">Active Stores</p>
                                    <p class="text-3xl font-bold text-gray-900">189</p>
                                    <p class="text-xs text-gray-500 mt-2">80% occupancy</p>
                                </div>
                            </div>
                        </div>

                        <!-- Growth Card -->
                        <div class="group relative bg-white rounded-2xl shadow-lg border border-gray-100 p-6 hover:shadow-xl transition-all duration-300 hover:-translate-y-1">
                            <div class="absolute inset-0 bg-gradient-to-br from-green-50 to-emerald-50 rounded-2xl opacity-0 group-hover:opacity-100 transition-opacity"></div>
                            <div class="relative">
                                <div class="flex items-center justify-between mb-4">
                                    <div class="w-12 h-12 bg-gradient-to-br from-green-500 to-emerald-600 rounded-xl flex items-center justify-center shadow-lg">
                                        <span class="text-2xl">📈</span>
                                    </div>
                                    <span class="text-xs font-medium text-green-600 bg-green-50 px-2 py-1 rounded-full">
                                        Healthy
                                    </span>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-600 mb-1">Growth</p>
                                    <p class="text-3xl font-bold text-gray-900">28.4%</p>
                                    <p class="text-xs text-gray-500 mt-2">Year over year</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Admin Actions & Top Tenants -->
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                        <!-- Admin Actions -->
                        <div class="lg:col-span-2 space-y-6">
                            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-8">
                                <div class="flex items-center justify-between mb-6">
                                    <div>
                                        <h3 class="text-xl font-bold text-gray-900">Admin Actions</h3>
                                        <p class="text-sm text-gray-600 mt-1">System management tools</p>
                                    </div>
                                    <div class="w-2 h-2 bg-purple-500 rounded-full animate-pulse"></div>
                                </div>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <a href="#users" class="group relative overflow-hidden rounded-xl border border-gray-200 p-6 hover:border-gray-300 hover:shadow-lg transition-all duration-300">
                                        <div class="absolute inset-0 bg-gradient-to-br from-purple-500 to-purple-600 opacity-0 group-hover:opacity-5 transition-opacity"></div>
                                        <div class="relative flex items-start space-x-4">
                                            <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl flex items-center justify-center flex-shrink-0 shadow-md">
                                                <span class="text-white text-xl">👥</span>
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <h4 class="text-base font-semibold text-gray-900 group-hover:text-blue-600 transition-colors mb-1">
                                                    Manage Users
                                                </h4>
                                                <p class="text-sm text-gray-600">View and manage all platform users</p>
                                            </div>
                                            <svg class="w-5 h-5 text-gray-400 group-hover:text-gray-600 group-hover:translate-x-1 transition-all flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                            </svg>
                                        </div>
                                    </a>
                                    
                                    <a href="#tenants" class="group relative overflow-hidden rounded-xl border border-gray-200 p-6 hover:border-gray-300 hover:shadow-lg transition-all duration-300">
                                        <div class="absolute inset-0 bg-gradient-to-br from-blue-500 to-blue-600 opacity-0 group-hover:opacity-5 transition-opacity"></div>
                                        <div class="relative flex items-start space-x-4">
                                            <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center flex-shrink-0 shadow-md">
                                                <span class="text-white text-xl">🏢</span>
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <h4 class="text-base font-semibold text-gray-900 group-hover:text-blue-600 transition-colors mb-1">
                                                    Tenant Management
                                                </h4>
                                                <p class="text-sm text-gray-600">Configure tenant settings</p>
                                            </div>
                                            <svg class="w-5 h-5 text-gray-400 group-hover:text-gray-600 group-hover:translate-x-1 transition-all flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                            </svg>
                                        </div>
                                    </a>

                                    <a href="#analytics" class="group relative overflow-hidden rounded-xl border border-gray-200 p-6 hover:border-gray-300 hover:shadow-lg transition-all duration-300">
                                        <div class="absolute inset-0 bg-gradient-to-br from-green-500 to-green-600 opacity-0 group-hover:opacity-5 transition-opacity"></div>
                                        <div class="relative flex items-start space-x-4">
                                            <div class="w-12 h-12 bg-gradient-to-br from-green-500 to-green-600 rounded-xl flex items-center justify-center flex-shrink-0 shadow-md">
                                                <span class="text-white text-xl">📊</span>
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <h4 class="text-base font-semibold text-gray-900 group-hover:text-blue-600 transition-colors mb-1">
                                                    System Analytics
                                                </h4>
                                                <p class="text-sm text-gray-600">View platform-wide statistics</p>
                                            </div>
                                            <svg class="w-5 h-5 text-gray-400 group-hover:text-gray-600 group-hover:translate-x-1 transition-all flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                            </svg>
                                        </div>
                                    </a>

                                    <a href="#security" class="group relative overflow-hidden rounded-xl border border-gray-200 p-6 hover:border-gray-300 hover:shadow-lg transition-all duration-300">
                                        <div class="absolute inset-0 bg-gradient-to-br from-red-500 to-red-600 opacity-0 group-hover:opacity-5 transition-opacity"></div>
                                        <div class="relative flex items-start space-x-4">
                                            <div class="w-12 h-12 bg-gradient-to-br from-red-500 to-red-600 rounded-xl flex items-center justify-center flex-shrink-0 shadow-md">
                                                <span class="text-white text-xl">🔒</span>
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <h4 class="text-base font-semibold text-gray-900 group-hover:text-blue-600 transition-colors mb-1">
                                                    Security Center
                                                </h4>
                                                <p class="text-sm text-gray-600">Monitor and manage security</p>
                                            </div>
                                            <svg class="w-5 h-5 text-gray-400 group-hover:text-gray-600 group-hover:translate-x-1 transition-all flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                            </svg>
                                        </div>
                                    </a>
                                </div>
                            </div>

                            <!-- Top Performing Tenants -->
                            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-8">
                                <div class="flex items-center justify-between mb-6">
                                    <div>
                                        <h3 class="text-xl font-bold text-gray-900">Top Performing Tenants</h3>
                                        <p class="text-sm text-gray-600 mt-1">Highest revenue generators</p>
                                    </div>
                                    <button class="text-blue-600 hover:text-blue-700 font-medium">View all →</button>
                                </div>
                                
                                <div class="space-y-4">
                                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl hover:bg-gray-100 transition-colors">
                                        <div class="flex items-center space-x-4">
                                            <div class="w-8 h-8 bg-gradient-to-br from-purple-500 to-pink-600 rounded-lg flex items-center justify-center text-white font-bold text-sm">
                                                1
                                            </div>
                                            <div>
                                                <h4 class="font-medium text-gray-900">TechZone</h4>
                                                <p class="text-sm text-gray-600">$45,230 revenue • 1,234 users</p>
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <p class="font-semibold text-gray-900">+23.5%</p>
                                        </div>
                                    </div>
                                    
                                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl hover:bg-gray-100 transition-colors">
                                        <div class="flex items-center space-x-4">
                                            <div class="w-8 h-8 bg-gradient-to-br from-purple-500 to-pink-600 rounded-lg flex items-center justify-center text-white font-bold text-sm">
                                                2
                                            </div>
                                            <div>
                                                <h4 class="font-medium text-gray-900">FashionHub</h4>
                                                <p class="text-sm text-gray-600">$38,920 revenue • 987 users</p>
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <p class="font-semibold text-gray-900">+18.2%</p>
                                        </div>
                                    </div>
                                    
                                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl hover:bg-gray-100 transition-colors">
                                        <div class="flex items-center space-x-4">
                                            <div class="w-8 h-8 bg-gradient-to-br from-purple-500 to-pink-600 rounded-lg flex items-center justify-center text-white font-bold text-sm">
                                                3
                                            </div>
                                            <div>
                                                <h4 class="font-medium text-gray-900">HomeDecor</h4>
                                                <p class="text-sm text-gray-600">$32,150 revenue • 756 users</p>
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <p class="font-semibold text-gray-900">+12.7%</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- System Activity -->
                        <div class="lg:col-span-1">
                            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-8 sticky top-6">
                                <div class="flex items-center justify-between mb-6">
                                    <div>
                                        <h3 class="text-xl font-bold text-gray-900">System Activity</h3>
                                        <p class="text-sm text-gray-600 mt-1">Real-time monitoring</p>
                                    </div>
                                    <div class="w-2 h-2 bg-red-500 rounded-full animate-pulse"></div>
                                </div>
                                
                                <div class="space-y-4 max-h-96 overflow-y-auto">
                                    <div class="flex items-start space-x-3 p-3 rounded-lg hover:bg-gray-50 transition-colors">
                                        <div class="w-10 h-10 bg-gradient-to-br from-gray-100 to-gray-200 rounded-xl flex items-center justify-center flex-shrink-0 group-hover:scale-110 transition-transform">
                                            <span class="text-lg">👤</span>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-medium text-gray-900">New user registration: john@example.com</p>
                                            <div class="flex items-center justify-between mt-1">
                                                <span class="text-xs text-gray-500">2 minutes ago</span>
                                                <span class="px-2 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-800">
                                                    info
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="flex items-start space-x-3 p-3 rounded-lg hover:bg-gray-50 transition-colors">
                                        <div class="w-10 h-10 bg-gradient-to-br from-gray-100 to-gray-200 rounded-xl flex items-center justify-center flex-shrink-0 group-hover:scale-110 transition-transform">
                                            <span class="text-lg">🏢</span>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-medium text-gray-900">New tenant created: TechStore Inc.</p>
                                            <div class="flex items-center justify-between mt-1">
                                                <span class="text-xs text-gray-500">15 minutes ago</span>
                                                <span class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">
                                                    success
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="flex items-start space-x-3 p-3 rounded-lg hover:bg-gray-50 transition-colors">
                                        <div class="w-10 h-10 bg-gradient-to-br from-gray-100 to-gray-200 rounded-xl flex items-center justify-center flex-shrink-0 group-hover:scale-110 transition-transform">
                                            <span class="text-lg">💾</span>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-medium text-gray-900">System backup completed successfully</p>
                                            <div class="flex items-center justify-between mt-1">
                                                <span class="text-xs text-gray-500">1 hour ago</span>
                                                <span class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">
                                                    success
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="flex items-start space-x-3 p-3 rounded-lg hover:bg-gray-50 transition-colors">
                                        <div class="w-10 h-10 bg-gradient-to-br from-gray-100 to-gray-200 rounded-xl flex items-center justify-center flex-shrink-0 group-hover:scale-110 transition-transform">
                                            <span class="text-lg">🔒</span>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-medium text-gray-900">Failed login attempt detected</p>
                                            <div class="flex items-center justify-between mt-1">
                                                <span class="text-xs text-gray-500">2 hours ago</span>
                                                <span class="px-2 py-1 text-xs font-medium rounded-full bg-yellow-100 text-yellow-800">
                                                    warning
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="mt-6 pt-6 border-t border-gray-200">
                                    <a href="#" class="flex items-center justify-center w-full py-2 text-sm font-medium text-blue-600 hover:text-blue-700 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors">
                                        View system logs
                                        <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                        </svg>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Default/Fallback -->
        <div v-else class="flex items-center justify-center min-h-screen">
            <div class="text-center">
                <div class="w-16 h-16 border-4 border-gray-300 border-t-gray-600 rounded-full animate-spin mx-auto mb-4"></div>
                <p class="text-gray-600">Loading your dashboard...</p>
                <p class="text-sm text-gray-500 mt-2">Please wait while we set up your experience</p>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
