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
    class="fixed top-16 left-0 z-50 h-[calc(100vh-4rem)]
    bg-white dark:bg-gray-900 border-r border-gray-200 dark:border-gray-700
    transform transition-all duration-300 ease-in-out
    lg:relative lg:transform-none
    ${isCollapsed ? 'lg:w-16 w-64' : 'w-64'}
    ${isMobileOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'}
    "
  >
    <!-- Content Wrapper -->
    <div class="h-full flex flex-col">

      <!-- Navigation -->
      <nav class="flex-1 p-4 space-y-2 overflow-y-auto">
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
      </Link>
    </nav>

      <!-- User Info -->
      <div :class="`${isCollapsed ? 'p-2.5' : 'p-4'} border-t border-gray-200 dark:border-gray-700`">
        <!-- Expanded State -->
        <div v-if="!isCollapsed" class="flex items-center space-x-3">
          <!-- Avatar fallback with initials -->
          <div
            v-if="!user?.avatar"
            class="h-10 w-10 rounded-full bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center text-white text-sm font-medium transition-all duration-200"
          >
            {{ userInitials }}
          </div>
          <img
            v-else
            :src="userAvatar"
            :alt="displayName"
            class="h-10 w-10 rounded-full object-cover transition-all duration-200"
          />
          <div class="flex-1 min-w-0">
            <p class="text-sm font-medium text-gray-900 dark:text-white truncate transition-opacity duration-200">
              {{ displayName }}
            </p>
            <p class="text-xs text-gray-500 dark:text-gray-400 truncate transition-opacity duration-200">
              {{ user?.email || '' }}
            </p>
          </div>
        </div>

        <!-- Collapsed State -->
        <div v-else class="flex flex-col items-center space-y-3">
          <!-- Avatar (smaller when collapsed) -->
          <div
            v-if="!user?.avatar"
            class="h-8 w-8 rounded-full bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center text-white text-xs font-medium transition-all duration-200"
          >
            {{ userInitials }}
          </div>
          <img
            v-else
            :src="userAvatar"
            :alt="displayName"
            class="h-8 w-8 rounded-full object-cover transition-all duration-200"
          />
          <!-- User name tooltip - mejorado para mayor visibilidad -->
          <div class="group relative">
            <p class="text-xs font-medium text-gray-700 dark:text-gray-300 whitespace-nowrap transition-colors duration-200 group-hover:text-gray-900 dark:group-hover:text-white">
              <!-- Truncate name to fit in collapsed sidebar -->
              {{ displayName.length > 8 ? displayName.substring(0, 8) + '...' : displayName }}
            </p>
            <!-- Full name tooltip on hover - ajustado para no sobresalir -->
            <div class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-1 px-2 py-1 bg-gray-900 dark:bg-gray-700 text-white text-xs rounded opacity-0 group-hover:opacity-100 transition-opacity duration-200 pointer-events-none whitespace-nowrap z-50 max-w-[12rem]">
              {{ displayName }}
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Collapse Toggle (Desktop) -->
    <button
      @click="toggleSidebar"
      class="hidden lg:flex absolute -right-3 top-8 items-center justify-center w-6 h-6 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-full shadow-md hover:shadow-lg transition-all duration-200 z-10"
    >
      <Icon
        name="chevronLeft"
        category="ui"
        :class="`text-gray-600 dark:text-gray-400 transform transition-transform duration-200 ${isCollapsed ? 'rotate-180' : ''}`"
      />
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

// User avatar computed property
const userAvatar = computed(() => {
  return user.value?.avatar || '/images/default-avatar.png';
});

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
