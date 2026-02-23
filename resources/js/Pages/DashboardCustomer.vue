<script setup>
import { computed, ref, onMounted } from 'vue';
import { usePage } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';

const page = usePage();
const user = computed(() => page.props.auth.user);

// Mock data for customer dashboard
const featuredStores = ref([
    { 
        id: 1, 
        name: 'TechZone', 
        logo: '🖥️', 
        description: 'Latest electronics and gadgets',
        rating: 4.8, 
        products: 234,
        badge: 'Popular',
        discount: 20
    },
    { 
        id: 2, 
        name: 'FashionHub', 
        logo: '👗', 
        description: 'Trendy clothing and accessories',
        rating: 4.6, 
        products: 567,
        badge: 'New',
        discount: 15
    },
    { 
        id: 3, 
        name: 'HomeDecor', 
        logo: '🏠', 
        description: 'Beautiful home essentials',
        rating: 4.9, 
        products: 189,
        badge: 'Premium',
        discount: null
    },
    { 
        id: 4, 
        name: 'SportsPlus', 
        logo: '⚽', 
        description: 'Sports equipment and gear',
        rating: 4.7, 
        products: 412,
        badge: 'Sale',
        discount: 30
    }
]);

const personalizedProducts = ref([
    {
        id: 1,
        name: 'Wireless Noise-Canceling Headphones',
        store: 'TechZone',
        price: 299.99,
        originalPrice: 399.99,
        image: '🎧',
        rating: 4.8,
        reviews: 234,
        badge: 'Best Seller',
        discount: 25
    },
    {
        id: 2,
        name: 'Smart Watch Pro 2024',
        store: 'TechZone',
        price: 449.99,
        originalPrice: 599.99,
        image: '⌚',
        rating: 4.7,
        reviews: 189,
        badge: 'New',
        discount: 25
    },
    {
        id: 3,
        name: 'Designer Leather Jacket',
        store: 'FashionHub',
        price: 189.99,
        originalPrice: 249.99,
        image: '🧥',
        rating: 4.6,
        reviews: 92,
        badge: 'Limited',
        discount: 24
    },
    {
        id: 4,
        name: 'Yoga Mat Premium',
        store: 'SportsPlus',
        price: 49.99,
        originalPrice: 69.99,
        image: '🧘',
        rating: 4.9,
        reviews: 156,
        badge: 'Eco-Friendly',
        discount: 29
    }
]);

const specialOffers = ref([
    {
        id: 1,
        title: 'Flash Sale - TechZone',
        description: 'Up to 50% off on selected electronics',
        discount: 50,
        endTime: '2h 34m',
        image: '⚡',
        color: 'from-purple-500 to-pink-500'
    },
    {
        id: 2,
        title: 'Weekend Fashion Deals',
        description: 'Extra 20% off on all clothing',
        discount: 20,
        endTime: '1d 12h',
        image: '🛍️',
        color: 'from-pink-500 to-rose-500'
    },
    {
        id: 3,
        title: 'Home Makeover Sale',
        description: 'Save up to 40% on home decor',
        discount: 40,
        endTime: '3d 8h',
        image: '🏡',
        color: 'from-blue-500 to-cyan-500'
    }
]);

const recentOrders = ref([
    {
        id: 'ORD-2024-001',
        date: '2024-01-15',
        status: 'delivered',
        total: 299.99,
        items: 3,
        tracking: 'TRK123456789'
    },
    {
        id: 'ORD-2024-002',
        date: '2024-01-18',
        status: 'in-transit',
        total: 189.99,
        items: 2,
        tracking: 'TRK987654321'
    },
    {
        id: 'ORD-2024-003',
        date: '2024-01-20',
        status: 'processing',
        total: 449.99,
        items: 1,
        tracking: null
    }
]);

const upcomingEvents = ref([
    {
        id: 1,
        title: 'Virtual Fashion Show',
        date: 'Jan 25, 2024',
        time: '7:00 PM',
        store: 'FashionHub',
        type: 'live-event',
        image: '👗'
    },
    {
        id: 2,
        title: 'Tech Product Launch',
        date: 'Jan 28, 2024',
        time: '3:00 PM',
        store: 'TechZone',
        type: 'product-launch',
        image: '📱'
    },
    {
        id: 3,
        title: 'Home Decor Workshop',
        date: 'Jan 30, 2024',
        time: '5:00 PM',
        store: 'HomeDecor',
        type: 'workshop',
        image: '🎨'
    }
]);

