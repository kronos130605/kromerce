<script setup>
import { ref, onMounted } from 'vue';
import { useI18n } from 'vue-i18n';

// Import dashboard components
import CustomerWelcome from '@/modules/customer/components/dashboard/CustomerWelcome.vue';
import CustomerFlashSaleBanner from '@/modules/customer/components/dashboard/CustomerFlashSaleBanner.vue';
import CustomerFeaturedStores from '@/modules/customer/components/dashboard/CustomerFeaturedStores.vue';
import CustomerAiRecommendations from '@/modules/customer/components/dashboard/CustomerAIRecommendations.vue';
import CustomerQuickActions from '@/modules/customer/components/dashboard/CustomerQuickActions.vue';
import { useAuth } from '@/composables/useAuth';

const { user } = useAuth();
const { t } = useI18n();

// State management
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
        items: 4,
        store: 'FashionHub',
        tracking: 'TRK456789123',
        progress: 45
    }
]);

// Upcoming events
const upcomingEvents = ref([
    {
        id: 1,
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
        id: 2,
        title: 'Fashion Week Exclusive',
        date: '2024-01-30',
        time: '6:00 PM',
        type: 'fashion',
        attendees: 892,
        maxAttendees: 1000,
        registered: true,
        description: 'Exclusive preview of new collections'
    },
    {
        id: 3,
        title: 'Tech Innovation Day',
        date: '2024-02-05',
        time: '3:00 PM',
        type: 'tech',
        attendees: 234,
        maxAttendees: 500,
        registered: false,
        description: 'Discover latest tech innovations'
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
        showNotification(t('dashboard.successfully_registered', { event: event.title }), 'success');
    } else {
        event.attendees--;
        showNotification(t('dashboard.unregistered_successfully', { event: event.title }), 'info');
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
    <div class="space-y-8">
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

        <div>
            <!-- Welcome Section -->
            <CustomerWelcome :user="userProfile" :currentTime="currentTime" />

            <!-- Flash Sale Banner -->
            <CustomerFlashSaleBanner :endTime="liveStats.flashSaleEnds" />

            <!-- Live Stats -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <div class="flex items-center">
                        <div class="p-3 bg-blue-100 dark:bg-blue-900 rounded-lg">
                            <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3m0 0l6-6m6 6v6m0-6v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6m2 2 0 012 2h10a2 2 0 012-2 2v-6a2 2 0 01-2-2" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Active Users</p>
                            <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ liveStats.activeUsers.toLocaleString() }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <div class="flex items-center">
                        <div class="p-3 bg-green-100 dark:bg-green-900 rounded-lg">
                            <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 3 3 3 .895 0 3-2 1.343-3-3-3m0 8c1.657 0 3-.895 3-2s-1.343-3-3-3m0-8c-1.657 0-3 .895-3 2s1.343 3 3 3 .895 0 3-2 1.343-3-3-3m0-8c-1.657 0-3 .895-3 2s1.343 3 3 3 .895 0 3-2 1.343-3-3-3" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Today's Sales</p>
                            <p class="text-2xl font-semibold text-gray-900 dark:text-white">${{ liveStats.todaySales.toLocaleString() }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <div class="flex items-center">
                        <div class="p-3 bg-purple-100 dark:bg-purple-900 rounded-lg">
                            <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4M2 17l6-6m4 6l6-6m2 10H4m16 0v-4a2 2 0 00-2-2H6a2 2 0 00-2 2v4" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">New Products</p>
                            <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ liveStats.newProducts }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <div class="flex items-center">
                        <div class="p-3 bg-orange-100 dark:bg-orange-900 rounded-lg">
                            <svg class="w-6 h-6 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3v-4m0 4v-4m0 4v-4m-6 6h6M6 9l6 6m6-6v6" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Flash Sale Ends</p>
                            <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ liveStats.flashSaleEnds }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <CustomerQuickActions :cartItems="cartItems" :wishlistItems="wishlistItems" />

            <!-- Featured Stores -->
            <CustomerFeaturedStores />

            <!-- AI Recommendations -->
            <CustomerAiRecommendations />

            <!-- Recent Orders -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Recent Orders</h3>
                <div class="space-y-4">
                    <div v-for="order in recentOrders" :key="order.id" class="border-b border-gray-200 dark:border-gray-700 pb-4 last:border-0">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="font-medium text-gray-900 dark:text-white">{{ t('dashboard.order_id') }}: {{ order.id }}</p>
                                <p class="text-sm text-gray-600 dark:text-gray-400">{{ order.date }}</p>
                            </div>
                            <div class="text-right">
                                <p class="font-semibold text-gray-900 dark:text-white">${{ order.total.toFixed(2) }}</p>
                                <span :class="`px-2 py-1 text-xs rounded-full ${
                                    order.status === 'delivered' ? 'bg-green-100 text-green-800' :
                                    order.status === 'shipped' ? 'bg-blue-100 text-blue-800' :
                                    order.status === 'processing' ? 'bg-yellow-100 text-yellow-800' :
                                    'bg-gray-100 text-gray-800'
                                }`">
                                    {{ order.status }}
                                </span>
                            </div>
                        </div>
                        <div v-if="order.tracking" class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                            {{ t('dashboard.tracking') }}: {{ order.tracking }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Upcoming Events -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Upcoming Events</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <div v-for="event in upcomingEvents" :key="event.id"
                         class="border border-gray-200 dark:border-gray-700 rounded-lg p-4 hover:shadow-md transition-shadow">
                        <div class="flex items-center justify-between mb-2">
                            <h4 class="font-medium text-gray-900 dark:text-white">{{ event.title }}</h4>
                            <span :class="`px-2 py-1 text-xs rounded-full ${
                                event.registered ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'
                            }`">
                                {{ event.registered ? 'Registered' : 'Available' }}
                            </span>
                        </div>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">{{ event.description }}</p>
                        <div class="flex items-center justify-between text-sm">
                            <span>{{ event.date }} at {{ event.time }}</span>
                            <span>{{ event.attendees }}/{{ event.maxAttendees }} attending</span>
                        </div>
                        <button @click="toggleEventRegistration(event)"
                                :class="`w-full mt-2 px-4 py-2 rounded-lg text-sm font-medium ${
                                    event.registered
                                    ? 'bg-gray-200 text-gray-800 hover:bg-gray-300'
                                    : 'bg-blue-600 text-white hover:bg-blue-700'
                                }`">
                            {{ event.registered ? 'Unregister' : 'Register' }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
/* Scrollbar styling */
::-webkit-scrollbar {
    width: 8px;
}

::-webkit-scrollbar-track {
    background: #f1f1f1;
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

/* Gradient animation */
@keyframes gradient {
    0% {
        background-position: 0 50%;
    }
    50% {
        background-position: 100% 50%;
    }
    100% {
        background-position: 0 50%;
    }
}
</style>
