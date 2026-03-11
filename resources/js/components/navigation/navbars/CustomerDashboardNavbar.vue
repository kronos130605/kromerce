<script setup>
import { ref, onMounted, onBeforeUnmount } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import { useAuth } from '@/composables/useAuth.js';
import { useDarkMode } from '@/composables/useDarkMode.js';
import { useNavigation } from '@/composables/useNavigation.js';
import Badge from '@/components/ui/Badge.vue';
import LanguageSelector from '@/components/shared/LanguageSelector.vue';

const { t } = useI18n();

// Use auth composable
const {
    user,
    currentTenant,
    isBusinessOwner,
    displayName,
    userAvatar,
    userInitials,
} = useAuth();

// Use dark mode composable
const { isDark, toggleDarkMode } = useDarkMode();

const isOpen = ref(false);
const showUserDropdown = ref(false);
const userDropdownRef = ref(null);

let onDocumentClick;

const logout = () => {
  router.post('/logout');
  showUserDropdown.value = false;
};

// Use navigation composable
const { navigationItems } = useNavigation();

// Debug: Verificar items de navegación
console.log('CustomerDashboardNavbar - navigationItems:', navigationItems.value);

onMounted(() => {
  // Close dropdown when clicking outside
  onDocumentClick = (event) => {
    if (!showUserDropdown.value) return;
    const el = userDropdownRef.value;
    if (!el) return;
    if (event.target instanceof Node && !el.contains(event.target)) {
      showUserDropdown.value = false;
    }
  };
  document.addEventListener('click', onDocumentClick, true);
});

onBeforeUnmount(() => {
  if (onDocumentClick) {
    document.removeEventListener('click', onDocumentClick, true);
    onDocumentClick = undefined;
  }
});
</script>

