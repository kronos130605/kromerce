<template>
  <div class="min-h-screen bg-background dark:bg-background">
    <!-- Business Dashboard Navbar -->
    <BusinessDashboardNavbar />

    <div class="flex pt-16">
      <!-- Sidebar -->
      <BusinessSidebar />

      <!-- Main Content -->
      <main class="flex-1 p-6">
        <slot />
      </main>
    </div>
  </div>
</template>

<script setup>
import BusinessSidebar from '@/components/navigation/sidebars/BusinessSidebar.vue';
import BusinessDashboardNavbar from '@/components/navigation/navbars/BusinessDashboardNavbar.vue';
</script>
