<script setup>
import { ref, onMounted, onBeforeUnmount } from 'vue';
import Button from '@/components/ui/Button.vue';
import Badge from '@/components/ui/Badge.vue';

const routeList = [
  {
    href: "#features",
    label: "Características",
  },
  {
    href: "#testimonials",
    label: "Testimonios",
  },
  {
    href: "#pricing",
    label: "Precios",
  },
  {
    href: "#contact",
    label: "Contacto",
  },
];

const isOpen = ref(false);
const isDarkTheme = ref(false);

let observer;

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

const smoothScroll = (href) => {
  const element = document.querySelector(href);
  if (element) {
    element.scrollIntoView({ behavior: 'smooth' });
  }
  isOpen.value = false;
};

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

  // Add ID to hero section for navigation
  const hero = document.querySelector('section');
  if (hero) {
    hero.id = 'hero';
  }
});

onBeforeUnmount(() => {
  if (observer) {
    observer.disconnect();
    observer = undefined;
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
        <a href="/" class="flex items-center group cursor-pointer">
          <div class="mr-3 transition-transform duration-300 group-hover:scale-110">
            <img
              src="/resources/images/logos/kromerce-business-text.png"
              alt="Kromerce"
              :class="[
                'h-8 w-auto object-contain transition-transform duration-300 group-hover:scale-110',
                isDarkTheme ? 'filter brightness-0 invert' : ''
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
              isDarkTheme
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
          <Badge
            variant="secondary"
            :class="[
              'text-xs',
              isDarkTheme
                ? 'bg-gray-800 text-gray-300 border-gray-700'
                : 'bg-muted text-foreground'
            ]"
          >
            Nuevo
          </Badge>

          <!-- Dark Mode Toggle -->
          <button
            @click="toggleDarkMode"
            class="p-2 rounded-lg hover:bg-accent transition-colors cursor-pointer"
            :title="isDarkTheme ? 'Cambiar a modo claro' : 'Cambiar a modo oscuro'"
          >
            <span v-if="!isDarkTheme" class="text-xl">🌙</span>
            <span v-else class="text-xl">☀️</span>
          </button>

          <Button class="cursor-pointer">
            Comenzar Gratis
          </Button>
        </div>

        <!-- Mobile Menu -->
        <div class="flex items-center lg:hidden gap-2">
          <!-- Dark Mode Toggle Mobile -->
          <button
            @click="toggleDarkMode"
            class="p-2 rounded-lg hover:bg-accent transition-colors cursor-pointer"
            :title="isDarkTheme ? 'Cambiar a modo claro' : 'Cambiar a modo oscuro'"
          >
            <span v-if="!isDarkTheme" class="text-xl">🌙</span>
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
          isDarkTheme
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
              isDarkTheme
                ? 'text-gray-300 hover:text-white hover:bg-gray-800'
                : 'text-foreground/80 hover:text-foreground hover:bg-accent'
            ]"
          >
            {{ label }}
          </Button>
          <div :class="['pt-4 border-t', isDarkTheme ? 'border-gray-800' : 'border-border']">
            <Button
              class="w-full cursor-pointer"
            >
              Comenzar Gratis
            </Button>
          </div>
        </div>
      </div>
    </div>
  </header>
</template>
