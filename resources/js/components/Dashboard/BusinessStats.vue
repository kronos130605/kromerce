<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { useI18n } from 'vue-i18n';

const { t } = useI18n();

// Reactive dark mode detection
const isDarkMode = ref(false);

// Check dark mode
const checkDarkMode = () => {
    isDarkMode.value = document.documentElement.classList.contains('dark');
};

// Get dynamic background color based on mode
const getBgColor = (stat) => {
    return isDarkMode.value ? stat.bgColorDark : stat.bgColor;
};

// Setup dark mode listener
onMounted(() => {
    checkDarkMode(); // Initial check
    
    // Listen for class changes on document.documentElement
    const observer = new MutationObserver(() => {
        checkDarkMode();
    });
    
    observer.observe(document.documentElement, {
        attributes: true,
        attributeFilter: ['class']
    });
    
    // Store observer for cleanup
    window._darkModeObserver = observer;
});

onUnmounted(() => {
    if (window._darkModeObserver) {
        window._darkModeObserver.disconnect();
        delete window._darkModeObserver;
    }
});

// Business metrics
const stats = ref([
    {
        title: 'dashboard.total_revenue',
        value: 125430,
        change: 23.5,
        changeType: 'positive',
        icon: '💰',
        color: 'from-blue-500 to-emerald-600',
        bgColor: 'from-blue-50 to-emerald-50',
        bgColorDark: 'from-blue-900 to-emerald-900',
        previous: 101000
    },
    {
        title: 'dashboard.total_orders',
        value: 3421,
        change: 18.2,
        changeType: 'positive',
        icon: '📦',
        color: 'from-emerald-500 to-teal-600',
        bgColor: 'from-emerald-50 to-teal-50',
        bgColorDark: 'from-emerald-900 to-teal-900',
        previous: 2894
    },
    {
        title: 'dashboard.products',
        value: 156,
        change: 12.1,
        changeType: 'positive',
        icon: '🎯',
        color: 'from-indigo-500 to-purple-600',
        bgColor: 'from-indigo-50 to-purple-50',
        bgColorDark: 'from-indigo-900 to-purple-900',
        previous: 139
    },
    {
        title: 'dashboard.customers',
        value: 1289,
        change: 8.4,
        changeType: 'positive',
        icon: '👥',
        color: 'from-amber-500 to-orange-600',
        bgColor: 'from-amber-50 to-orange-50',
        bgColorDark: 'from-amber-900 to-orange-900',
        previous: 1188
    }
]);

const formatValue = (value, title) => {
    if (title === 'dashboard.total_revenue') {
        return `$${value.toLocaleString()}`;
    }
    return value.toLocaleString();
};

const getChangeText = (stat) => {
    const changeValue = Math.abs(stat.change);
    if (stat.title === 'dashboard.total_revenue') {
        return `↑ $${((stat.value - stat.previous)).toLocaleString()} ${t('dashboard.from_last_month')}`;
    }
    return `↑ ${Math.round((stat.value - stat.previous))} ${t('dashboard.from_last_month')}`;
};
</script>

<template>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div v-for="stat in stats" :key="stat.title"
             class="group relative bg-white dark:bg-gray-900 rounded-2xl shadow-lg border border-gray-100 dark:border-gray-700 p-6 hover:shadow-xl transition-all duration-300 hover:-translate-y-1">
            <!-- Background gradient on hover -->
            <div :class="`absolute inset-0 bg-gradient-to-br ${getBgColor(stat)} rounded-2xl opacity-0 group-hover:opacity-100 transition-opacity -z-10`"></div>
            
            <div class="relative z-20">
                <!-- Header -->
                <div class="flex items-center justify-between mb-4">
                    <div :class="`w-12 h-12 bg-gradient-to-br ${stat.color} rounded-xl flex items-center justify-center shadow-lg`">
                        <span class="text-2xl">{{ stat.icon }}</span>
                    </div>
                    <span :class="`text-xs font-medium ${stat.color.split(' ')[0].replace('from-', '')}600 bg-${stat.color.split(' ')[0].replace('from-', '')}50 px-2 py-1 rounded-full`">
                        {{ stat.change > 0 ? '+' : '' }}{{ stat.change }}%
                    </span>
                </div>
                
                <!-- Content -->
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">{{ t(stat.title) }}</p>
                    <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ formatValue(stat.value, stat.title) }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">{{ getChangeText(stat) }}</p>
                </div>
            </div>
        </div>
    </div>
</template>
