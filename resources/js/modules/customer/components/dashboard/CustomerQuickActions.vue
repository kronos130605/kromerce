<script setup>
import { ref } from 'vue';
import { useTranslations } from '@/composables/useTranslations';

const { t } = useTranslations();

const quickActions = ref([
    {
        id: 1,
        title: t('dashboard.track_order'),
        description: t('dashboard.track_package'),
        icon: '📦',
        badge: { type: 'warning', text: '2 ' + t('dashboard.active') }
    },
    {
        id: 2,
        title: t('dashboard.browse_deals'),
        description: t('dashboard.hot_offers'),
        icon: '🔥',
        badge: { type: 'danger', text: '24 ' + t('dashboard.new') }
    },
    {
        id: 3,
        title: t('dashboard.wishlist'),
        description: t('dashboard.saved_items'),
        icon: '❤️',
        badge: { type: 'success', text: '12 ' + t('dashboard.items') }
    },
    {
        id: 4,
        title: t('dashboard.customer_support'),
        description: t('dashboard.get_help'),
        icon: '💬',
        badge: null
    },
    {
        id: 5,
        title: t('dashboard.payment_methods'),
        description: t('dashboard.manage_cards'),
        icon: '💳',
        badge: null
    },
    {
        id: 6,
        title: t('dashboard.addresses'),
        description: t('dashboard.shipping_locations'),
        icon: '📍',
        badge: null
    },
    {
        id: 7,
        title: t('dashboard.reviews'),
        description: t('dashboard.rate_products'),
        icon: '⭐',
        badge: { type: 'warning', text: '3 ' + t('dashboard.pending') }
    },
    {
        id: 8,
        title: t('dashboard.settings'),
        description: t('dashboard.account_preferences'),
        icon: '⚙️',
        badge: null
    }
]);

const handleAction = (action) => {
    console.log('Action clicked:', action.title);
    // Handle navigation or action
};
</script>

<template>
    <div class="space-y-6 mt-8">
        <div>
            <h3 class="text-2xl font-bold text-gray-900 dark:text-white">{{ t('dashboard.quick_actions') }}</h3>
            <p class="text-gray-600 dark:text-gray-300 mt-1">{{ t('dashboard.get_things_done') }}</p>
        </div>
        
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <button v-for="action in quickActions" :key="action.id"
                    @click="handleAction(action)"
                    class="group bg-white dark:bg-gray-800 rounded-xl shadow-md border border-gray-100 dark:border-gray-700 p-4 hover:shadow-lg transition-all duration-300 hover:-translate-y-1 text-left">
                
                <span class="flex flex-col items-center text-center space-y-3">
                    <span class="w-12 h-12 bg-gradient-to-br from-blue-100 to-purple-100 dark:from-blue-900 dark:to-purple-900 rounded-xl flex items-center justify-center text-2xl group-hover:scale-110 transition-transform">
                        {{ action.icon }}
                    </span>
                    
                    <span class="flex-1">
                        <span class="font-semibold text-gray-900 dark:text-white group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">
                            {{ action.title }}
                        </span>
                        <span class="text-xs text-gray-600 dark:text-gray-300 mt-1">{{ action.description }}</span>
                    </span>
                    
                    <span v-if="action.badge" 
                         :class="`px-2 py-1 rounded-full text-xs font-bold ${
                             action.badge.type === 'success' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' :
                             action.badge.type === 'warning' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200' :
                             action.badge.type === 'danger' ? 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' :
                             'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200'
                         }`">
                        {{ action.badge.text }}
                    </span>
                </span>
            </button>
        </div>
    </div>
</template>