const quickActions = computed(() => [
    { title: 'Browse Stores', description: 'Discover new shops', icon: '🏪', color: 'from-blue-500 to-blue-600', href: '#stores' },
    { title: 'My Orders', description: 'Track your purchases', icon: '📦', color: 'from-green-500 to-green-600', href: '#orders' },
    { title: 'Wishlist', description: 'Saved items', icon: '❤️', color: 'from-pink-500 to-pink-600', href: '#wishlist' },
    { title: 'Deals & Offers', description: 'Latest discounts', icon: '🎯', color: 'from-purple-500 to-purple-600', href: '#deals' }
]);

const getStatusColor = (status) => {
    const colors = {
        'delivered': 'bg-green-100 text-green-800',
        'in-transit': 'bg-blue-100 text-blue-800',
        'processing': 'bg-yellow-100 text-yellow-800',
        'cancelled': 'bg-red-100 text-red-800'
    };
    return colors[status] || 'bg-gray-100 text-gray-800';
};

const getEventTypeColor = (type) => {
    const colors = {
        'live-event': 'from-red-500 to-pink-500',
        'product-launch': 'from-blue-500 to-purple-500',
        'workshop': 'from-green-500 to-teal-500'
    };
    return colors[type] || 'from-gray-500 to-gray-600';
};
</script>

