<script setup>
import { ref, computed, onMounted, onBeforeUnmount } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { Link, router } from '@inertiajs/vue3';
import Button from '@/components/ui/Button.vue';
import Badge from '@/components/ui/Badge.vue';
import LanguageSelector from '@/components/LanguageSelector.vue';

const page = usePage();
const user = computed(() => page.props.auth.user);
const currentTenant = computed(() => page.props.current_tenant);

// Check user role
const isCustomer = computed(() => {
    const roles = user.value?.roles || [];
    return roles.some(role => role.name === 'customer') || user.value?.role === 'customer';
});

const isBusinessOwner = computed(() => {
    const roles = user.value?.roles || [];
    return roles.some(role => role.name === 'business_owner') || user.value?.role === 'business_owner';
});

const isSuperAdmin = computed(() => {
    const roles = user.value?.roles || [];
    return roles.some(role => role.name === 'super_admin') || user.value?.role === 'super_admin';
});

const isOpen = ref(false);
const isDarkTheme = ref(false);
const showUserDropdown = ref(false);
const userDropdownRef = ref(null);

let observer;
let onDocumentClick;

const applyDarkClass = (enabled) => {
  if (enabled) {
    document.documentElement.classList.add('dark');
  } else {
    document.documentElement.classList.remove('dark');
  }
};

const syncDarkThemeFromDom = () => {
  isDarkTheme.value = document.documentElement.classList.contains('dark');
};

const toggleDarkMode = () => {
  isDarkTheme.value = !isDarkTheme.value;
  applyDarkClass(isDarkTheme.value);
  try {
    localStorage.setItem('kromerce_theme', isDarkTheme.value ? 'dark' : 'light');
  } catch {
    // ignore
  }
};

const navigateTo = (href) => {
  router.visit(href);
  isOpen.value = false;
};

const logout = () => {
  router.post('/logout');
  showUserDropdown.value = false;
};

// Navigation items based on role
const navigationItems = computed(() => {
  if (isCustomer.value) {
    return [
      { href: '/dashboard', label: 'Dashboard', icon: '🏠' },
      { href: '#stores', label: 'Stores', icon: '🏪' },
      { href: '#orders', label: 'My Orders', icon: '📦' },
      { href: '#wishlist', label: 'Wishlist', icon: '❤️' },
      { href: '#deals', label: 'Deals', icon: '🎯' },
    ];
  } else if (isBusinessOwner.value) {
    return [
      { href: '/dashboard', label: 'Dashboard', icon: '📊' },
      { href: '#products', label: 'Products', icon: '📦' },
      { href: '#orders', label: 'Orders', icon: '🛒' },
      { href: '#analytics', label: 'Analytics', icon: '📈' },
      { href: '#settings', label: 'Settings', icon: '⚙️' },
    ];
  } else if (isSuperAdmin.value) {
    return [
      { href: '/dashboard', label: 'Dashboard', icon: '🔧' },
      { href: '#users', label: 'Users', icon: '👥' },
      { href: '#tenants', label: 'Tenants', icon: '🏢' },
      { href: '#analytics', label: 'Analytics', icon: '📊' },
      { href: '#settings', label: 'Settings', icon: '⚙️' },
    ];
  }
  return [];
});

