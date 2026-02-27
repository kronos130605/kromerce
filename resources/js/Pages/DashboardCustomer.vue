<script setup>
import { computed, ref, onMounted, watch } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';

// Import dashboard components
import CustomerWelcome from '@/components/Dashboard/CustomerWelcome.vue';
import FlashSaleBanner from '@/components/Dashboard/FlashSaleBanner.vue';
import FeaturedStores from '@/components/Dashboard/FeaturedStores.vue';
import AIRecommendations from '@/components/Dashboard/AIRecommendations.vue';
import QuickActions from '@/components/Dashboard/QuickActions.vue';

const page = usePage();
const user = computed(() => page.props.auth.user);
const { t } = useI18n();

// State management
const activeTab = ref('overview');
const searchQuery = ref('');
const selectedCategory = ref('all');
const priceRange = ref([0, 1000]);
const sortBy = ref('recommended');
const viewMode = ref('grid');
const notifications = ref([]);
const cartItems = ref(3);
const wishlistItems = ref(12);

// Real-time data simulation
const currentTime = ref(new Date());
const liveStats = ref({
    activeUsers: 1234,
    todaySales: 45678,
    newProducts: 89,
    flashSaleEnds: '2h 34m 12s'
});

// Enhanced user data
const userProfile = ref({
    name: user.value.name,
    avatar: user.value.avatar || null,
    level: 'VIP Gold',
    points: 2450,
    memberSince: '2023-01-15',
    totalSpent: 3420.50,
    savedAmount: 689.75,
    carbonSaved: 12.3,
    balance: 2847.50,
    ordersCount: 47
});

// Recent orders
const recentOrders = ref([
    {
        id: 'ORD-2024-001',
        date: '2024-01-15',
        status: 'delivered',
        total: 189.99,
        items: 3,
        store: 'TechZone',
        tracking: 'TRK123456789',
        progress: 100
    },
    {
        id: 'ORD-2024-002',
        date: '2024-01-18',
        status: 'shipped',
        total: 79.99,
        items: 2,
        store: 'EcoLiving',
        tracking: 'TRK987654321',
        progress: 75
    },
    {
        id: 'ORD-2024-003',
        date: '2024-01-20',
        status: 'processing',
        total: 249.99,
        items: 5,
        store: 'FashionHub',
        tracking: null,
        progress: 25
    }
]);

// Upcoming events
const upcomingEvents = ref([
    {
        id: 1,
        title: 'Mega Electronics Sale',
        date: '2024-01-25',
        time: '10:00 AM',
        type: 'sale',
        attendees: 1234,
        maxAttendees: 5000,
        registered: true,
        description: 'Up to 70% off on electronics'
    },
    {
        id: 2,
        title: 'Sustainable Living Workshop',
        date: '2024-01-27',
        time: '2:00 PM',
        type: 'workshop',
        attendees: 156,
        maxAttendees: 200,
        registered: false,
        description: 'Learn about eco-friendly products'
    },
    {
        id: 3,
        title: 'Fashion Week Exclusive',
        date: '2024-01-30',
        time: '6:00 PM',
        type: 'fashion',
        attendees: 892,
        maxAttendees: 1000,
        registered: true,
        description: 'Exclusive preview of new collections'
    }
]);

// Notification functions
const showNotification = (message, type = 'success') => {
    const notification = {
        id: Date.now(),
        message,
        type,
        timestamp: new Date()
    };
    notifications.value.push(notification);
    
    // Auto dismiss after 5 seconds
    setTimeout(() => {
        dismissNotification(notification.id);
    }, 5000);
};

const dismissNotification = (id) => {
    const index = notifications.value.findIndex(n => n.id === id);
    if (index > -1) {
        notifications.value.splice(index, 1);
    }
};

