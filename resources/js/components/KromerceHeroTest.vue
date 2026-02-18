<script setup>
import { ref } from 'vue';
import Button from '@/components/ui/Button.vue';
import Badge from '@/components/ui/Badge.vue';
import KromerceHeroMinimal from './KromerceHeroMinimal.vue';
import KromerceHeroReal from './KromerceHeroReal.vue';
import KromerceHeroSaaS from './KromerceHeroSaaS.vue';

const selectedHero = ref('minimal');

const heroes = {
  minimal: 'KromerceHeroMinimal',
  real: 'KromerceHeroReal', 
  saas: 'KromerceHeroSaaS'
};

const selectHero = (hero) => {
  selectedHero.value = hero;
};
</script>

<template>
  <div class="min-h-screen bg-background">
    <!-- Selector -->
    <div class="sticky top-0 z-50 bg-background border-b border-border p-4">
      <div class="container mx-auto px-4">
        <div class="flex flex-wrap gap-4 justify-center">
          <button
            v-for="(component, name) in heroes"
            :key="name"
            @click="selectHero(name)"
            :class="[
              'px-4 py-2 rounded-lg font-medium transition-colors',
              selectedHero === name
                ? 'bg-primary text-primary-foreground'
                : 'bg-muted hover:bg-accent'
            ]"
          >
            {{ name.charAt(0).toUpperCase() + name.slice(1) }}
          </button>
        </div>
      </div>
    </div>

    <!-- Hero Display -->
    <div class="relative">
      <!-- Minimal Hero -->
      <KromerceHeroMinimal v-if="selectedHero === 'minimal'" />
      
      <!-- Real Interface Hero -->
      <KromerceHeroReal v-if="selectedHero === 'real'" />
      
      <!-- SaaS Modern Hero -->
      <KromerceHeroSaaS v-if="selectedHero === 'saas'" />
    </div>

    <!-- Continue Button -->
    <div class="text-center py-8 bg-muted/50">
      <p class="text-muted-foreground mb-4">
        Selecciona tu versión favorita y luego continuaremos con el resto del sitio
      </p>
      <Button @click="$emit('heroSelected', selectedHero)" class="px-6">
        Continuar con {{ selectedHero.charAt(0).toUpperCase() + selectedHero.slice(1) }}
      </Button>
    </div>
  </div>
</template>
