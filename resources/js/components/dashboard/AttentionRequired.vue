<script setup>
import { computed } from 'vue';
import { useI18n } from 'vue-i18n';
import { Link } from '@inertiajs/vue3';

const { t } = useI18n();

const props = defineProps({
    alerts: {
        type: Array,
        default: () => []
    }
});

// Group alerts by category
const alertsByCategory = computed(() => {
    const grouped = {
        urgent: [],
        warning: [],
        info: []
    };
    
    props.alerts.forEach(alert => {
        if (grouped[alert.type]) {
            grouped[alert.type].push(alert);
        }
    });
    
    return grouped;
});

const getAlertIcon = (type) => {
    const icons = {
        urgent: '🚨',
        warning: '⚠️',
        info: 'ℹ️'
    };
    return icons[type] || 'ℹ️';
};

const getAlertColor = (type) => {
    const colors = {
        urgent: 'bg-red-50 border-red-200 text-red-800 dark:bg-red-900/20 dark:border-red-800 dark:text-red-200',
        warning: 'bg-yellow-50 border-yellow-200 text-yellow-800 dark:bg-yellow-900/20 dark:border-yellow-800 dark:text-yellow-200',
        info: 'bg-blue-50 border-blue-200 text-blue-800 dark:bg-blue-900/20 dark:border-blue-800 dark:text-blue-200'
    };
    return colors[type] || colors.info;
};

const getAlertBadge = (type) => {
    const badges = {
        urgent: { text: t('dashboard.urgent_action'), color: 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' },
        warning: { text: t('dashboard.attention_needed'), color: 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200' },
        info: { text: t('dashboard.information'), color: 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200' }
    };
    return badges[type] || badges.info;
};

const totalAlerts = computed(() => props.alerts.length);
const hasUrgentAlerts = computed(() => alertsByCategory.value.urgent.length > 0);
</script>

<template>
    <div class="space-y-6 mt-8">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <div :class="`w-3 h-3 rounded-full animate-pulse ${hasUrgentAlerts ? 'bg-red-500' : 'bg-yellow-500'}`"></div>
                <div>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white">
                        {{ t('dashboard.attention_required') }}
                        <span v-if="totalAlerts > 0" class="ml-2 px-2 py-0.5 text-sm bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200 rounded-full">
                            {{ totalAlerts }}
                        </span>
                    </h3>
                    <p class="text-gray-600 dark:text-gray-400 text-sm">{{ t('dashboard.items_need_action') }}</p>
                </div>
            </div>
        </div>

        <!-- Empty State -->
        <div v-if="totalAlerts === 0" class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-xl p-6 text-center">
            <div class="text-4xl mb-2">✅</div>
            <h4 class="font-medium text-green-800 dark:text-green-200">{{ t('dashboard.all_good') }}</h4>
            <p class="text-sm text-green-600 dark:text-green-300 mt-1">{{ t('dashboard.no_alerts') }}</p>
        </div>

        <!-- Alerts List -->
        <div v-else class="space-y-3">
            <!-- Urgent Alerts -->
            <div v-for="alert in alertsByCategory.urgent" :key="alert.id"
                 class="group relative flex items-start space-x-3 p-4 rounded-xl border-2 transition-all hover:shadow-md cursor-pointer"
                 :class="getAlertColor(alert.type)">
                <div class="flex-shrink-0 w-10 h-10 bg-white dark:bg-gray-800 rounded-lg flex items-center justify-center text-xl shadow-sm">
                    {{ getAlertIcon(alert.type) }}
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center space-x-2 mb-1">
                        <span :class="`px-2 py-0.5 text-xs font-medium rounded-full ${getAlertBadge(alert.type).color}`">
                            {{ getAlertBadge(alert.type).text }}
                        </span>
                        <span class="text-xs opacity-75">{{ alert.timeAgo }}</span>
                    </div>
                    <h4 class="font-medium">{{ alert.title }}</h4>
                    <p class="text-sm opacity-80 mt-1">{{ alert.description }}</p>
                    
                    <div class="flex items-center space-x-3 mt-3">
                        <Link v-if="alert.actionLink" :href="alert.actionLink"
                              class="inline-flex items-center text-sm font-medium hover:underline">
                            {{ alert.actionText || t('dashboard.take_action') }}
                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </Link>
                        <button v-if="alert.onDismiss" @click.stop="alert.onDismiss" 
                                class="text-sm opacity-60 hover:opacity-100">
                            {{ t('dashboard.dismiss') }}
                        </button>
                    </div>
                </div>
            </div>

            <!-- Warning Alerts -->
            <div v-for="alert in alertsByCategory.warning" :key="alert.id"
                 class="group relative flex items-start space-x-3 p-4 rounded-xl border transition-all hover:shadow-md cursor-pointer"
                 :class="getAlertColor(alert.type)">
                <div class="flex-shrink-0 w-10 h-10 bg-white dark:bg-gray-800 rounded-lg flex items-center justify-center text-xl shadow-sm">
                    {{ getAlertIcon(alert.type) }}
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center space-x-2 mb-1">
                        <span :class="`px-2 py-0.5 text-xs font-medium rounded-full ${getAlertBadge(alert.type).color}`">
                            {{ getAlertBadge(alert.type).text }}
                        </span>
                        <span class="text-xs opacity-75">{{ alert.timeAgo }}</span>
                    </div>
                    <h4 class="font-medium">{{ alert.title }}</h4>
                    <p class="text-sm opacity-80 mt-1">{{ alert.description }}</p>
                    
                    <div class="flex items-center space-x-3 mt-3">
                        <Link v-if="alert.actionLink" :href="alert.actionLink"
                              class="inline-flex items-center text-sm font-medium hover:underline">
                            {{ alert.actionText || t('dashboard.take_action') }}
                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </Link>
                        <button v-if="alert.onDismiss" @click.stop="alert.onDismiss" 
                                class="text-sm opacity-60 hover:opacity-100">
                            {{ t('dashboard.dismiss') }}
                        </button>
                    </div>
                </div>
            </div>

            <!-- Info Alerts -->
            <div v-for="alert in alertsByCategory.info" :key="alert.id"
                 class="group relative flex items-start space-x-3 p-4 rounded-xl border transition-all hover:shadow-md cursor-pointer"
                 :class="getAlertColor(alert.type)">
                <div class="flex-shrink-0 w-10 h-10 bg-white dark:bg-gray-800 rounded-lg flex items-center justify-center text-xl shadow-sm">
                    {{ getAlertIcon(alert.type) }}
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center space-x-2 mb-1">
                        <span :class="`px-2 py-0.5 text-xs font-medium rounded-full ${getAlertBadge(alert.type).color}`">
                            {{ getAlertBadge(alert.type).text }}
                        </span>
                        <span class="text-xs opacity-75">{{ alert.timeAgo }}</span>
                    </div>
                    <h4 class="font-medium">{{ alert.title }}</h4>
                    <p class="text-sm opacity-80 mt-1">{{ alert.description }}</p>
                    
                    <div class="flex items-center space-x-3 mt-3">
                        <Link v-if="alert.actionLink" :href="alert.actionLink"
                              class="inline-flex items-center text-sm font-medium hover:underline">
                            {{ alert.actionText || t('dashboard.take_action') }}
                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </Link>
                        <button v-if="alert.onDismiss" @click.stop="alert.onDismiss" 
                                class="text-sm opacity-60 hover:opacity-100">
                            {{ t('dashboard.dismiss') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
