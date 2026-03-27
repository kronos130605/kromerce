<script setup>
import { ref, computed, onMounted, onBeforeUnmount } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import { useAuth } from '@/composables/useAuth.js';
import { useDarkMode } from '@/composables/useDarkMode.js';
import Button from '@/components/ui/buttons/Button.vue';
import Badge from '@/components/ui/data-display/Badge.vue';
import LanguageSelector from '@/components/shared/LanguageSelector.vue';

const { t } = useI18n();
const page = usePage();
const { user } = useAuth();
const { isDark, toggleDarkMode } = useDarkMode();

const routeList = computed(() => [
  {
    href: "#features",
    label: t('nav.features'),
  },
  {
    href: "#testimonials",
    label: t('nav.testimonials'),
  },
  {
    href: "#pricing",
    label: t('nav.pricing'),
  },
  {
    href: "#contact",
    label: t('nav.contact'),
  },
]);

const isOpen = ref(false);

const smoothScroll = (href) => {
  const element = document.querySelector(href);
  if (element) {
    element.scrollIntoView({ behavior: 'smooth' });
  }
  isOpen.value = false;
};

onMounted(() => {
  // Add ID to hero section for navigation
  const hero = document.querySelector('section');
  if (hero) {
    hero.id = 'hero';
  }
});

onBeforeUnmount(() => {
  // Cleanup if needed
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
    <div class="container mx-auto px-4">
      <div class="flex justify-between items-center h-16">
        <!-- Logo -->
        <a href="/public" class="flex items-center group cursor-pointer">
          <div class="mr-3 transition-transform duration-300 group-hover:scale-110">
            <img
              src="/images/kromerce-business-text.png"
              alt="Kromerce"
              :class="[
                'h-8 w-auto object-contain transition-transform duration-300 group-hover:scale-110',
                isDark ? 'filter brightness-0 invert' : ''
              ]"
            />
          </div>
        </a>

        <!-- Desktop Menu -->
        <nav class="hidden lg:flex items-center gap-8">
          <button
            v-for="{ href, label } in routeList"
            :key="label"
            @click="smoothScroll(href)"
            :class="[
              'font-medium transition-colors relative group cursor-pointer',
              isDark
                ? 'text-gray-300 hover:text-white'
                : 'text-foreground hover:text-foreground'
            ]"
          >
            {{ label }}
            <span
              class="absolute bottom-0 left-0 h-0.5 bg-primary transition-all duration-300 group-hover:w-full"
              style="width: 0;"
            ></span>
          </button>
        </nav>

        <!-- Desktop CTA -->
        <div class="hidden lg:flex items-center gap-4">
          <!-- Authenticated User -->
          <div v-if="user" class="flex items-center gap-4">
            <Badge
              variant="secondary"
              :class="[
                'text-xs',
                isDark
                  ? 'bg-gray-800 text-gray-300 border-gray-700'
                  : 'bg-muted text-foreground'
              ]"
            >
              {{ user.name }}
            </Badge>
            <Button variant="outline" @click="$inertia.visit('/dashboard')">
              Dashboard
            </Button>
            <Button variant="ghost" @click="$inertia.post('/logout')">
              Logout
            </Button>
          </div>

          <!-- Guest User -->
          <div v-else class="flex items-center gap-4">
            <Button variant="ghost" @click="$inertia.visit('/login')">
              {{ t('nav.login') }}
            </Button>
            <Button @click="$inertia.visit('/register')">
              {{ t('nav.register') }}
            </Button>
          </div>

          <!-- Language Selector -->
          <LanguageSelector />

          <!-- Dark Mode Toggle -->
          <button
            @click="toggleDarkMode"
            class="p-2 rounded-lg hover:bg-accent transition-colors cursor-pointer"
            :title="isDark ? t('nav.toggle_light') : t('nav.toggle_dark')"
          >
            <span v-if="!isDark" class="text-xl">🌙</span>
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
            :title="isDark ? t('nav.toggle_light') : t('nav.toggle_dark')"
          >
            <span v-if="!isDark" class="text-xl">🌙</span>
            <span v-else class="text-xl">☀️</span>
          </button>

          <Button
            @click="isOpen = !isOpen"
            variant="ghost"
            size="icon"
            class="text-foreground hover:bg-accent cursor-pointer"
          >
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
          </Button>
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
          <Button
            v-for="{ href, label } in routeList"
            :key="label"
            @click="smoothScroll(href)"
            variant="ghost"
            :class="[
              'w-full justify-start transition-colors cursor-pointer',
              isDark
                ? 'text-gray-300 hover:text-white hover:bg-gray-800'
                : 'text-foreground/80 hover:text-foreground hover:bg-accent'
            ]"
          >
            {{ label }}
          </Button>
          <div :class="['pt-4 border-t', isDark ? 'border-gray-800' : 'border-border']">
            <!-- Authenticated User Mobile -->
            <div v-if="user" class="space-y-2">
              <div class="px-2 py-1 text-sm text-muted-foreground">
                {{ user.name }}
              </div>
              <Button
                @click="$inertia.visit('/dashboard')"
                variant="outline"
                class="w-full justify-start"
              >
                Dashboard
              </Button>
              <Button
                @click="$inertia.post('/logout')"
                variant="ghost"
                class="w-full justify-start"
              >
                Logout
              </Button>
            </div>

            <!-- Guest User Mobile -->
            <div v-else class="space-y-2">
              <Button
                @click="$inertia.visit('/login')"
                variant="ghost"
                class="w-full justify-start"
              >
                {{ t('nav.login') }}
              </Button>
              <Button
                @click="$inertia.visit('/register')"
                class="w-full"
              >
                {{ t('nav.register') }}
              </Button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </header>
</template>