<template>
    <Head title="Customer Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <div class="relative">
                <!-- Background gradient decoration -->
                <div class="absolute inset-0 bg-gradient-to-r from-blue-50 via-purple-50 to-pink-50 -z-10 rounded-2xl"></div>
                
                <div class="relative px-8 py-6">
                    <div class="flex justify-between items-start">
                        <div class="space-y-2">
                            <div class="flex items-center space-x-3">
                                <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
                                <h1 class="text-3xl font-bold bg-gradient-to-r from-gray-900 to-gray-700 bg-clip-text text-transparent">
                    Welcome back, {{ user.name }}!
                </h1>
                            </div>
                            <p class="text-gray-600 text-lg">Discover amazing products and exclusive deals</p>
                            <div class="flex items-center space-x-4 text-sm">
                                <span class="text-gray-500">Personalized recommendations</span>
                                <span class="px-2 py-1 bg-purple-100 text-purple-700 rounded-full font-medium">
                                    VIP Member
                                </span>
                            </div>
                        </div>
                        
                        <div class="flex items-center space-x-3">
                            <div class="text-right">
                                <p class="text-sm text-gray-500">Available Balance</p>
                                <p class="text-xl font-bold text-gray-900">$250.00</p>
                            </div>
                            <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-pink-600 rounded-xl flex items-center justify-center shadow-lg">
                                <span class="text-white text-xl">💳</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </template>

        <div class="px-4 sm:px-6 lg:px-8 py-6">
            <div class="mx-auto max-w-7xl space-y-8">
                
                <!-- Special Offers Banner -->
                <div class="bg-gradient-to-r from-purple-600 to-pink-600 rounded-2xl p-8 text-white shadow-xl">
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="text-2xl font-bold mb-2">🔥 Flash Sale Event</h2>
                            <p class="text-purple-100 mb-4">Up to 50% off on selected items - Limited time only!</p>
                            <div class="flex items-center space-x-4">
                                <button class="px-6 py-3 bg-white text-purple-600 rounded-lg font-semibold hover:bg-purple-50 transition-colors">
                                    Shop Now
                                </button>
                                <div class="flex items-center space-x-2">
                                    <span class="text-sm">Ends in:</span>
                                    <span class="font-mono font-bold">2h 34m 12s</span>
                                </div>
                            </div>
                        </div>
                        <div class="text-6xl">⚡</div>
                    </div>
                </div>

                <!-- Featured Stores -->
                <div class="space-y-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-2xl font-bold text-gray-900">Featured Stores</h3>
                            <p class="text-gray-600 mt-1">Trending shops you'll love</p>
                        </div>
                        <button class="text-blue-600 hover:text-blue-700 font-medium">View all →</button>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                        <div v-for="store in featuredStores" :key="store.id" 
                             class="group bg-white rounded-2xl shadow-lg border border-gray-100 p-6 hover:shadow-xl transition-all duration-300 hover:-translate-y-1">
                            <div class="relative">
                                <div class="flex items-center justify-between mb-4">
                                    <div class="w-16 h-16 bg-gradient-to-br from-blue-100 to-purple-100 rounded-xl flex items-center justify-center text-2xl group-hover:scale-110 transition-transform">
                                        {{ store.logo }}
                                    </div>
                                    <span v-if="store.discount" 
                                          class="px-2 py-1 bg-red-500 text-white text-xs font-bold rounded-full">
                                        -{{ store.discount }}%
                                    </span>
                                </div>
                                
                                <div class="space-y-2">
                                    <h4 class="font-bold text-gray-900 group-hover:text-blue-600 transition-colors">{{ store.name }}</h4>
                                    <p class="text-sm text-gray-600 line-clamp-2">{{ store.description }}</p>
                                    
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center space-x-1">
                                            <span class="text-yellow-500">⭐</span>
                                            <span class="text-sm font-medium">{{ store.rating }}</span>
                                            <span class="text-sm text-gray-500">({{ store.products }})</span>
                                        </div>
                                        <span class="px-2 py-1 bg-purple-100 text-purple-700 text-xs font-medium rounded-full">
                                            {{ store.badge }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Personalized Products -->
                <div class="space-y-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-2xl font-bold text-gray-900">Recommended For You</h3>
                            <p class="text-gray-600 mt-1">Based on your shopping history</p>
                        </div>
                        <button class="text-blue-600 hover:text-blue-700 font-medium">See more →</button>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                        <div v-for="product in personalizedProducts" :key="product.id" 
                             class="group bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden hover:shadow-xl transition-all duration-300 hover:-translate-y-1">
                            <div class="relative">
                                <div class="h-48 bg-gradient-to-br from-gray-50 to-gray-100 flex items-center justify-center text-5xl group-hover:scale-110 transition-transform">
                                    {{ product.image }}
                                </div>
                                <span v-if="product.discount" 
                                      class="absolute top-3 left-3 px-2 py-1 bg-red-500 text-white text-xs font-bold rounded-full">
                                    -{{ product.discount }}%
                                </span>
                                <span class="absolute top-3 right-3 px-2 py-1 bg-purple-500 text-white text-xs font-medium rounded-full">
                                    {{ product.badge }}
                                </span>
                            </div>
                            
                            <div class="p-4 space-y-3">
                                <div>
                                    <h4 class="font-semibold text-gray-900 group-hover:text-blue-600 transition-colors line-clamp-2">
                                        {{ product.name }}
                                    </h4>
                                    <p class="text-sm text-gray-600">{{ product.store }}</p>
                                </div>
                                
                                <div class="flex items-center space-x-1">
                                    <span class="text-yellow-500">⭐</span>
                                    <span class="text-sm font-medium">{{ product.rating }}</span>
                                    <span class="text-sm text-gray-500">({{ product.reviews }})</span>
                                </div>
                                
                                <div class="flex items-center justify-between">
                                    <div>
                                        <span class="text-xl font-bold text-gray-900">${{ product.price }}</span>
                                        <span v-if="product.originalPrice" class="text-sm text-gray-500 line-through ml-2">
                                            ${{ product.originalPrice }}
                                        </span>
                                    </div>
                                    <button class="w-8 h-8 bg-blue-600 text-white rounded-lg flex items-center justify-center hover:bg-blue-700 transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions & Recent Orders -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <!-- Quick Actions -->
                    <div class="lg:col-span-2 space-y-6">
                        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-8">
                            <div class="flex items-center justify-between mb-6">
                                <div>
                                    <h3 class="text-xl font-bold text-gray-900">Quick Actions</h3>
                                    <p class="text-sm text-gray-600 mt-1">Get started with these common tasks</p>
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

                        <!-- Recent Orders -->
                        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-8">
                            <div class="flex items-center justify-between mb-6">
                                <div>
                                    <h3 class="text-xl font-bold text-gray-900">Recent Orders</h3>
                                    <p class="text-sm text-gray-600 mt-1">Track your latest purchases</p>
                                </div>
                                <button class="text-blue-600 hover:text-blue-700 font-medium">View all →</button>
                            </div>
                            
                            <div class="space-y-4">
                                <div v-for="order in recentOrders" :key="order.id" 
                                     class="flex items-center justify-between p-4 bg-gray-50 rounded-xl hover:bg-gray-100 transition-colors">
                                    <div class="flex items-center space-x-4">
                                        <div class="w-12 h-12 bg-gradient-to-br from-blue-100 to-purple-100 rounded-xl flex items-center justify-center">
                                            <span class="text-xl">📦</span>
                                        </div>
                                        <div>
                                            <h4 class="font-medium text-gray-900">{{ order.id }}</h4>
                                            <p class="text-sm text-gray-600">{{ order.date }} • {{ order.items }} items</p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <p class="font-semibold text-gray-900">${{ order.total }}</p>
                                        <span :class="`px-2 py-1 text-xs font-medium rounded-full ${getStatusColor(order.status)}`">
                                            {{ order.status.replace('-', ' ') }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Upcoming Events & Special Offers -->
                    <div class="lg:col-span-1 space-y-6">
                        <!-- Upcoming Events -->
                        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-8">
                            <div class="flex items-center justify-between mb-6">
                                <div>
                                    <h3 class="text-xl font-bold text-gray-900">Upcoming Events</h3>
                                    <p class="text-sm text-gray-600 mt-1">Don't miss out</p>
                                </div>
                                <div class="w-2 h-2 bg-red-500 rounded-full animate-pulse"></div>
                            </div>
                            
                            <div class="space-y-4">
                                <div v-for="event in upcomingEvents" :key="event.id" 
                                     class="group relative overflow-hidden rounded-xl border border-gray-200 p-4 hover:border-gray-300 hover:shadow-md transition-all duration-300">
                                    <div :class="`absolute inset-0 bg-gradient-to-br ${getEventTypeColor(event.type)} opacity-0 group-hover:opacity-5 transition-opacity`"></div>
                                    
                                    <div class="relative">
                                        <div class="flex items-start space-x-3">
                                            <div class="w-12 h-12 bg-gradient-to-br from-gray-100 to-gray-200 rounded-xl flex items-center justify-center text-2xl group-hover:scale-110 transition-transform">
                                                {{ event.image }}
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <h4 class="font-medium text-gray-900 group-hover:text-blue-600 transition-colors">
                                                    {{ event.title }}
                                                </h4>
                                                <p class="text-sm text-gray-600">{{ event.store }}</p>
                                                <div class="flex items-center space-x-2 mt-1">
                                                    <span class="text-xs text-gray-500">{{ event.date }}</span>
                                                    <span class="text-xs text-gray-500">•</span>
                                                    <span class="text-xs text-gray-500">{{ event.time }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Special Offers -->
                        <div class="bg-gradient-to-br from-purple-50 to-pink-50 rounded-2xl border border-purple-200 p-8">
                            <div class="flex items-center justify-between mb-6">
                                <div>
                                    <h3 class="text-xl font-bold text-gray-900">Hot Deals</h3>
                                    <p class="text-sm text-gray-600 mt-1">Limited time offers</p>
                                </div>
                                <div class="w-2 h-2 bg-orange-500 rounded-full animate-pulse"></div>
                            </div>
                            
                            <div class="space-y-4">
                                <div v-for="offer in specialOffers" :key="offer.id" 
                                     class="group relative overflow-hidden rounded-xl border border-purple-200 p-4 hover:border-purple-300 hover:shadow-md transition-all duration-300 bg-white/50">
                                    <div :class="`absolute inset-0 bg-gradient-to-br ${offer.color} opacity-0 group-hover:opacity-5 transition-opacity`"></div>
                                    
                                    <div class="relative">
                                        <div class="flex items-start space-x-3">
                                            <div class="w-12 h-12 bg-gradient-to-br from-purple-100 to-pink-100 rounded-xl flex items-center justify-center text-2xl group-hover:scale-110 transition-transform">
                                                {{ offer.image }}
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <h4 class="font-medium text-gray-900 group-hover:text-blue-600 transition-colors">
                                                    {{ offer.title }}
                                                </h4>
                                                <p class="text-sm text-gray-600">{{ offer.description }}</p>
                                                <div class="flex items-center justify-between mt-2">
                                                    <span class="text-xs font-medium text-red-600">-{{ offer.discount }}% OFF</span>
                                                    <span class="text-xs text-gray-500">Ends in {{ offer.endTime }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
