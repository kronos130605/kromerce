<template>
    <div class="dashboard-business">
        <!-- Header con estadísticas principales -->
        <div class="dashboard-header">
            <h1>Business Dashboard</h1>
            <div class="stats-grid">
                <StatCard
                    title="Total Products"
                    :value="statistics.totalProducts"
                    icon="package"
                    color="blue"
                />
                <StatCard
                    title="Active Products"
                    :value="statistics.activeProducts"
                    icon="check-circle"
                    color="green"
                />
                <StatCard
                    title="Low Stock"
                    :value="statistics.lowStockProducts"
                    icon="alert-triangle"
                    color="yellow"
                />
                <StatCard
                    title="Categories"
                    :value="statistics.totalCategories"
                    icon="folder"
                    color="purple"
                />
            </div>
        </div>

        <!-- Alertas y notificaciones -->
        <div v-if="alerts.length > 0" class="alerts-section">
            <h2>Alerts & Notifications</h2>
            <div class="alerts-grid">
                <AlertCard
                    v-for="alert in alerts"
                    :key="alert.message"
                    :alert="alert"
                />
            </div>
        </div>

        <!-- Estado de monedas -->
        <div class="currency-section">
            <h2>Currency Status</h2>
            <CurrencyStatusCard
                :currency-status="currencyStatus"
                @update-rates="handleUpdateRates"
            />
        </div>

        <!-- Productos recientes y actividad -->
        <div class="activity-section">
            <div class="recent-products">
                <h2>Recent Products</h2>
                <ProductList
                    :products="statistics.recentProducts"
                    compact
                />
            </div>
            
            <div class="recent-activity">
                <h2>Recent Activity</h2>
                <ActivityList
                    :activities="recentActivity"
                />
            </div>
        </div>

        <!-- Top productos -->
        <div class="top-products-section">
            <h2>Top Performing Products</h2>
            <div class="top-products-grid">
                <TopProductsCard
                    title="Top Revenue"
                    :products="topProducts.byRevenue"
                    metric="revenue"
                />
                <TopProductsCard
                    title="Most Viewed"
                    :products="topProducts.byViews"
                    metric="views"
                />
                <TopProductsCard
                    title="Best Sellers"
                    :products="topProducts.bySales"
                    metric="sales"
                />
            </div>
        </div>

        <!-- Gráficos -->
        <div class="charts-section">
            <h2>Analytics</h2>
            <div class="charts-grid">
                <ChartCard
                    title="Monthly Revenue"
                    :data="chartData.monthlyRevenue"
                    type="line"
                />
                <ChartCard
                    title="Product Growth"
                    :data="chartData.productGrowth"
                    type="bar"
                />
                <ChartCard
                    title="Currency Performance"
                    :data="chartData.currencyPerformance"
                    type="area"
                />
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { Head } from '@inertiajs/vue3'
import StatCard from '@/components/Dashboard/StatCard.vue'
import AlertCard from '@/components/Dashboard/AlertCard.vue'
import CurrencyStatusCard from '@/components/Dashboard/CurrencyStatusCard.vue'
import ProductList from '@/components/Dashboard/ProductList.vue'
import ActivityList from '@/components/Dashboard/ActivityList.vue'
import TopProductsCard from '@/components/Dashboard/TopProductsCard.vue'
import ChartCard from '@/components/Dashboard/ChartCard.vue'

const props = defineProps({
    statistics: Object,
    recentActivity: Object,
    currencyStatus: Object,
    topProducts: Object,
    alerts: Array,
    chartData: Object,
})

const handleUpdateRates = () => {
    // Llamar a la API para actualizar tasas
    router.post('/currency/update-rates')
}
</script>

<style scoped>
.dashboard-business {
    padding: 2rem;
    max-width: 1400px;
    margin: 0 auto;
}

.dashboard-header {
    margin-bottom: 2rem;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1.5rem;
    margin-top: 1.5rem;
}

.alerts-section {
    margin-bottom: 2rem;
}

.alerts-grid {
    display: grid;
    gap: 1rem;
    margin-top: 1rem;
}

.currency-section {
    margin-bottom: 2rem;
}

.activity-section {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 2rem;
    margin-bottom: 2rem;
}

.top-products-section {
    margin-bottom: 2rem;
}

.top-products-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 1.5rem;
    margin-top: 1rem;
}

.charts-section {
    margin-bottom: 2rem;
}

.charts-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
    gap: 1.5rem;
    margin-top: 1rem;
}

@media (max-width: 768px) {
    .activity-section {
        grid-template-columns: 1fr;
    }
    
    .charts-grid {
        grid-template-columns: 1fr;
    }
}
</style>
