<script setup>
import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';

// Import customer components
import CustomerDashboardNavbar from '@/components/navigation/navbars/CustomerDashboardNavbar.vue';
import CustomerSidebar from '@/components/navigation/sidebars/CustomerSidebar.vue';

// Import content components
import DashboardContent from '@/modules/customer/content/DashboardContent.vue';

const page = usePage();

// Get active tab from props or default to overview
const activeTab = computed(() => page.props.activeTab || 'overview');

// Content components mapping
const contentComponents = {
    overview: DashboardContent,
    orders: DashboardContent, // Temporal, luego crear OrdersContent
    profile: DashboardContent, // Temporal, luego crear ProfileContent
    settings: DashboardContent, // Temporal, luego crear SettingsContent
};

// Current content component
const currentContent = computed(() => {
    return contentComponents[activeTab.value] || DashboardContent;
});
</script>

<template>
  <div class="min-h-screen bg-gray-50 dark:bg-gray-900">
    <!-- Customer Navbar -->
    <CustomerDashboardNavbar ref="navbarRef" />

    <div class="flex h-[calc(100vh-10px)]">
      <!-- Customer Sidebar -->
      <CustomerSidebar ref="sidebarRef" />

      <!-- Main Content -->
      <main class="flex-1 overflow-y-auto">
        <div class="px-4 pb-4 pt-[5rem] h-full">
          <component
            :is="currentContent"
            :user="page.props.auth.user"
            :statistics="page.props.statistics"
          />
        </div>
      </main>
    </div>
  </div>
</template>