// Event handlers
const toggleEventRegistration = (event) => {
    event.registered = !event.registered;
    if (event.registered) {
        event.attendees++;
        showNotification(`Successfully registered for ${event.title}`, 'success');
    } else {
        event.attendees--;
        showNotification(`Unregistered from ${event.title}`, 'info');
    }
};

// Update live stats
onMounted(() => {
    setInterval(() => {
        currentTime.value = new Date();
        liveStats.value.activeUsers += Math.floor(Math.random() * 10) - 5;
        liveStats.value.todaySales += Math.floor(Math.random() * 100);
        liveStats.value.newProducts += Math.random() > 0.8 ? 1 : 0;
    }, 5000);
});
</script>

<template>
    <Head title="Customer Dashboard" />

    <AuthenticatedLayout>
        <div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-8">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Notifications -->
                <div v-if="notifications.length > 0" class="fixed top-4 right-4 z-50 space-y-2">
                    <div v-for="notification in notifications" :key="notification.id"
                         :class="`p-4 rounded-lg shadow-lg border-l-4 ${
                             notification.type === 'success' ? 'bg-green-50 border-green-500 text-green-800' :
                             notification.type === 'error' ? 'bg-red-50 border-red-500 text-red-800' :
                             'bg-blue-50 border-blue-500 text-blue-800'
                         }`">
                        <div class="flex items-center justify-between">
                            <span>{{ notification.message }}</span>
                            <button @click="dismissNotification(notification.id)" class="ml-4 text-gray-500 hover:text-gray-700">
                                ✕
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Welcome Section -->
                <CustomerWelcome />

                <!-- Flash Sale Banner -->
                <FlashSaleBanner />

                <!-- Enhanced navigation tabs -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md border border-gray-200 dark:border-gray-700 p-2 mb-8 mt-4">
                    <div class="flex space-x-2">
                        <button v-for="tab in ['overview', 'products', 'orders', 'events']" :key="tab"
                                @click="activeTab = tab"
                                :class="`flex-1 px-4 py-2 rounded-lg font-medium transition-all ${
                                    activeTab === tab 
                                        ? 'bg-blue-500 text-white shadow-lg' 
                                        : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700'
                                }`">
                            {{ tab.charAt(0).toUpperCase() + tab.slice(1) }}
                        </button>
                    </div>
                </div>

                <!-- Overview Tab Content -->
                <div v-if="activeTab === 'overview'" class="space-y-8">
                    <!-- Enhanced Featured Stores -->
                    <FeaturedStores />

                    <!-- AI Recommendations -->
                    <AIRecommendations />

                    <!-- Quick Actions -->
                    <QuickActions />

                    <!-- Recent Orders -->
                    <div class="space-y-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-2xl font-bold text-gray-900 dark:text-white">{{ t('dashboard.recent_orders') }}</h3>
                                <p class="text-gray-600 dark:text-gray-300 mt-1">{{ t('dashboard.track_your_purchases') }}</p>
                            </div>
                            <button class="text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300 font-medium flex items-center space-x-1">
                                <span>{{ t('dashboard.view_all') }}</span>
                                <span>→</span>
                            </button>
                        </div>
                        
                        <div class="space-y-4">
                            <div v-for="order in recentOrders" :key="order.id" 
                                 class="bg-white dark:bg-gray-800 rounded-xl shadow-md border border-gray-100 dark:border-gray-700 p-6">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-4">
                                        <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900 rounded-lg flex items-center justify-center">
                                            📦
                                        </div>
                                        <div>
                                            <h4 class="font-semibold text-gray-900 dark:text-white">{{ order.id }}</h4>
                                            <p class="text-sm text-gray-600 dark:text-gray-300">{{ order.store }} • {{ order.date }}</p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <div class="text-lg font-bold text-gray-900 dark:text-white">${{ order.total }}</div>
                                        <div class="text-sm text-gray-600 dark:text-gray-300">{{ order.items }} {{ t('dashboard.items') }}</div>
                                    </div>
                                </div>
                                
                                <!-- Order Progress -->
                                <div class="mt-4">
                                    <div class="flex items-center justify-between text-sm mb-2">
                                        <span class="text-gray-600 dark:text-gray-300">{{ t('dashboard.order_status') }}</span>
                                        <span class="font-medium text-gray-900 dark:text-white capitalize">{{ order.status }}</span>
                                    </div>
                                    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                        <div class="bg-blue-600 h-2 rounded-full transition-all duration-500" 
                                             :style="{ width: `${order.progress}%` }"></div>
                                    </div>
                                    <div v-if="order.tracking" class="mt-2 text-sm text-blue-600 dark:text-blue-400">
                                        {{ t('dashboard.tracking') }}: {{ order.tracking }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Live Stats -->
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md border border-gray-100 dark:border-gray-700 p-6">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm text-gray-600 dark:text-gray-300">{{ t('dashboard.active_users') }}</p>
                                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ liveStats.activeUsers.toLocaleString() }}</p>
                                </div>
                                <div class="w-12 h-12 bg-green-100 dark:bg-green-900 rounded-lg flex items-center justify-center">
                                    👥
                                </div>
                            </div>
                        </div>
                        
                        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md border border-gray-100 dark:border-gray-700 p-6">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm text-gray-600 dark:text-gray-300">{{ t('dashboard.today_sales') }}</p>
                                    <p class="text-2xl font-bold text-gray-900 dark:text-white">${{ liveStats.todaySales.toLocaleString() }}</p>
                                </div>
                                <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900 rounded-lg flex items-center justify-center">
                                    💰
                                </div>
                            </div>
                        </div>
                        
                        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md border border-gray-100 dark:border-gray-700 p-6">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm text-gray-600 dark:text-gray-300">{{ t('dashboard.new_products') }}</p>
                                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ liveStats.newProducts }}</p>
                                </div>
                                <div class="w-12 h-12 bg-purple-100 dark:bg-purple-900 rounded-lg flex items-center justify-center">
                                    🆕
                                </div>
                            </div>
                        </div>
                        
                        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md border border-gray-100 dark:border-gray-700 p-6">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm text-gray-600 dark:text-gray-300">{{ t('dashboard.flash_sale_ends') }}</p>
                                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ liveStats.flashSaleEnds }}</p>
                                </div>
                                <div class="w-12 h-12 bg-red-100 dark:bg-red-900 rounded-lg flex items-center justify-center">
                                    🔥
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Products Tab Content -->
                <div v-if="activeTab === 'products'" class="space-y-6">
                    <!-- Search and Filters -->
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md border border-gray-100 dark:border-gray-700 p-6">
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                            <div class="md:col-span-2">
                                <input v-model="searchQuery" 
                                       type="text" 
                                       :placeholder="t('dashboard.search_products')"
                                       class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
                            </div>
                            <select v-model="selectedCategory" 
                                    class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
                                <option value="all">{{ t('dashboard.all_categories') }}</option>
                                <option value="electronics">{{ t('dashboard.electronics') }}</option>
                                <option value="fashion">{{ t('dashboard.fashion') }}</option>
                                <option value="home">{{ t('dashboard.home') }}</option>
                                <option value="sports">{{ t('dashboard.sports') }}</option>
                            </select>
                            <select v-model="sortBy" 
                                    class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
                                <option value="recommended">{{ t('dashboard.recommended') }}</option>
                                <option value="price_low">{{ t('dashboard.price_low_high') }}</option>
                                <option value="price_high">{{ t('dashboard.price_high_low') }}</option>
                                <option value="rating">{{ t('dashboard.top_rated') }}</option>
                            </select>
                        </div>
                    </div>

                    <!-- Products Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-6">
                        <!-- Product cards would go here -->
                        <div v-for="i in 8" :key="i" 
                             class="bg-white dark:bg-gray-800 rounded-xl shadow-md border border-gray-100 dark:border-gray-700 p-6">
                            <div class="w-full h-48 bg-gray-200 dark:bg-gray-700 rounded-lg mb-4"></div>
                            <h4 class="font-semibold text-gray-900 dark:text-white mb-2">{{ t('dashboard.product_name') }} {{ i }}</h4>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white mb-4">${{ (Math.random() * 100 + 20).toFixed(2) }}</p>
                            <button class="w-full bg-blue-600 hover:bg-blue-700 text-white py-2 rounded-lg font-medium transition-colors">
                                {{ t('dashboard.add_to_cart') }}
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Orders Tab Content -->
                <div v-if="activeTab === 'orders'" class="space-y-6">
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md border border-gray-100 dark:border-gray-700 p-6">
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-4">{{ t('dashboard.order_history') }}</h3>
                        <div class="space-y-4">
                            <div v-for="order in recentOrders" :key="order.id" 
                                 class="border-b border-gray-200 dark:border-gray-700 pb-4 last:border-0">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <h4 class="font-semibold text-gray-900 dark:text-white">{{ order.id }}</h4>
                                        <p class="text-sm text-gray-600 dark:text-gray-300">{{ order.date }} • {{ order.store }}</p>
                                    </div>
                                    <div class="text-right">
                                        <div class="text-lg font-bold text-gray-900 dark:text-white">${{ order.total }}</div>
                                        <div class="text-sm text-gray-600 dark:text-gray-300">{{ order.items }} {{ t('dashboard.items') }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Events Tab Content -->
                <div v-if="activeTab === 'events'" class="space-y-6">
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md border border-gray-100 dark:border-gray-700 p-6">
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-4">{{ t('dashboard.upcoming_events') }}</h3>
                        <div class="space-y-4">
                            <div v-for="event in upcomingEvents" :key="event.id" 
                                 class="border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <h4 class="font-semibold text-gray-900 dark:text-white">{{ event.title }}</h4>
                                        <p class="text-sm text-gray-600 dark:text-gray-300">{{ event.date }} • {{ event.time }}</p>
                                        <p class="text-sm text-gray-600 dark:text-gray-300 mt-1">{{ event.description }}</p>
                                        <div class="flex items-center space-x-4 mt-2">
                                            <span class="text-sm text-gray-600 dark:text-gray-300">
                                                {{ event.attendees }}/{{ event.maxAttendees }} {{ t('dashboard.attendees') }}
                                            </span>
                                            <span class="px-2 py-1 bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 text-xs rounded-full">
                                                {{ event.type }}
                                            </span>
                                        </div>
                                    </div>
                                    <button @click="toggleEventRegistration(event)"
                                            :class="`px-4 py-2 rounded-lg font-medium transition-colors ${
                                                event.registered 
                                                    ? 'bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-200 hover:bg-gray-300 dark:hover:bg-gray-600' 
                                                    : 'bg-blue-600 hover:bg-blue-700 text-white'
                                            }`">
                                        {{ event.registered ? t('dashboard.unregister') : t('dashboard.register') }}
                                    </button>
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
/* Custom scrollbar */
::-webkit-scrollbar {
    width: 8px;
}

::-webkit-scrollbar-track {
    background: #f1f1f1;
}

.dark ::-webkit-scrollbar-track {
    background: #374151;
}

::-webkit-scrollbar-thumb {
    background: #888;
    border-radius: 4px;
}

::-webkit-scrollbar-thumb:hover {
    background: #555;
}

/* Animations */
@keyframes slideIn {
    from {
        transform: translateX(100%);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}

.animate-slide-in {
    animation: slideIn 0.3s ease-out;
}

/* Gradient animation */
@keyframes gradient {
    0% {
        background-position: 0% 50%;
    }
    50% {
        background-position: 100% 50%;
    }
    100% {
        background-position: 0% 50%;
    }
}

.animate-gradient {
    background-size: 200% 200%;
    animation: gradient 3s ease infinite;
}
</style>
