<script setup>
import { useDarkMode } from '@/composables/useDarkMode';
import BusinessAnalytics from '../BusinessAnalytics.vue';

const props = defineProps({
    dashboardData: {
        type: Object,
        default: () => ({})
    },
    selectedPeriod: {
        type: String,
        default: '30days'
    }
});

const { isDark } = useDarkMode();
</script>

<template>
    <div class="space-y-6">
        <BusinessAnalytics 
            :data="dashboardData.analytics || {}"
            :period="selectedPeriod"
        />
    </div>
</template>
