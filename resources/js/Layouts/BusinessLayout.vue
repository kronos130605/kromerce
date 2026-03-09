<template>
  <div class="min-h-screen bg-background dark:bg-background">
    <!-- Business Dashboard Navbar -->
    <BusinessDashboardNavbar ref="navbarRef" />

    <div class="flex pt-16">
      <!-- Sidebar -->
      <BusinessSidebar ref="sidebarRef" />

      <!-- Main Content with dynamic width adjustment -->
      <main 
        class="flex-1 overflow-y-auto p-6 transition-all duration-300"
        :class="{
          'lg:ml-0': sidebarRef?.isCollapsed,
          'lg:ml-0': !sidebarRef?.isCollapsed
        }"
      >
        <slot />
      </main>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import BusinessSidebar from '@/components/navigation/sidebars/BusinessSidebar.vue';
import BusinessDashboardNavbar from '@/components/navigation/navbars/BusinessDashboardNavbar.vue';

const sidebarRef = ref(null);
const navbarRef = ref(null);

// Listen for toggle sidebar event
const handleToggleSidebar = () => {
  if (sidebarRef.value) {
    sidebarRef.value.toggleSidebar();
  }
};

onMounted(() => {
  document.addEventListener('toggle-sidebar', handleToggleSidebar);
});

onUnmounted(() => {
  document.removeEventListener('toggle-sidebar', handleToggleSidebar);
});
</script>
