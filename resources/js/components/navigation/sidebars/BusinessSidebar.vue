<template>
  <aside class="w-64 bg-background dark:bg-gray-900 border-r border-border dark:border-gray-800 min-h-screen">
    <nav class="p-4 space-y-2">
      <Link
        v-for="item in navigation"
        :key="item.href"
        :href="item.href"
        class="flex items-center space-x-3 px-4 py-3 rounded-lg transition-colors"
        :class="isActive(item.href) ? 'bg-accent text-accent-foreground' : 'text-muted-foreground hover:bg-accent hover:text-accent-foreground'"
      >
        <span class="text-xl">{{ item.icon }}</span>
        <span class="font-medium">{{ item.label }}</span>
        <span 
          v-if="item.badge" 
          class="ml-auto px-2 py-1 text-xs rounded-full"
          :class="getBadgeClass(item.badge.type)"
        >
          {{ item.badge.text }}
        </span>
      </Link>
    </nav>
  </aside>
</template>

<script setup>
import { Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const page = usePage();

const navigation = computed(() => [
  { 
    href: '/dashboard', 
    label: 'Dashboard', 
    icon: '📊',
    badge: null
  },
  { 
    href: '/test-products', 
    label: 'Products', 
    icon: '📦',
    badge: { type: 'success', text: '0 items' }
  },
  { 
    href: '#orders', 
    label: 'Orders', 
    icon: '🛒',
    badge: { type: 'warning', text: '0 new' }
  },
  { 
    href: '#analytics', 
    label: 'Analytics', 
    icon: '📈',
    badge: null
  },
  { 
    href: '#settings', 
    label: 'Settings', 
    icon: '⚙️',
    badge: null
  },
]);

const isActive = (href) => {
  return page.url.startsWith(href);
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
