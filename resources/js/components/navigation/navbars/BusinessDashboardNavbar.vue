<script setup>
import { ref, onMounted, onBeforeUnmount } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import { useAuth } from '@/composables/useAuth.js';
import { useDarkMode } from '@/composables/useDarkMode.js';
import { useNavigation } from '@/composables/useNavigation.js';
import Badge from '@/components/ui/data-display/Badge.vue';
import LanguageSelector from '@/components/shared/LanguageSelector.vue';
import Icon from '@/components/ui/data-display/Icon.vue';

const { t } = useI18n();

// Use auth composable
const {
    user,
    currentStore,
    isBusinessOwner,
    isSuperAdmin,
    displayName,
    userInitials,
    userAvatar,
} = useAuth();

// Use dark mode composable
const { isDark, toggleDarkMode } = useDarkMode();

const isOpen = ref(false);
const showUserDropdown = ref(false);
const userDropdownRef = ref(null);

let onDocumentClick;

const navigateTo = (href) => {
  router.visit(href);
  isOpen.value = false;
};

const logout = () => {
  router.post('/logout');
  showUserDropdown.value = false;
};

// Use navigation composable
const { navigationItems } = useNavigation();

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
            <Icon
              :name="isOpen ? 'close' : 'menu'"
              category="ui"
              class="h-6 w-6"
            />
          </button>

          <!-- Logo -->
          <Link href="/public" class="flex items-center ml-4 lg:ml-0 group cursor-pointer">
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
            v-for="{ href, label, name } in navigationItems"
            :key="label"
            :href="href"
            :class="[
              'flex items-center gap-2 px-3 py-2 rounded-lg text-sm font-medium transition-colors',
              isDark
                ? 'text-gray-300 hover:text-white hover:bg-gray-800'
                : 'text-foreground hover:text-foreground hover:bg-accent'
            ]"
          >
            <Icon
              :name="name"
              category="business"
              class="w-5 h-5"
            />
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
            <Icon
              :name="isDark ? 'moon' : 'sun'"
              category="ui"
              class="h-5 w-5"
            />
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
            <Icon
              name="bell"
              category="ui"
              class="h-5 w-5"
            />
            <span class="absolute top-1 right-1 h-2 w-2 bg-red-500 rounded-full"></span>
          </button>

          <!-- User Dropdown -->
          <div class="relative">
            <button
              @click.stop="showUserDropdown = !showUserDropdown"
              :class="[
                'flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors cursor-pointer',
                isDark
                  ? 'text-gray-300 hover:text-white hover:bg-gray-800'
                  : 'text-foreground hover:text-foreground hover:bg-accent'
              ]"
            >
              <!-- Avatar fallback with initials -->
              <span
                v-if="!user?.avatar"
                class="w-8 h-8 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center text-white text-sm font-bold"
              >
                {{ userInitials }}
              </span>
              <img
                v-else
                :src="userAvatar"
                :alt="displayName"
                class="h-8 w-8 rounded-full object-cover"
              />
              <span class="hidden md:block">{{ displayName }}</span>
              <Icon
                name="chevronDown"
                category="ui"
                class="w-4 h-4 transition-transform"
                :class="{ 'rotate-180': showUserDropdown }"
              />
            </button>

            <!-- Dropdown Menu -->
            <div
              v-if="showUserDropdown"
              ref="userDropdownRef"
              :class="[
                'absolute right-0 top-full mt-2 w-56 rounded-lg border shadow-lg py-2 z-50',
                isDark
                  ? 'bg-gray-900 border-gray-800'
                  : 'bg-background border-border'
              ]"
            >
              <!-- User Info -->
              <div class="px-4 py-3 border-b" :class="isDark ? 'border-gray-800' : 'border-border'">
                <p class="text-sm font-medium">{{ displayName }}</p>
                <p class="text-xs text-muted-foreground">{{ user.email }}</p>
                <div v-if="isBusinessOwner" class="mt-2">
                  <Badge variant="secondary" class="text-xs">{{ t('roles.business_owner') }}</Badge>
                </div>
                <div v-else-if="isSuperAdmin" class="mt-2">
                  <Badge variant="secondary" class="text-xs">{{ t('roles.super_admin') }}</Badge>
                </div>
              </div>

              <!-- Menu Items -->
              <div class="py-2">
                <Link
                  href="/profile"
                  :class="[
                    'flex items-center gap-3 px-4 py-2 text-sm transition-colors',
                    isDark
                      ? 'text-gray-300 hover:text-white hover:bg-gray-800'
                      : 'text-foreground hover:text-foreground hover:bg-accent'
                  ]"
                >
                  <Icon
                    name="user"
                    category="ui"
                    class="h-4 w-4"
                  />
                  {{ t('profile.profile_settings') }}
                </Link>

                <Link
                  href="#billing"
                  :class="[
                    'flex items-center gap-3 px-4 py-2 text-sm transition-colors',
                    isDark
                      ? 'text-gray-300 hover:text-white hover:bg-gray-800'
                      : 'text-foreground hover:text-foreground hover:bg-accent'
                  ]"
                >
                  <Icon
                    name="reports"
                    category="business"
                    class="h-4 w-4"
                  />
                  {{ t('profile.billing') }}
                </Link>
              </div>

              <!-- Logout -->
              <div class="pt-2 border-t" :class="isDark ? 'border-gray-800' : 'border-border'">
                <button
                  @click="logout"
                  :class="[
                    'flex items-center gap-3 w-full px-4 py-2 text-sm transition-colors text-left',
                    isDark
                      ? 'text-red-400 hover:text-red-300 hover:bg-gray-800'
                      : 'text-red-600 hover:text-red-700 hover:bg-accent'
                  ]"
                >
                  <Icon
                    name="logout"
                    category="ui"
                    class="h-4 w-4"
                  />
                  {{ t('common.logout') }}
                </button>
              </div>
            </div>
          </div>
        </div>

      <!-- Mobile Menu Content -->
      <div
        v-if="isOpen"
        :class="[
          'lg:hidden transition-all duration-300',
          isDark
            ? 'bg-gray-900 border-t border-gray-800'
            : 'bg-background border-t border-border'
        ]"
      >
        <div class="py-4 space-y-2">
          <!-- Navigation Items -->
          <button
            v-for="{ href, label, name } in navigationItems"
            :key="label"
            @click="navigateTo(href)"
            :class="[
              'flex items-center gap-3 w-full px-4 py-3 text-sm font-medium transition-colors cursor-pointer',
              isDark
                ? 'text-gray-300 hover:text-white hover:bg-gray-800'
                : 'text-foreground/80 hover:text-foreground hover:bg-accent'
            ]"
          >
            <Icon
              :name="name"
              category="business"
              class="w-5 h-5"
            />
            {{ t(label) }}
          </button>

          <div :class="['pt-4 border-t', isDark ? 'border-gray-800' : 'border-border']">
            <!-- User Info Mobile -->
            <div class="px-4 py-3 space-y-2">
              <div class="flex items-center gap-3">
                <!-- Avatar fallback with initials -->
                <div
                  v-if="!user?.avatar"
                  class="w-10 h-10 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center text-white font-bold"
                >
                  {{ userInitials }}
                </div>
                <img
                  v-else
                  :src="userAvatar"
                  :alt="displayName"
                  class="h-10 w-10 rounded-full object-cover"
                />
                <div>
                  <p class="text-sm font-medium">{{ displayName }}</p>
                  <p class="text-xs text-muted-foreground">{{ user.email }}</p>
                </div>
              </div>

              <div v-if="isBusinessOwner">
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
                  isDark
                    ? 'text-gray-300 hover:text-white hover:bg-gray-800'
                    : 'text-foreground/80 hover:text-foreground hover:bg-accent'
                ]"
              >
                <Icon
                  name="user"
                  category="ui"
                  class="h-4 w-4"
                />
                {{ t('dashboard.profile_settings') }}
              </button>

              <button
                @click="logout"
                :class="[
                  'flex items-center gap-3 w-full px-4 py-3 text-sm transition-colors cursor-pointer',
                  isDark
                    ? 'text-red-400 hover:text-red-300 hover:bg-gray-800'
                    : 'text-red-600 hover:text-red-700 hover:bg-accent'
                ]"
              >
                <Icon
                  name="logout"
                  category="ui"
                  class="h-4 w-4"
                />
                {{ t('dashboard.logout') }}
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
    </div>
  </header>
</template>
