<script setup>
import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import { useAuth } from '@/composables/useAuth.js';

const page = usePage();
import { Link } from '@inertiajs/vue3';

const { t } = useI18n();
const { user, currentStore } = useAuth();

// Props for real data from backend
const props = defineProps({
    stats: {
        type: Object,
        default: () => ({
            revenue: 0,
            orders: 0,
            products: 0,
            customers: 0,
            growth: 0,
            conversionRate: 0
        })
    },
    alerts: {
        type: Array,
        default: () => []
    }
});

// Smart greeting based on time of day
const greeting = computed(() => {
    const hour = new Date().getHours();
    if (hour < 12) return t('dashboard.good_morning');
    if (hour < 18) return t('dashboard.good_afternoon');
    return t('dashboard.good_evening');
});

// Format currency
const formatCurrency = (value) => {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD',
        minimumFractionDigits: 0,
        maximumFractionDigits: 0
    }).format(value);
};

// Alert icons by type
const getAlertIcon = (type) => {
    const icons = {
        warning: '⚠️',
        danger: '🚨',
        info: 'ℹ️',
        success: '✅'
    };
    return icons[type] || 'ℹ️';
};

// Alert colors by type
const getAlertColor = (type) => {
    const colors = {
        warning: 'bg-yellow-500/20 border-yellow-400 text-yellow-100',
        danger: 'bg-red-500/20 border-red-400 text-red-100',
        info: 'bg-blue-500/20 border-blue-400 text-blue-100',
        success: 'bg-green-500/20 border-green-400 text-green-100'
    };
    return colors[type] || colors.info;
};
</script>

<template>
    <div class="bg-gradient-to-br from-blue-600 to-emerald-600 dark:from-blue-800 dark:to-emerald-800 rounded-2xl p-8 text-white shadow-xl">
        <div class="flex items-center justify-between">
            <div class="flex-1">
                <div class="flex items-center space-x-3 mb-4">
                    <div class="w-3 h-3 bg-green-400 rounded-full animate-pulse"></div>
                    <h1 class="text-4xl font-bold">
                        {{ greeting }}, {{ user.name }}! 🚀
                    </h1>
                </div>
                <p class="text-blue-100 text-lg mb-6">
                    {{ t('dashboard.business_subtitle') }}
                </p>

                <!-- Business Info -->
                <div v-if="currentStore" class="flex items-center space-x-4">
                    <div class="bg-white/20 backdrop-blur-sm rounded-xl px-4 py-2">
                        <span class="text-blue-100 text-sm">{{ t('dashboard.store') }}:</span>
                        <span class="font-semibold ml-2">{{ currentStore.name }}</span>
                    </div>
                    <div class="bg-white/20 backdrop-blur-sm rounded-xl px-4 py-2">
                        <span class="text-blue-100 text-sm">{{ t('dashboard.domain') }}:</span>
                        <span class="font-mono ml-2">{{ currentStore.slug }}.kromerce.test</span>
                    </div>
                </div>
            </div>

            <!-- Key Metrics -->
            <div class="text-right">
                <div class="bg-white/20 backdrop-blur-sm rounded-xl p-6">
                    <div class="text-sm text-blue-100 mb-2">{{ t('dashboard.monthly_revenue') }}</div>
                    <div class="text-3xl font-bold">{{ formatCurrency(props.stats.revenue) }}</div>
                    <div class="text-xs text-emerald-200 mt-2 flex items-center justify-end space-x-1">
                        <span>↑</span>
                        <span>{{ props.stats.growth }}% {{ t('dashboard.growth') }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Contextual Alerts -->
        <div v-if="props.alerts.length > 0" class="mt-6 space-y-2">
            <Link 
                v-for="alert in props.alerts" 
                :key="alert.id"
                :href="alert.action"
                class="flex items-center space-x-3 p-3 rounded-xl border backdrop-blur-sm hover:opacity-90 transition-opacity"
                :class="getAlertColor(alert.type)"
            >
                <span class="text-xl">{{ getAlertIcon(alert.type) }}</span>
                <span class="font-medium">{{ alert.message }}</span>
                <span class="ml-auto text-sm opacity-75">{{ t('dashboard.take_action') }} →</span>
            </Link>
        </div>

        <!-- Quick Stats Grid -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-6">
            <div class="bg-white/10 backdrop-blur-sm rounded-lg p-4 text-center border border-white/20 hover:bg-white/20 transition-colors">
                <div class="text-2xl font-bold">{{ props.stats.orders.toLocaleString() }}</div>
                <div class="text-xs text-blue-100 mt-1">{{ t('dashboard.orders') }}</div>
            </div>
            <div class="bg-white/10 backdrop-blur-sm rounded-lg p-4 text-center border border-white/20 hover:bg-white/20 transition-colors">
                <div class="text-2xl font-bold">{{ props.stats.products }}</div>
                <div class="text-xs text-blue-100 mt-1">{{ t('common.products') }}</div>
            </div>
            <div class="bg-white/10 backdrop-blur-sm rounded-lg p-4 text-center border border-white/20 hover:bg-white/20 transition-colors">
                <div class="text-2xl font-bold">{{ props.stats.customers.toLocaleString() }}</div>
                <div class="text-xs text-blue-100 mt-1">{{ t('dashboard.customers') }}</div>
            </div>
            <div class="bg-white/10 backdrop-blur-sm rounded-lg p-4 text-center border border-white/20 hover:bg-white/20 transition-colors">
                <div class="text-2xl font-bold">{{ props.stats.conversionRate }}%</div>
                <div class="text-xs text-blue-100 mt-1">{{ t('dashboard.conversion_rate') }}</div>
            </div>
        </div>
    </div>
</template>
