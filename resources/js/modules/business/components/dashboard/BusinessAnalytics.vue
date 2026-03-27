<script setup>
import { computed, ref } from 'vue';
import { useI18n } from 'vue-i18n';

const props = defineProps({
    chartData: {
        type: Object,
        default: () => ({})
    }
});

const { t } = useI18n();

// Mock data for charts
const mockChartData = computed(() => ({
    revenue: {
        labels: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun'],
        datasets: [{
            label: 'Ingresos',
            data: [12000, 19000, 15000, 25000, 22000, 30000],
            borderColor: 'rgb(59, 130, 246)',
            backgroundColor: 'rgba(59, 130, 246, 0.1)',
        }]
    },
    orders: {
        labels: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun'],
        datasets: [{
            label: 'Pedidos',
            data: [120, 190, 150, 250, 220, 300],
            borderColor: 'rgb(16, 185, 129)',
            backgroundColor: 'rgba(16, 185, 129, 0.1)',
        }]
    },
    products: {
        labels: ['Electrónica', 'Ropa', 'Hogar', 'Deportes', 'Libros'],
        datasets: [{
            label: 'Ventas por categoría',
            data: [35, 25, 20, 15, 5],
            backgroundColor: [
                'rgba(59, 130, 246, 0.8)',
                'rgba(16, 185, 129, 0.8)',
                'rgba(245, 158, 11, 0.8)',
                'rgba(239, 68, 68, 0.8)',
                'rgba(139, 92, 246, 0.8)',
            ],
        }]
    }
}));

const chartData = computed(() => ({
    ...mockChartData.value,
    ...props.chartData
}));

const activeChart = ref('revenue');

const chartOptions = [
    { key: 'revenue', label: 'Ingresos', icon: '💰' },
    { key: 'orders', label: 'Pedidos', icon: '📦' },
    { key: 'products', label: 'Productos', icon: '📊' }
];

const stats = computed(() => [
    {
        label: 'Ingresos Totales',
        value: '$123,000',
        change: '+23.5%',
        trend: 'up',
        icon: '💰'
    },
    {
        label: 'Pedidos Totales',
        value: '1,234',
        change: '+15.2%',
        trend: 'up',
        icon: '📦'
    },
    {
        label: 'Valor Promedio',
        value: '$99.70',
        change: '+5.8%',
        trend: 'up',
        icon: '📈'
    },
    {
        label: 'Tasa Conversión',
        value: '3.2%',
        change: '+0.8%',
        trend: 'up',
        icon: '🎯'
    }
]);
</script>

<template>
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md border border-gray-200 dark:border-gray-700 p-6">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                {{ t('business.analytics_overview') }}
            </h3>
            <div class="flex space-x-2">
                <button v-for="option in chartOptions" :key="option.key"
                        @click="activeChart = option.key"
                        :class="`px-3 py-1 rounded-lg text-sm font-medium transition-all ${
                            activeChart === option.key
                                ? 'bg-blue-500 text-white'
                                : 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600'
                        }`">
                    <span class="mr-1">{{ option.icon }}</span>
                    {{ option.label }}
                </button>
            </div>
        </div>

        <!-- Stats Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <div v-for="stat in stats" :key="stat.label"
                 class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-2xl">{{ stat.icon }}</span>
                    <span :class="`px-2 py-1 rounded-full text-xs font-medium ${
                        stat.trend === 'up'
                            ? 'bg-green-100 dark:bg-green-900 text-green-700 dark:text-green-300'
                            : 'bg-red-100 dark:bg-red-900 text-red-700 dark:text-red-300'
                    }`">
                        {{ stat.change }}
                    </span>
                </div>
                <div class="text-2xl font-bold text-gray-900 dark:text-white">
                    {{ stat.value }}
                </div>
                <div class="text-sm text-gray-500 dark:text-gray-400">
                    {{ stat.label }}
                </div>
            </div>
        </div>

        <!-- Chart Placeholder -->
        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-8">
            <div class="text-center">
                <div class="text-6xl mb-4">📊</div>
                <h4 class="text-lg font-medium text-gray-900 dark:text-white mb-2">
                    {{ chartOptions.find(opt => opt.key === activeChart)?.label }} Chart
                </h4>
                <p class="text-gray-600 dark:text-gray-400">
                    {{ t('business.chart_coming_soon') }}
                </p>
                <div class="mt-4 text-sm text-gray-500 dark:text-gray-400">
                    <p>Sample data points: {{ chartData[activeChart]?.datasets?.[0]?.data?.length || 0 }}</p>
                </div>
            </div>
        </div>

        <!-- Export Options -->
        <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-600">
            <div class="flex items-center justify-between">
                <span class="text-sm text-gray-500 dark:text-gray-400">
                    {{ t('business.data_period') }}: Últimos 30 días
                </span>
                <div class="flex space-x-2">
                    <button class="text-sm text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white">
                        {{ t('business.export_csv') }}
                    </button>
                    <button class="text-sm text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white">
                        {{ t('business.export_pdf') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>
