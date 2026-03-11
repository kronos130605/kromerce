<template>
  <!-- Mobile Floating Menu Button - Professional Design -->
  <button
    v-if="isMobile"
    @click="toggleSidebar"
    class="fixed top-20 left-2 z-50 w-14 h-14 bg-gradient-to-br from-blue-500 to-blue-600 dark:from-blue-600 dark:to-blue-700 text-white rounded-full shadow-2xl hover:shadow-3xl transition-all duration-300 ease-out hover:scale-105 active:scale-95 lg:hidden flex items-center justify-center group"
    :title="isMobileOpen ? 'Close menu' : 'Open menu'"
  >
    <!-- Background glow effect -->
    <span class="absolute inset-0 bg-blue-400 rounded-full opacity-0 group-hover:opacity-20 transition-opacity duration-300 blur-xl"></span>

    <!-- Icon container -->
    <span class="relative flex items-center justify-center">
      <Icon
        v-if="!isMobileOpen"
        name="menu"
        category="ui"
        class="w-6 h-6 transition-all duration-300 group-hover:rotate-12"
      />
      <Icon
        v-else
        name="close"
        category="ui"
        class="w-6 h-6 transition-all duration-300 group-hover:rotate-90"
      />
    </span>

    <!-- Pulse animation when closed -->
    <span
      v-if="!isMobileOpen"
      class="absolute inset-0 rounded-full border-2 border-blue-400 opacity-0 group-hover:opacity-100 transition-opacity duration-300"
    ></span>
  </button>

  <!-- Mobile Overlay -->
  <div
    v-if="isMobileOpen"
    class="fixed inset-0 bg-black bg-opacity-60 backdrop-blur-sm z-40 lg:hidden transition-opacity duration-300"
    @click="closeMobileSidebar"
  ></div>

  <!-- Sidebar -->
  <aside
    class="fixed lg:static inset-y-0 left-0 z-30 bg-background dark:bg-gray-900 border-r border-border dark:border-gray-800 transform transition-all duration-300 ease-in-out"
    :class="sidebarClasses"
  >
    <!-- Sidebar Header - Always visible -->
    <div class="flex items-center justify-between p-4 border-b border-border dark:border-gray-800 h-16">
      <!-- Logo/Title -->
      <div class="flex items-center space-x-3">
        <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg flex items-center justify-center text-white font-bold text-sm flex-shrink-0">
          K
        </div>
        <h3 v-if="!isCollapsed" class="text-lg font-semibold text-foreground truncate">Menu</h3>
      </div>

      <!-- Collapse Toggle (Desktop) - Hidden when collapsed -->
      <button
        v-if="!isMobile && !isCollapsed"
        @click="toggleSidebar"
        class="p-2 rounded-lg hover:bg-accent transition-colors flex-shrink-0"
        title="Collapse sidebar"
      >
        <svg
          class="w-5 h-5 transition-transform duration-300"
          fill="none"
          stroke="currentColor"
          viewBox="0 0 24 24"
        >
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"></path>
        </svg>
      </button>
    </div>

      <!-- Navigation - Always visible -->
    <nav class="h-[calc(100vh-4rem)] overflow-y-auto p-2 space-y-1">
      <Link
        v-for="item in sidebarNavigationItems"
        :key="item.href"
        :href="isActive(item.href) ? '#' : item.href"
        class="flex items-center px-3 py-2.5 rounded-lg transition-colors group relative"
        :class="[
          isActive(item.href)
            ? 'bg-gradient-to-r from-blue-500 to-blue-600 dark:from-blue-600 dark:to-blue-700 text-white cursor-default shadow-md'
            : 'text-muted-foreground hover:bg-accent hover:text-accent-foreground cursor-pointer',
          isCollapsed ? 'justify-center' : 'justify-start space-x-3'
        ]"
        @click="isActive(item.href) ? $event.preventDefault() : null"
      >

        <!-- Icon -->
        <Icon
          :name="item.name"
          category="business"
          :class="`
            text-xl flex-shrink-0
            ${isCollapsed ? 'w-6 h-6' : 'w-5 h-5'}
          `"
        />

        <!-- Label and Badge (hidden when collapsed) -->
        <div v-if="!isCollapsed" class="flex-1 flex items-center justify-between min-w-0">
          <span class="font-medium truncate">{{ item.label }}</span>
          <span
            v-if="item.badge"
            class="ml-auto px-2 py-1 text-xs rounded-full flex-shrink-0"
            :class="getBadgeClass(item.badge.type)"
          >
            {{ item.badge.text }}
          </span>
        </div>

        <!-- Tooltip for collapsed state -->
        <div
          v-if="isCollapsed"
          class="absolute left-full ml-2 px-2 py-1 bg-gray-900 dark:bg-gray-700 text-white text-sm rounded opacity-0 group-hover:opacity-100 transition-opacity duration-200 pointer-events-none whitespace-nowrap z-50"
        >
          {{ item.label }}
          <div class="absolute right-full top-1/2 transform -translate-y-1/2 border-4 border-transparent border-r-gray-900 dark:border-r-gray-700"></div>
        </div>
      </Link>
    </nav>
  </aside>

  <!-- Desktop Expand Button - Outside sidebar container -->
  <aside>
    <button
    v-if="isCollapsed && !isMobile && showExpandButton"
    @click="toggleSidebar"
    class="fixed left-16 w-6 h-12 bg-gradient-to-b from-gray-50 to-gray-100 dark:from-gray-800 dark:to-gray-700 border border-gray-200 dark:border-gray-600 rounded-r-lg shadow-md hover:shadow-lg transition-all duration-300 hover:translate-x-1 flex items-center justify-center z-40 group opacity-0 animate-fade-in"
    :data-first-render="isFirstLoad && isCollapsed"
    style="top: 72px;"
    title="Expand sidebar"
  >
      <!-- Vertical gradient line for visual effect -->
      <span class="absolute left-0 top-0 bottom-0 w-0.5 bg-gradient-to-b from-blue-400 to-blue-600 rounded-l-full"></span>

      <!-- Icon container -->
      <span class="relative flex items-center justify-center">
        <svg
          class="w-4 h-4 text-gray-600 dark:text-gray-300 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors duration-200"
          fill="none"
          stroke="currentColor"
          viewBox="0 0 24 24"
        >
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
        </svg>
      </span>

      <!-- Hover effect overlay -->
      <span class="absolute inset-0 bg-gradient-to-r from-transparent via-blue-50 to-blue-100 dark:from-transparent dark:via-blue-900/20 dark:to-blue-900/40 rounded-r-lg opacity-0 group-hover:opacity-100 transition-opacity duration-200"></span>

      <!-- Subtle top and bottom borders -->
      <span class="absolute top-0 left-0 right-0 h-px bg-gradient-to-r from-transparent via-gray-300 dark:via-gray-600 to-transparent"></span>
      <span class="absolute bottom-0 left-0 right-0 h-px bg-gradient-to-r from-transparent via-gray-300 dark:via-gray-600 to-transparent"></span>
    </button>
  </aside>
</template>

<script setup>
import { Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import { useSidebar } from '@/composables/useSidebar.js';
import { useNavigation } from '@/composables/useNavigation.js';
import { useAuth } from '@/composables/useAuth.js';
import { UIIcons, BusinessIcons } from '@/icons';
import Icon from '@/components/ui/Icon.vue';
import Badge from '@/components/ui/Badge.vue';

const page = usePage();

// Use auth composable
const { user, displayName, userInitials } = useAuth();

// Use navigation composable
const { sidebarNavigationItems } = useNavigation();

// Use sidebar composable
const {
    isCollapsed,
    isMobileOpen,
    showExpandButton,
    isMobile,
    sidebarClasses,
    toggleSidebar,
    closeMobileSidebar,
    isFirstLoad
} = useSidebar();

// Methods
const isActive = (href) => {
  const currentUrl = page.url;
  // Handle hash-based routes
  if (href.startsWith('#')) {
    return currentUrl.includes(href.substring(1));
  }
  // Handle exact and partial matches
  return currentUrl === href || currentUrl.startsWith(href);
};

const getBadgeClass = (type) => {
  const classes = {
    success: 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300',
    warning: 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300',
    error: 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300',
    info: 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300',
  };
  return classes[type] || 'bg-muted text-muted-foreground';
};
</script>

<style scoped>
@keyframes fadeIn {
  from {
    opacity: 0;
    transform: translateX(-5px);
  }
  to {
    opacity: 1;
    transform: translateX(0);
  }
}

.animate-fade-in {
  animation: fadeIn 0.2s ease-out forwards;
}

/* Prevenir parpadeo al cambiar de página */
.animate-fade-in:not([data-first-render="true"]) {
  animation: none;
  opacity: 1 !important;
  transform: translateX(0) !important;
}

/* Solo animar cuando el botón aparece por primera vez */
.animate-fade-in[data-first-render="true"] {
  animation: fadeIn 0.2s ease-out forwards;
}
</style>
