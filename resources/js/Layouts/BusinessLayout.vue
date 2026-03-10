<template>
  <div class="min-h-screen bg-gray-50 dark:bg-gray-900">
    <!-- Business Dashboard Navbar -->
    <BusinessDashboardNavbar ref="navbarRef" />

    <div class="flex pt-16">
      <!-- Sidebar -->
      <BusinessSidebar ref="sidebarRef" />

      <!-- Main Content with dynamic width adjustment -->
      <main 
        class="flex-1 overflow-y-auto transition-all duration-300"
        :class="{
          'lg:ml-0': sidebarRef?.isCollapsed
        }"
      >
        <!-- Dynamic Content Slot -->
        <div class="px-6 pb-6">
          <component 
            :is="currentContent"
            :products="page.props.products"
            :categories="page.props.categories"
            :filters="page.props.filters"
            :statistics="page.props.statistics"
          />
        </div>
      </main>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import BusinessSidebar from '@/components/navigation/sidebars/BusinessSidebar.vue';
import BusinessDashboardNavbar from '@/components/navigation/navbars/BusinessDashboardNavbar.vue';
import { useSidebar } from '@/Composables/useSidebar.js';

// Import content components
import DashboardContent from '@/Pages/Business/Content/DashboardContent.vue';
import ProductsContent from '@/Pages/Business/Content/ProductsContent.vue';
import OrdersContent from '@/Pages/Business/Content/OrdersContent.vue';
import AnalyticsContent from '@/Pages/Business/Content/AnalyticsContent.vue';

const page = usePage();
const { t } = useI18n();
const sidebarRef = ref(null);
const navbarRef = ref(null);

// Use sidebar composable
const { isCollapsed } = useSidebar();

// Get active tab from props or default to overview
const activeTab = computed(() => page.props.activeTab || 'overview');

// Content components mapping
const contentComponents = {
    overview: DashboardContent,
    products: ProductsContent,
    orders: OrdersContent,
    analytics: AnalyticsContent,
    marketing: AnalyticsContent, // Temporal, luego crear MarketingContent
    settings: AnalyticsContent, // Temporal, luego crear SettingsContent
};

// Get current component based on active tab
const currentContent = computed(() => {
    return contentComponents[activeTab.value] || DashboardContent;
});

// Listen for toggle sidebar event
const handleToggleSidebar = () => {
  // The composable handles the state, we just need to ensure the ref is available
  if (sidebarRef.value) {
    // The sidebar component will handle its own state
  }
};

onMounted(() => {
  document.addEventListener('toggle-sidebar', handleToggleSidebar);
});

onUnmounted(() => {
  document.removeEventListener('toggle-sidebar', handleToggleSidebar);
});
</script>
