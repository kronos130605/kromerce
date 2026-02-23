<script setup>
import { computed, ref } from 'vue';
import { usePage } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';

const page = usePage();
const user = computed(() => page.props.auth.user);
const currentTenant = computed(() => page.props.current_tenant);

// Mock data for business dashboard
const stats = ref({
    totalRevenue: 125430,
    totalOrders: 3421,
    totalProducts: 156,
    totalCustomers: 1289,
    growth: 23.5,
    conversionRate: 3.2,
    avgOrderValue: 36.67
});

const topProducts = ref([
    { name: 'Wireless Headphones', sales: 234, revenue: 70200, growth: 15.3, stock: 45 },
    { name: 'Smart Watch Pro', sales: 189, revenue: 56700, growth: 8.7, stock: 23 },
    { name: 'Laptop Stand', sales: 156, revenue: 23400, growth: -2.1, stock: 67 },
    { name: 'USB-C Hub', sales: 143, revenue: 21450, growth: 22.4, stock: 89 }
]);

const recentOrders = ref([
    {
        id: 'ORD-2024-001',
        customer: 'John Doe',
        date: '2024-01-20',
        status: 'processing',
        total: 299.99,
        items: 3
    },
    {
        id: 'ORD-2024-002',
        customer: 'Jane Smith',
        date: '2024-01-20',
        status: 'shipped',
        total: 189.99,
        items: 2
    },
    {
        id: 'ORD-2024-003',
        customer: 'Bob Johnson',
        date: '2024-01-19',
        status: 'delivered',
        total: 449.99,
        items: 1
    }
]);

const quickActions = computed(() => [
    { title: 'Product Catalog', description: 'Manage your inventory', icon: '📦', color: 'from-emerald-500 to-emerald-600', href: '#products' },
    { title: 'Order Management', description: 'Process orders', icon: '🛒', color: 'from-blue-500 to-blue-600', href: '#orders' },
    { title: 'Marketing Tools', description: 'Campaigns', icon: '📈', color: 'from-violet-500 to-violet-600', href: '#marketing' },
    { title: 'Business Insights', description: 'Analytics', icon: '💡', color: 'from-amber-500 to-amber-600', href: '#insights' }
]);

const getStatusColor = (status) => {
    const colors = {
        'processing': 'bg-yellow-100 text-yellow-800',
        'shipped': 'bg-blue-100 text-blue-800',
        'delivered': 'bg-green-100 text-green-800',
        'cancelled': 'bg-red-100 text-red-800'
    };
    return colors[status] || 'bg-gray-100 text-gray-800';
};
</script>

<template>
    <Head title="Business Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <div class="relative">
                <!-- Background gradient decoration -->
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
                                    ↑ {{ stats.growth }}% growth
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
        </template>

        <div class="px-4 sm:px-6 lg:px-8 py-6">
            <div class="mx-auto max-w-7xl space-y-8">
                
                <!-- Stats Grid -->
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
                                <p class="text-3xl font-bold text-gray-900">${{ stats.totalRevenue.toLocaleString() }}</p>
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
                                <p class="text-3xl font-bold text-gray-900">{{ stats.totalOrders.toLocaleString() }}</p>
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
                                <p class="text-3xl font-bold text-gray-900">{{ stats.totalProducts }}</p>
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
                                <p class="text-3xl font-bold text-gray-900">{{ stats.totalCustomers.toLocaleString() }}</p>
                                <p class="text-xs text-gray-500 mt-2">↑ 101 new customers</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Main Content Grid -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <!-- Quick Actions & Top Products -->
                    <div class="lg:col-span-2 space-y-6">
                        <!-- Quick Actions -->
                        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-8">
                            <div class="flex items-center justify-between mb-6">
                                <div>
                                    <h3 class="text-xl font-bold text-gray-900">Quick Actions</h3>
                                    <p class="text-sm text-gray-600 mt-1">Manage your business efficiently</p>
                                </div>
                                <div class="w-2 h-2 bg-blue-500 rounded-full animate-pulse"></div>
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <a v-for="action in quickActions" :key="action.title"
                                   :href="action.href"
                                   class="group relative overflow-hidden rounded-xl border border-gray-200 p-6 hover:border-gray-300 hover:shadow-lg transition-all duration-300">
                                    <div :class="`absolute inset-0 bg-gradient-to-br ${action.color} opacity-0 group-hover:opacity-5 transition-opacity`"></div>
                                    
                                    <div class="relative flex items-start space-x-4">
                                        <div :class="`w-12 h-12 bg-gradient-to-br ${action.color} rounded-xl flex items-center justify-center flex-shrink-0 shadow-md`">
                                            <span class="text-white text-xl">{{ action.icon }}</span>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <h4 class="text-base font-semibold text-gray-900 group-hover:text-blue-600 transition-colors mb-1">
                                                {{ action.title }}
                                            </h4>
                                            <p class="text-sm text-gray-600">{{ action.description }}</p>
                                        </div>
                                        <svg class="w-5 h-5 text-gray-400 group-hover:text-gray-600 group-hover:translate-x-1 transition-all flex-shrink-0" 
                                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                                    <p class="text-sm text-gray-600 mt-1">Best performing items</p>
                                </div>
                                <button class="text-blue-600 hover:text-blue-700 font-medium">View all →</button>
                            </div>
                            
                            <div class="space-y-4">
                                <div v-for="(product, index) in topProducts" :key="product.name" 
                                     class="flex items-center justify-between p-4 bg-gray-50 rounded-xl hover:bg-gray-100 transition-colors">
                                    <div class="flex items-center space-x-4">
                                        <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-purple-600 rounded-lg flex items-center justify-center text-white font-bold text-sm">
                                            {{ index + 1 }}
                                        </div>
                                        <div>
                                            <h4 class="font-medium text-gray-900">{{ product.name }}</h4>
                                            <p class="text-sm text-gray-600">{{ product.sales }} sold • {{ product.stock }} in stock</p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <p class="font-semibold text-gray-900">${{ product.revenue.toLocaleString() }}</p>
                                        <p class="text-xs" :class="product.growth > 0 ? 'text-green-600' : 'text-red-600'">
                                            {{ product.growth > 0 ? '↑' : '↓' }} {{ Math.abs(product.growth) }}%
                                        </p>
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
                                <div v-for="order in recentOrders" :key="order.id" 
                                     class="group relative">
                                    <div class="flex items-start space-x-3 p-3 rounded-lg hover:bg-gray-50 transition-colors">
                                        <div class="w-10 h-10 bg-gradient-to-br from-blue-100 to-purple-100 rounded-xl flex items-center justify-center flex-shrink-0 group-hover:scale-110 transition-transform">
                                            <span class="text-lg">📦</span>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-medium text-gray-900">{{ order.id }}</p>
                                            <p class="text-sm text-gray-600">{{ order.customer }}</p>
                                            <div class="flex items-center justify-between mt-1">
                                                <span class="text-xs text-gray-500">{{ order.date }}</span>
                                                <span :class="`px-2 py-1 text-xs font-medium rounded-full ${getStatusColor(order.status)}`">
                                                    {{ order.status }}
                                                </span>
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <p class="font-semibold text-gray-900">${{ order.total }}</p>
                                            <p class="text-xs text-gray-500">{{ order.items }} items</p>
                                        </div>
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
    </AuthenticatedLayout>
</template>