<template>
  <header
    :class="[
      'fixed top-0 left-0 right-0 z-50 backdrop-blur-md transition-all duration-300',
      isDark
        ? 'bg-gray-900/95 border-b border-gray-800'
        : 'bg-white/95 border-b border-border shadow-sm'
    ]"
  >
    <div class="mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex justify-between items-center h-16">
        <!-- Left side - Logo and Mobile Menu -->
        <div class="flex items-center">
          <!-- Mobile menu button -->
          <button
            @click="isOpen = !isOpen"
            :class="[
              'lg:hidden p-2 rounded-md transition-colors',
              isDark
                ? 'text-gray-300 hover:text-white hover:bg-gray-800'
                : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100'
            ]"
          >
            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path v-if="!isOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
              <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>

          <!-- Logo -->
          <Link href="/" class="flex items-center ml-4 lg:ml-0 group">
            <div class="transition-transform duration-300 group-hover:scale-110">
              <img
                src="/images/kromerce-business-text.png"
                alt="Kromerce"
                :class="[
                  'h-8 w-auto object-contain transition-transform duration-300',
                  isDark ? 'filter brightness-0 invert' : ''
                ]"
              />
            </div>
          </Link>
        </div>

        <!-- Desktop Navigation -->
        <nav class="hidden lg:flex items-center gap-6">
          <Link
            v-for="{ href, label, icon } in navigationItems"
            :key="label"
            :href="href"
            :class="[
              'flex items-center gap-2 px-3 py-2 rounded-lg text-sm font-medium transition-colors',
              isDark
                ? 'text-gray-300 hover:text-white hover:bg-gray-800'
                : 'text-foreground hover:text-foreground hover:bg-accent'
            ]"
          >
            <span>{{ icon }}</span>
            {{ t(label) }}
          </Link>
        </nav>

        <!-- Right side - User actions -->
        <div class="flex items-center gap-4">
          <!-- Language Selector -->
          <LanguageSelector />

          <!-- Theme Toggle -->
          <button
            @click="toggleDarkMode"
            :class="[
              'p-2 rounded-md transition-colors',
              isDark
                ? 'text-gray-300 hover:text-white hover:bg-gray-800'
                : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100'
            ]"
          >
            <svg v-if="!isDark" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
            </svg>
            <svg v-else class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
            </svg>
          </button>

          <!-- Notifications -->
          <button
            :class="[
              'p-2 rounded-md transition-colors relative',
              isDark
                ? 'text-gray-300 hover:text-white hover:bg-gray-800'
                : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100'
            ]"
          >
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
            </svg>
            <span class="absolute top-1 right-1 h-2 w-2 bg-red-500 rounded-full"></span>
          </button>

          <!-- Tenant Info for Business Owners -->
          <div v-if="isBusinessOwner && currentTenant" class="hidden lg:flex items-center gap-2">
            <Badge
              variant="secondary"
              :class="[
                'text-xs',
                isDark
                  ? 'bg-gray-800 text-gray-300 border-gray-700'
                  : 'bg-muted text-foreground'
              ]"
            >
              {{ currentTenant.name }}
            </Badge>
          </div>

          <!-- User Dropdown -->
          <div class="relative" ref="userDropdownRef">
            <button
              @click="showUserDropdown = !showUserDropdown"
              :class="[
                'flex items-center space-x-3 p-2 rounded-md transition-colors',
                isDark
                  ? 'text-gray-300 hover:text-white hover:bg-gray-800'
                  : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100'
              ]"
            >
              <!-- Avatar fallback with initials -->
              <span
                v-if="!user?.avatar"
                class="h-8 w-8 rounded-full bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center text-white text-sm font-medium"
              >
                {{ userInitials }}
              </span>
              <img
                v-else
                :src="userAvatar"
                :alt="displayName"
                class="h-8 w-8 rounded-full object-cover"
              />
              <span class="hidden md:block text-sm font-medium">{{ displayName }}</span>
              <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
              </svg>
            </button>

            <!-- Dropdown Menu -->
            <div
              v-if="showUserDropdown"
              :class="[
                'absolute right-0 mt-2 w-48 rounded-md shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none',
                isDark
                  ? 'bg-gray-800 border border-gray-700'
                  : 'bg-white border border-gray-200'
              ]"
            >
              <div class="py-1">
                <Link
                  href="/profile"
                  :class="[
                    'block px-4 py-2 text-sm transition-colors',
                    isDark
                      ? 'text-gray-300 hover:bg-gray-700'
                      : 'text-gray-700 hover:bg-gray-100'
                  ]"
                >
                  {{ t('navigation.profile') }}
                </Link>
                <Link
                  href="/settings"
                  :class="[
                    'block px-4 py-2 text-sm transition-colors',
                    isDark
                      ? 'text-gray-300 hover:bg-gray-700'
                      : 'text-gray-700 hover:bg-gray-100'
                  ]"
                >
                  {{ t('navigation.settings') }}
                </Link>
                <div :class="[
                  'border-t',
                  isDark ? 'border-gray-700' : 'border-gray-200'
                ]"></div>
                <button
                  @click="logout"
                  :class="[
                    'block w-full text-left px-4 py-2 text-sm transition-colors',
                    isDark
                      ? 'text-gray-300 hover:bg-gray-700'
                      : 'text-gray-700 hover:bg-gray-100'
                  ]"
                >
                  {{ t('navigation.logout') }}
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Mobile Navigation Menu -->
      <div v-if="isOpen" :class="[
        'lg:hidden border-t',
        isDark ? 'border-gray-800 bg-gray-900' : 'border-gray-200 bg-white'
      ]">
        <div class="px-2 pt-2 pb-3 space-y-1">
          <Link
            v-for="{ href, label, icon } in navigationItems"
            :key="label"
            :href="href"
            :class="[
              'flex items-center gap-3 px-3 py-2 rounded-md text-base font-medium transition-colors',
              isDark
                ? 'text-gray-300 hover:text-white hover:bg-gray-800'
                : 'text-gray-700 hover:text-gray-900 hover:bg-gray-100'
            ]"
            @click="isOpen = false"
          >
            <span>{{ icon }}</span>
            {{ t(label) }}
          </Link>
        </div>
      </div>
    </div>
  </header>
</template>