onMounted(() => {
  try {
    const stored = localStorage.getItem('kromerce_theme');
    if (stored === 'dark' || stored === 'light') {
      applyDarkClass(stored === 'dark');
    }
  } catch {
    // ignore
  }

  syncDarkThemeFromDom();

  observer = new MutationObserver(syncDarkThemeFromDom);
  observer.observe(document.documentElement, {
    attributes: true,
    attributeFilter: ['class'],
  });

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
  if (observer) {
    observer.disconnect();
    observer = undefined;
  }

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
      isDarkTheme
        ? 'bg-gray-900/95 border-b border-gray-800'
        : 'bg-white/95 border-b border-border shadow-sm'
    ]"
  >
    <div class="container mx-auto px-4">
      <div class="flex justify-between items-center h-16">
        <!-- Logo -->
        <Link href="/" class="flex items-center group cursor-pointer">
          <div class="mr-3 transition-transform duration-300 group-hover:scale-110">
            <img
              src="/images/kromerce-business-text.png"
              alt="Kromerce"
              :class="[
                'h-8 w-auto object-contain transition-transform duration-300 group-hover:scale-110',
                isDarkTheme ? 'filter brightness-0 invert' : ''
              ]"
            />
          </div>
        </Link>

        <!-- Desktop Navigation -->
        <nav class="hidden lg:flex items-center gap-6">
          <Link
            v-for="{ href, label, icon } in navigationItems"
            :key="label"
            :href="href"
            :class="[
              'flex items-center gap-2 px-3 py-2 rounded-lg text-sm font-medium transition-colors',
              isDarkTheme
                ? 'text-gray-300 hover:text-white hover:bg-gray-800'
                : 'text-foreground hover:text-foreground hover:bg-accent'
            ]"
          >
            <span>{{ icon }}</span>
            {{ label }}
          </Link>
        </nav>

        <!-- Desktop User Section -->
        <div class="hidden lg:flex items-center gap-4">
          <!-- Tenant Info for Business Owners -->
          <div v-if="isBusinessOwner && currentTenant" class="flex items-center gap-2">
            <Badge
              variant="secondary"
              :class="[
                'text-xs',
                isDarkTheme
                  ? 'bg-gray-800 text-gray-300 border-gray-700'
                  : 'bg-muted text-foreground'
              ]"
            >
              🏢 {{ currentTenant.name }}
            </Badge>
          </div>

          <!-- User Dropdown -->
          <div class="relative">
            <button
              @click.stop="showUserDropdown = !showUserDropdown"
              :class="[
                'flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors cursor-pointer',
                isDarkTheme
                  ? 'text-gray-300 hover:text-white hover:bg-gray-800'
                  : 'text-foreground hover:text-foreground hover:bg-accent'
              ]"
            >
              <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center text-white text-sm font-bold">
                {{ user.name.charAt(0).toUpperCase() }}
              </div>
              <span class="hidden md:block">{{ user.name }}</span>
              <svg class="w-4 h-4 transition-transform" :class="{ 'rotate-180': showUserDropdown }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
              </svg>
            </button>

            <!-- Dropdown Menu -->
            <div
              v-if="showUserDropdown"
              ref="userDropdownRef"
              :class="[
                'absolute right-0 top-full mt-2 w-56 rounded-lg border shadow-lg py-2 z-50',
                isDarkTheme
                  ? 'bg-gray-900 border-gray-800'
                  : 'bg-background border-border'
              ]"
            >
              <!-- User Info -->
              <div class="px-4 py-3 border-b" :class="isDarkTheme ? 'border-gray-800' : 'border-border'">
                <p class="text-sm font-medium">{{ user.name }}</p>
                <p class="text-xs text-muted-foreground">{{ user.email }}</p>
                <div v-if="isCustomer" class="mt-2">
                  <Badge variant="secondary" class="text-xs">VIP Member</Badge>
                </div>
                <div v-else-if="isBusinessOwner" class="mt-2">
                  <Badge variant="secondary" class="text-xs">Business Owner</Badge>
                </div>
                <div v-else-if="isSuperAdmin" class="mt-2">
                  <Badge variant="secondary" class="text-xs">Super Admin</Badge>
                </div>
              </div>

              <!-- Menu Items -->
              <div class="py-2">
                <Link
                  href="/profile"
                  :class="[
                    'flex items-center gap-3 px-4 py-2 text-sm transition-colors',
                    isDarkTheme
                      ? 'text-gray-300 hover:text-white hover:bg-gray-800'
                      : 'text-foreground hover:text-foreground hover:bg-accent'
                  ]"
                >
                  <span>👤</span>
                  Profile Settings
                </Link>
                
                <Link
                  v-if="isCustomer"
                  href="#notifications"
                  :class="[
                    'flex items-center gap-3 px-4 py-2 text-sm transition-colors',
                    isDarkTheme
                      ? 'text-gray-300 hover:text-white hover:bg-gray-800'
                      : 'text-foreground hover:text-foreground hover:bg-accent'
                  ]"
                >
                  <span>🔔</span>
                  Notifications
                </Link>

                <Link
                  v-if="isBusinessOwner"
                  href="#billing"
                  :class="[
                    'flex items-center gap-3 px-4 py-2 text-sm transition-colors',
                    isDarkTheme
                      ? 'text-gray-300 hover:text-white hover:bg-gray-800'
                      : 'text-foreground hover:text-foreground hover:bg-accent'
                  ]"
                >
                  <span>💳</span>
                  Billing
                </Link>
              </div>

              <!-- Logout -->
              <div class="pt-2 border-t" :class="isDarkTheme ? 'border-gray-800' : 'border-border'">
                <button
                  @click="logout"
                  :class="[
                    'flex items-center gap-3 w-full px-4 py-2 text-sm transition-colors text-left',
                    isDarkTheme
                      ? 'text-red-400 hover:text-red-300 hover:bg-gray-800'
                      : 'text-red-600 hover:text-red-700 hover:bg-accent'
                  ]"
                >
                  <span>🚪</span>
                  Logout
                </button>
              </div>
            </div>
          </div>

          <!-- Language Selector -->
          <LanguageSelector />

          <!-- Dark Mode Toggle -->
          <button
            @click="toggleDarkMode"
            class="p-2 rounded-lg hover:bg-accent transition-colors cursor-pointer"
            :title="isDarkTheme ? 'Switch to light mode' : 'Switch to dark mode'"
          >
            <span v-if="!isDarkTheme" class="text-xl">🌙</span>
            <span v-else class="text-xl">☀️</span>
          </button>
        </div>

        <!-- Mobile Menu -->
        <div class="flex items-center lg:hidden gap-2">
          <!-- Language Selector Mobile -->
          <LanguageSelector />

          <!-- Dark Mode Toggle Mobile -->
          <button
            @click="toggleDarkMode"
            class="p-2 rounded-lg hover:bg-accent transition-colors cursor-pointer"
            :title="isDarkTheme ? 'Switch to light mode' : 'Switch to dark mode'"
          >
            <span v-if="!isDarkTheme" class="text-xl">🌙</span>
            <span v-else class="text-xl">☀️</span>
          </button>

          <!-- Mobile Menu Button -->
          <button
            @click="isOpen = !isOpen"
            class="p-2 rounded-lg hover:bg-accent transition-colors cursor-pointer"
          >
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
          </button>
        </div>
      </div>

      <!-- Mobile Menu Content -->
      <div
        v-if="isOpen"
        :class="[
          'lg:hidden transition-all duration-300',
          isDarkTheme
            ? 'bg-gray-900 border-t border-gray-800'
            : 'bg-background border-t border-border'
        ]"
      >
        <div class="py-4 space-y-2">
          <!-- Navigation Items -->
          <button
            v-for="{ href, label, icon } in navigationItems"
            :key="label"
            @click="navigateTo(href)"
            :class="[
              'flex items-center gap-3 w-full px-4 py-3 text-sm font-medium transition-colors cursor-pointer',
              isDarkTheme
                ? 'text-gray-300 hover:text-white hover:bg-gray-800'
                : 'text-foreground/80 hover:text-foreground hover:bg-accent'
            ]"
          >
            <span>{{ icon }}</span>
            {{ label }}
          </button>

          <div :class="['pt-4 border-t', isDarkTheme ? 'border-gray-800' : 'border-border']">
            <!-- User Info Mobile -->
            <div class="px-4 py-3 space-y-2">
              <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center text-white font-bold">
                  {{ user.name.charAt(0).toUpperCase() }}
                </div>
                <div>
                  <p class="text-sm font-medium">{{ user.name }}</p>
                  <p class="text-xs text-muted-foreground">{{ user.email }}</p>
                </div>
              </div>
              
              <div v-if="isCustomer">
                <Badge variant="secondary" class="text-xs">VIP Member</Badge>
              </div>
              <div v-else-if="isBusinessOwner">
                <Badge variant="secondary" class="text-xs">Business Owner</Badge>
              </div>
              <div v-else-if="isSuperAdmin">
                <Badge variant="secondary" class="text-xs">Super Admin</Badge>
              </div>
            </div>

            <!-- Mobile Actions -->
            <div class="space-y-2">
              <button
                @click="navigateTo('/profile')"
                :class="[
                  'flex items-center gap-3 w-full px-4 py-3 text-sm transition-colors cursor-pointer',
                  isDarkTheme
                    ? 'text-gray-300 hover:text-white hover:bg-gray-800'
                    : 'text-foreground/80 hover:text-foreground hover:bg-accent'
                ]"
              >
                <span>👤</span>
                Profile Settings
              </button>
              
              <button
                @click="logout"
                :class="[
                  'flex items-center gap-3 w-full px-4 py-3 text-sm transition-colors cursor-pointer',
                  isDarkTheme
                    ? 'text-red-400 hover:text-red-300 hover:bg-gray-800'
                    : 'text-red-600 hover:text-red-700 hover:bg-accent'
                ]"
              >
                <span>🚪</span>
                Logout
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </header>
</template>
