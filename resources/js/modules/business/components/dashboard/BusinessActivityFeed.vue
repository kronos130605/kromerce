<script setup>
import { computed } from 'vue';
import { useI18n } from 'vue-i18n';
import { Link } from '@inertiajs/vue3';

const { t } = useI18n();

const props = defineProps({
    activities: {
        type: Array,
        default: () => []
    },
    loading: {
        type: Boolean,
        default: false
    }
});

// Group activities by date
const groupedActivities = computed(() => {
    const grouped = {};
    
    props.activities.forEach(activity => {
        const date = new Date(activity.timestamp).toLocaleDateString('es-ES', {
            weekday: 'long',
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        });
        
        if (!grouped[date]) {
            grouped[date] = [];
        }
        grouped[date].push(activity);
    });
    
    return grouped;
});

const getActivityIcon = (type) => {
    const icons = {
        order: '🛒',
        product: '📦',
        customer: '👤',
        payment: '💰',
        review: '⭐',
        settings: '⚙️',
        marketing: '📢',
        user: '👥'
    };
    return icons[type] || '📌';
};

const getActivityColor = (type) => {
    const colors = {
        order: 'bg-blue-100 text-blue-600 dark:bg-blue-900 dark:text-blue-300',
        product: 'bg-emerald-100 text-emerald-600 dark:bg-emerald-900 dark:text-emerald-300',
        customer: 'bg-purple-100 text-purple-600 dark:bg-purple-900 dark:text-purple-300',
        payment: 'bg-amber-100 text-amber-600 dark:bg-amber-900 dark:text-amber-300',
        review: 'bg-yellow-100 text-yellow-600 dark:bg-yellow-900 dark:text-yellow-300',
        settings: 'bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-300',
        marketing: 'bg-pink-100 text-pink-600 dark:bg-pink-900 dark:text-pink-300',
        user: 'bg-indigo-100 text-indigo-600 dark:bg-indigo-900 dark:text-indigo-300'
    };
    return colors[type] || 'bg-gray-100 text-gray-600';
};

const formatTime = (timestamp) => {
    const date = new Date(timestamp);
    const now = new Date();
    const diffMinutes = Math.floor((now - date) / (1000 * 60));
    const diffHours = Math.floor(diffMinutes / 60);
    const diffDays = Math.floor(diffHours / 24);
    
    if (diffMinutes < 1) return t('dashboard.just_now');
    if (diffMinutes < 60) return `${diffMinutes}m`;
    if (diffHours < 24) return `${diffHours}h`;
    if (diffDays < 7) return `${diffDays}d`;
    
    return date.toLocaleDateString('es-ES', { month: 'short', day: 'numeric' });
};

const skeletonArray = Array(5).fill(null);
</script>

<template>
    <div class="space-y-6 mt-8">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-xl font-bold text-gray-900 dark:text-white">{{ t('dashboard.recent_activity') }}</h3>
                <p class="text-gray-600 dark:text-gray-400 text-sm">{{ t('dashboard.activity_timeline') }}</p>
            </div>
            <div class="flex items-center space-x-2">
                <button class="p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Loading State -->
        <div v-if="loading" class="space-y-4">
            <div v-for="(n, index) in skeletonArray" :key="index" class="flex items-start space-x-4 animate-pulse">
                <div class="w-10 h-10 bg-gray-200 dark:bg-gray-700 rounded-full flex-shrink-0"></div>
                <div class="flex-1 space-y-2 py-2">
                    <div class="w-48 h-4 bg-gray-200 dark:bg-gray-700 rounded"></div>
                    <div class="w-32 h-3 bg-gray-200 dark:bg-gray-700 rounded"></div>
                </div>
            </div>
        </div>

        <!-- Empty State -->
        <div v-else-if="activities.length === 0" class="text-center py-8">
            <div class="text-4xl mb-2">📝</div>
            <p class="text-gray-500 dark:text-gray-400">{{ t('dashboard.no_activity') }}</p>
        </div>

        <!-- Timeline -->
        <div v-else class="space-y-6">
            <div v-for="(dayActivities, date) in groupedActivities" :key="date" class="relative">
                <!-- Date Header -->
                <div class="sticky top-0 bg-gray-50 dark:bg-gray-900 z-10 py-2 mb-4">
                    <h4 class="text-sm font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wider">{{ date }}</h4>
                </div>

                <!-- Activity Items -->
                <div class="space-y-4 relative">
                    <!-- Timeline Line -->
                    <div class="absolute left-5 top-0 bottom-0 w-0.5 bg-gray-200 dark:bg-gray-700"></div>

                    <div v-for="activity in dayActivities" :key="activity.id" class="relative flex items-start space-x-4 group">
                        <!-- Icon -->
                        <div :class="`relative z-10 w-10 h-10 rounded-full flex items-center justify-center flex-shrink-0 ${getActivityColor(activity.type)}`">
                            <span class="text-lg">{{ getActivityIcon(activity.type) }}</span>
                        </div>

                        <!-- Content -->
                        <div class="flex-1 min-w-0 pb-4">
                            <div class="flex items-start justify-between">
                                <div>
                                    <p class="text-sm font-medium text-gray-900 dark:text-white">
                                        <span class="font-semibold">{{ activity.user }}</span>
                                        {{ activity.action }}
                                        <Link v-if="activity.targetLink" :href="activity.targetLink" class="text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300 hover:underline">
                                            {{ activity.target }}
                                        </Link>
                                        <span v-else class="font-medium">{{ activity.target }}</span>
                                    </p>
                                    <p v-if="activity.details" class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                        {{ activity.details }}
                                    </p>
                                </div>
                                <span class="text-xs text-gray-400 dark:text-gray-500 flex-shrink-0 ml-4">
                                    {{ formatTime(activity.timestamp) }}
                                </span>
                            </div>

                            <!-- Metadata Tags -->
                            <div v-if="activity.metadata" class="flex items-center space-x-2 mt-2">
                                <span v-for="(value, key) in activity.metadata" :key="key" 
                                      class="inline-flex items-center px-2 py-1 text-xs font-medium bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400 rounded">
                                    {{ value }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
