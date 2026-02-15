<script setup lang="ts">
import { ref } from 'vue';

import { Button } from '@/components/ui/Button';
import { Badge } from '@/components/ui/Badge';

interface RouteProps {
  href: string;
  label: string;
}

const routeList: RouteProps[] = [
  {
    href: "#products",
    label: "Productos",
  },
  {
    href: "#services",
    label: "Servicios",
  },
  {
    href: "#pricing",
    label: "Planes",
  },
  {
    href: "#contact",
    label: "Contacto",
  },
];

const isOpen = ref<boolean>(false);
</script>

<template>
  <header class="w-[90%] md:w-[70%] lg:w-[75%] lg:max-w-screen-xl top-5 mx-auto sticky border z-40 rounded-2xl flex justify-between items-center p-2 bg-card shadow-md">
    <!-- Logo -->
    <a href="/" class="font-bold text-lg flex items-center group">
      <div class="mr-3 transition-transform duration-300 group-hover:scale-110">
        <img src="/resources/svg/logos/kromerce-logo-horizontal.svg" alt="Kromerce" class="h-10 w-auto" />
      </div>
    </a>

    <!-- Mobile Menu -->
    <div class="flex items-center lg:hidden">
      <Button @click="isOpen = !isOpen" variant="ghost" size="icon">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
        </svg>
      </Button>
    </div>

    <!-- Mobile Menu Content -->
    <div v-if="isOpen" class="absolute top-full left-0 right-0 bg-card border rounded-b-2xl p-4 lg:hidden">
      <div class="flex flex-col gap-2">
        <Button
          v-for="{ href, label } in routeList"
          :key="label"
          as-child
          variant="ghost"
          class="justify-start text-base"
        >
          <a @click="isOpen = false" :href="href">
            {{ label }}
          </a>
        </Button>
      </div>
      <div class="mt-4 pt-4 border-t">
        <Button class="w-full">
          Comenzar Gratis
        </Button>
      </div>
    </div>

    <!-- Desktop Menu -->
    <nav class="hidden lg:flex items-center gap-6">
      <a
        v-for="{ href, label } in routeList"
        :key="label"
        :href="href"
        class="text-sm font-medium hover:text-primary transition-colors"
      >
        {{ label }}
      </a>
    </nav>

    <!-- Desktop CTA -->
    <div class="hidden lg:flex items-center gap-4">
      <Badge variant="secondary" class="text-xs">
        Nuevo
      </Badge>
      <Button>
        Comenzar Gratis
      </Button>
    </div>
  </header>
</template>
