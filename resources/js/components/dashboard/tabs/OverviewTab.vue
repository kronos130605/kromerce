<script setup>
import { useDarkMode } from '@/composables/useDarkMode.js';
import BusinessQuickActions from '../BusinessQuickActions.vue';
import TopProducts from '../TopProducts.vue';
import RecentOrders from '../RecentOrders.vue';

const props = defineProps({
    dashboardData: {
        type: Object,
        default: () => ({})
    }
});

const { isDark } = useDarkMode();
</script>

<template>
    <div class="space-y-6">
        <BusinessQuickActions />
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <TopProducts :products="dashboardData.topProducts || []" />
            <RecentOrders :orders="dashboardData.recentOrders || []" />
        </div>
    </div>
</template>
