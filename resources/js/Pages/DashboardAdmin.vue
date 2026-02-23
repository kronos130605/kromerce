<script setup>
import { computed, ref } from 'vue';
import { usePage } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';

const page = usePage();
const user = computed(() => page.props.auth.user);

// Mock data for admin dashboard
const systemStats = ref({
    totalUsers: 15420,
    totalTenants: 234,
    totalRevenue: 2456780,
    activeStores: 189,
    growth: 28.4,
    systemUptime: '99.9%'
});

const recentActivity = ref([
    { id: 1, type: 'user', message: 'New user registration: john@example.com', time: '2 minutes ago', icon: '👤', severity: 'info' },
    { id: 2, type: 'tenant', message: 'New tenant created: TechStore Inc.', time: '15 minutes ago', icon: '🏢', severity: 'success' },
    { id: 3, type: 'system', message: 'System backup completed successfully', time: '1 hour ago', icon: '💾', severity: 'success' },
    { id: 4, type: 'security', message: 'Failed login attempt detected', time: '2 hours ago', icon: '🔒', severity: 'warning' }
]);

const topTenants = ref([
    { name: 'TechZone', revenue: 45230, users: 1234, growth: 23.5, status: 'active' },
    { name: 'FashionHub', revenue: 38920, users: 987, growth: 18.2, status: 'active' },
    { name: 'HomeDecor', revenue: 32150, users: 756, growth: 12.7, status: 'active' },
    { name: 'SportsPlus', revenue: 28940, users: 623, growth: 8.9, status: 'active' }
]);

const quickActions = computed(() => [
    { title: 'Manage Users', description: 'View and manage all users', icon: '👥', color: 'from-purple-500 to-purple-600', href: '#users' },
    { title: 'Tenant Management', description: 'Configure tenant settings', icon: '🏢', color: 'from-blue-500 to-blue-600', href: '#tenants' },
    { title: 'System Analytics', description: 'View platform statistics', icon: '📊', color: 'from-green-500 to-green-600', href: '#analytics' },
    { title: 'Security Center', description: 'Monitor and manage security', icon: '🔒', color: 'from-red-500 to-red-600', href: '#security' }
]);

const getSeverityColor = (severity) => {
    const colors = {
        'info': 'bg-blue-100 text-blue-800',
        'success': 'bg-green-100 text-green-800',
        'warning': 'bg-yellow-100 text-yellow-800',
        'error': 'bg-red-100 text-red-800'
    };
    return colors[severity] || 'bg-gray-100 text-gray-800';
};
</script>

<template>
    <Head title="Admin Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <div class="relative">
                <!-- Background gradient decoration -->
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
                                    {{ systemStats.systemUptime }} uptime
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
        </template>

        <div class="px-4 sm:px-6 lg:px-8 py-6">
            <div class="mx-auto max-w-7xl space-y-8">
                
                <!-- System Stats Grid -->
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
                                <p class="text-3xl font-bold text-gray-900">{{ systemStats.totalUsers.toLocaleString() }}</p>
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
                                <p class="text-3xl font-bold text-gray-900">{{ systemStats.totalTenants }}</p>
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
                                <p class="text-3xl font-bold text-gray-900">${{ (systemStats.totalRevenue / 1000).toFixed(0) }}k</p>
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
                                <p class="text-3xl font-bold text-gray-900">{{ systemStats.activeStores }}</p>
                                <p class="text-xs text-gray-500 mt-2">{{ Math.round((systemStats.activeStores / systemStats.totalTenants) * 100) }}% occupancy</p>
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
                                <p class="text-3xl font-bold text-gray-900">{{ systemStats.growth }}%</p>
                                <p class="text-xs text-gray-500 mt-2">Year over year</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Main Content Grid -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <!-- Quick Actions & Top Tenants -->
                    <div class="lg:col-span-2 space-y-6">
                        <!-- Quick Actions -->
                        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-8">
                            <div class="flex items-center justify-between mb-6">
                                <div>
                                    <h3 class="text-xl font-bold text-gray-900">Admin Actions</h3>
                                    <p class="text-sm text-gray-600 mt-1">System management tools</p>
                                </div>
                                <div class="w-2 h-2 bg-purple-500 rounded-full animate-pulse"></div>
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

                        <!-- Top Tenants -->
                        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-8">
                            <div class="flex items-center justify-between mb-6">
                                <div>
                                    <h3 class="text-xl font-bold text-gray-900">Top Performing Tenants</h3>
                                    <p class="text-sm text-gray-600 mt-1">Highest revenue generators</p>
                                </div>
                                <button class="text-blue-600 hover:text-blue-700 font-medium">View all →</button>
                            </div>
                            
                            <div class="space-y-4">
                                <div v-for="(tenant, index) in topTenants" :key="tenant.name" 
                                     class="flex items-center justify-between p-4 bg-gray-50 rounded-xl hover:bg-gray-100 transition-colors">
                                    <div class="flex items-center space-x-4">
                                        <div class="w-8 h-8 bg-gradient-to-br from-purple-500 to-pink-600 rounded-lg flex items-center justify-center text-white font-bold text-sm">
                                            {{ index + 1 }}
                                        </div>
                                        <div>
                                            <h4 class="font-medium text-gray-900">{{ tenant.name }}</h4>
                                            <p class="text-sm text-gray-600">{{ tenant.users }} users</p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <p class="font-semibold text-gray-900">${{ tenant.revenue.toLocaleString() }}</p>
                                        <p class="text-xs" :class="tenant.growth > 0 ? 'text-green-600' : 'text-red-600'">
                                            {{ tenant.growth > 0 ? '↑' : '↓' }} {{ Math.abs(tenant.growth) }}%
                                        </p>
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
                                <div v-for="activity in recentActivity" :key="activity.id" 
                                     class="group relative">
                                    <div class="flex items-start space-x-3 p-3 rounded-lg hover:bg-gray-50 transition-colors">
                                        <div class="w-10 h-10 bg-gradient-to-br from-gray-100 to-gray-200 rounded-xl flex items-center justify-center flex-shrink-0 group-hover:scale-110 transition-transform">
                                            <span class="text-lg">{{ activity.icon }}</span>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-medium text-gray-900">{{ activity.message }}</p>
                                            <div class="flex items-center justify-between mt-1">
                                                <span class="text-xs text-gray-500">{{ activity.time }}</span>
                                                <span :class="`px-2 py-1 text-xs font-medium rounded-full ${getSeverityColor(activity.severity)}`">
                                                    {{ activity.severity }}
                                                </span>
                                            </div>
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
    </AuthenticatedLayout>
</template>
