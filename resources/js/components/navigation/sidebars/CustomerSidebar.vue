<script setup>
import { ref, computed, onMounted, onBeforeUnmount } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { Link } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import { useNavigation } from '@/composables/useNavigation.js';
import { useAuth } from '@/composables/useAuth.js';
import Badge from '@/components/ui/Badge.vue';

const page = usePage();
const { t } = useI18n();

// Use auth composable
const { user, displayName, userAvatar, userInitials } = useAuth();

// Use navigation composable
const { sidebarNavigationItems } = useNavigation();

const isCollapsed = ref(false);
const isMobileOpen = ref(false);

const toggleSidebar = () => {
  isCollapsed.value = !isCollapsed.value;
  localStorage.setItem('customerSidebarCollapsed', isCollapsed.value);
};

const closeMobileSidebar = () => {
  isMobileOpen.value = false;
};

const openMobileSidebar = () => {
  isMobileOpen.value = true;
};

onMounted(() => {
  // Load saved state
  const savedState = localStorage.getItem('customerSidebarCollapsed');
  if (savedState !== null) {
    isCollapsed.value = savedState === 'true';
  }

  // Handle mobile menu
  const handleResize = () => {
    if (window.innerWidth >= 1024) {
      isMobileOpen.value = false;
    }
  };

  window.addEventListener('resize', handleResize);
  handleResize();

  onBeforeUnmount(() => {
    window.removeEventListener('resize', handleResize);
  });
});

</script>

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
      transform transition-transform duration-300 ease-in-out
      lg:relative lg:transform-none
      ${isCollapsed ? 'lg:w-16 w-64' : 'w-64'}
      ${isMobileOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'}
    `"
  >
    <!-- Mobile Close Button -->
    <div class="lg:hidden p-4 border-b border-gray-200 dark:border-gray-700">
      <button
        @click="closeMobileSidebar"
        class="p-2 rounded-md text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-800"
      >
        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
        </svg>
      </button>
    </div>

    <!-- Navigation -->
    <nav class="p-4 space-y-2">
      <template v-for="item in sidebarNavigationItems" :key="item.name">
        <Link
          :href="item.href"
          :class="`
            flex items-center px-3 py-2 rounded-lg text-sm font-medium transition-colors
            ${item.active 
              ? 'bg-blue-50 dark:bg-blue-900 text-blue-700 dark:text-blue-300' 
              : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800'
            }
            ${isCollapsed ? 'justify-center' : ''}
          `"
          @click="closeMobileSidebar"
        >
          <svg
            :class="`h-5 w-5 ${isCollapsed ? '' : 'mr-3'}`"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24"
          >
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="item.icon" />
          </svg>
          
          <span v-if="!isCollapsed" class="flex-1">{{ item.label }}</span>
          
          <Badge v-if="item.badge && !isCollapsed" variant="primary" size="sm">
            {{ item.badge }}
          </Badge>
        </Link>
      </template>
    </nav>

    <!-- User Info (Expanded State) -->
    <div v-if="!isCollapsed" class="absolute bottom-0 left-0 right-0 p-4 border-t border-gray-200 dark:border-gray-700">
      <div class="flex items-center space-x-3">
        <!-- Avatar fallback with initials -->
        <div 
          v-if="!user?.avatar"
          class="h-8 w-8 rounded-full bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center text-white text-sm font-medium"
        >
          {{ userInitials }}
        </div>
        <img
          v-else
          :src="userAvatar"
          :alt="displayName"
          class="h-8 w-8 rounded-full object-cover"
        />
        <div class="flex-1 min-w-0">
          <p class="text-sm font-medium text-gray-900 dark:text-white truncate">
            {{ displayName }}
          </p>
          <p class="text-xs text-gray-500 dark:text-gray-400 truncate">
            {{ user?.email || '' }}
          </p>
        </div>
      </div>
    </div>

    <!-- Collapse Toggle (Desktop) -->
    <button
      @click="toggleSidebar"
      class="hidden lg:flex absolute -right-3 top-8 items-center justify-center w-6 h-6 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-full shadow-md hover:shadow-lg transition-shadow"
    >
      <svg
        :class="`h-3 w-3 text-gray-600 dark:text-gray-400 transform transition-transform ${isCollapsed ? 'rotate-180' : ''}`"
        fill="none"
        stroke="currentColor"
        viewBox="0 0 24 24"
      >
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
      </svg>
    </button>
  </aside>
</template>
