<template>
    <div class="business-analytics">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                    <svg class="w-5 h-5 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6H2m7 7V10a2 2 0 0 0 2 2h5.586a1 1 0 0 0 .707.293l-6.414 6.414a1 1 0 0 0 .707-.293l-6.415-6.586a1 1 0 0 0-.707-.293l-6.586 6.586a1 1 0 0 0-.707-.293l-6.586-6.586z"></path>
                    </svg>
                    Analytics Overview
                </h3>
                <select v-model="selectedPeriod" class="px-4 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 rounded-lg border border-gray-300 dark:border-gray-600 text-sm">
                    <option value="7days">Last 7 Days</option>
                    <option value="30days">Last 30 Days</option>
                    <option value="90days">Last 90 Days</option>
                    <option value="1year">Last Year</option>
                </select>
            </div>
            
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Revenue Chart -->
                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                    <h4 class="text-base font-medium text-gray-900 dark:text-white mb-4">Monthly Revenue</h4>
                    <div class="h-64 flex items-end justify-between">
                        <div v-for="(item, index) in chartData.monthlyRevenue" :key="index" class="flex-1 mx-1">
                            <div class="bg-blue-500 rounded-t" :style="{ height: getBarHeight(item.revenue) + '%' }"></div>
                        </div>
                    </div>
                    <div class="flex justify-between mt-2 text-xs text-gray-600 dark:text-gray-400">
                        <div v-for="(item, index) in chartData.monthlyRevenue" :key="index" class="flex-1 text-center">
                            {{ item.month }}
                        </div>
                    </div>
                </div>
                
                <!-- Product Growth Chart -->
                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                    <h4 class="text-base font-medium text-gray-900 dark:text-white mb-4">Product Growth</h4>
                    <div class="h-64 flex items-end justify-between">
                        <div v-for="(item, index) in chartData.productGrowth" :key="index" class="flex-1 mx-1">
                            <div class="bg-green-500 rounded-t" :style="{ height: getBarHeight(item.products) + '%' }"></div>
                        </div>
                    </div>
                    <div class="flex justify-between mt-2 text-xs text-gray-600 dark:text-gray-400">
                        <div v-for="(item, index) in chartData.productGrowth" :key="index" class="flex-1 text-center">
                            {{ item.month }}
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Currency Performance -->
            <div class="mt-6 bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                <h4 class="text-base font-medium text-gray-900 dark:text-white mb-4">Currency Performance</h4>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div v-for="(perf, index) in chartData.currencyPerformance" :key="index" class="bg-white dark:bg-gray-600 rounded-lg p-3">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm font-medium text-gray-900 dark:text-white">{{ perf.currency }}</span>
                            <span class="text-xs px-2 py-1 rounded-full" :class="perf.trend === 'up' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'">
                                {{ perf.trend === 'up' ? '↑' : '↓' }} {{ Math.abs(perf.change_percent) }}%
                            </span>
                        </div>
                        <div class="text-lg font-semibold text-gray-900 dark:text-white">{{ perf.current_rate }}</div>
                        <div class="text-xs text-gray-600 dark:text-gray-400">Current Rate</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue'

const props = defineProps({
    chartData: {
        type: Object,
        required: true
    }
})

const selectedPeriod = ref('30days')

const maxRevenue = computed(() => {
    return Math.max(...props.chartData.monthlyRevenue.map(item => item.revenue), 1)
})

const maxProducts = computed(() => {
    return Math.max(...props.chartData.productGrowth.map(item => item.products), 1)
})

const getBarHeight = (value, maxValue) => {
    return maxValue > 0 ? (value / maxValue) * 100 : 0
}
</script>

<style scoped>
.business-analytics {
    @apply space-y-6;
}
</style>
