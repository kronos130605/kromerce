<template>
  <!-- Mobile Overlay -->
  <div
    v-if="isMobileOpen"
    class="fixed inset-0 bg-black bg-opacity-50 z-40 lg:hidden"
    @click="closeMobileSidebar"
  ></div>

  <!-- Sidebar -->
  <aside
    :class="`
      fixed top-16 left-0 z-50 h-[calc(100vh-4rem)]
      bg-white dark:bg-gray-900 border-r border-gray-200 dark:border-gray-700
      transform transition-all duration-300 ease-in-out
      lg:relative lg:transform-none
      ${isCollapsed ? 'lg:w-16 w-64' : 'w-64'}
      ${isMobileOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'}
    `"
  >
    <!-- Content Wrapper -->
    <div class="h-full flex flex-col">

      <!-- Navigation -->
      <nav class="flex-1 p-2.5 space-y-2 overflow-y-auto">
      <Link
        v-for="item in sidebarNavigationItems"
        :key="item.href"
        :href="item.href"
        :class="[
          isActive(item.href)
            ? 'bg-blue-50 dark:bg-blue-900 text-blue-700 dark:text-blue-300'
            : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800',
          isCollapsed ? 'justify-center' : '',
          'flex items-center px-3 py-2 rounded-lg text-sm font-medium transition-all duration-200'
        ]"
        @click="closeMobileSidebar"
      >

            <Icon
              :name="item.name"
              category="business"
              :class="`
                ${isCollapsed ? 'w-6 h-6' : 'w-5 h-5'}
                ${isCollapsed ? '' : 'mr-3'}
                transition-all duration-200
              `"
            />

            <span v-if="!isCollapsed" class="flex-1 transition-opacity duration-200">{{ item.label }}</span>

            <Badge v-if="item.badge && !isCollapsed" variant="primary" size="sm">
              {{ item.badge }}
            </Badge>
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
      class="hidden lg:flex absolute -right-4 top-8 items-center justify-center w-6 h-6 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-full shadow-md hover:shadow-lg transition-all duration-200 z-10"
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
</script>
