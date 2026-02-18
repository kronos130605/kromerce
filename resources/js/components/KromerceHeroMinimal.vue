<script setup>
import Button from '@/components/ui/Button.vue';
import Badge from '@/components/ui/Badge.vue';

const stats = [
  { number: '10K+', label: 'Negocios Activos' },
  { number: '50+', label: 'Países' },
  { number: '99.9%', label: 'Uptime' },
  { number: '24/7', label: 'Soporte' }
];
</script>

<template>
  <section class="relative py-20 md:py-32 overflow-hidden">
    <!-- Background Gradient -->
    <div class="absolute inset-0 bg-gradient-to-br from-blue-50 via-white to-cyan-50 dark:from-blue-950/20 dark:via-background dark:to-cyan-950/20" />
    
    <!-- Floating Elements -->
    <div class="absolute top-20 left-10 w-20 h-20 bg-blue-500/10 rounded-full blur-xl animate-pulse" />
    <div class="absolute top-40 right-20 w-32 h-32 bg-cyan-500/10 rounded-full blur-xl animate-pulse delay-1000" />
    <div class="absolute bottom-20 left-1/4 w-24 h-24 bg-blue-600/10 rounded-full blur-xl animate-pulse delay-500" />

    <div class="relative container mx-auto px-4">
      <div class="text-center max-w-4xl mx-auto">
        <!-- Badge -->
        <Badge variant="outline" class="text-sm py-2 mb-8">
          <span class="mr-2 text-blue-600">🚀</span>
          <span>Plataforma para negocios</span>
        </Badge>

        <!-- Main Title -->
        <h1 class="text-5xl md:text-7xl font-bold mb-6">
          <span class="text-foreground">Vende al mundo con</span>
          <br />
          <span class="text-transparent bg-gradient-to-r from-blue-600 to-cyan-500 bg-clip-text">
            Kromerce
          </span>
        </h1>

        <!-- Description -->
        <p class="text-xl md:text-2xl text-muted-foreground mb-12 max-w-2xl mx-auto leading-relaxed">
          La plataforma e-commerce diseñada para negocios de todos los tamaños. 
          Acepta pagos internacionales y locales, sin importar dónde estés.
        </p>

        <!-- CTA Buttons -->
        <div class="flex flex-col sm:flex-row gap-4 justify-center mb-16">
          <Button class="px-8 py-4 text-lg font-semibold">
            Comenzar Gratis
          </Button>
          <Button variant="secondary" class="px-8 py-4 text-lg font-semibold">
            Ver Demo
          </Button>
        </div>

        <!-- Stats Pills -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6 max-w-2xl mx-auto">
          <div
            v-for="stat in stats"
            :key="stat.label"
            class="text-center"
          >
            <div class="text-2xl md:text-3xl font-bold text-primary mb-1">
              {{ stat.number }}
            </div>
            <div class="text-sm text-muted-foreground">
              {{ stat.label }}
            </div>
          </div>
        </div>

        <!-- Trust Indicators -->
        <div class="flex flex-wrap justify-center items-center gap-8 mt-12 text-sm text-muted-foreground">
          <div class="flex items-center gap-2">
            <svg class="w-4 h-4 text-green-600" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
            </svg>
            <span>Sin comisiones de setup</span>
          </div>
          <div class="flex items-center gap-2">
            <svg class="w-4 h-4 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
            </svg>
            <span>Pagos globales</span>
          </div>
          <div class="flex items-center gap-2">
            <svg class="w-4 h-4 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
            </svg>
            <span>Soporte 24/7</span>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>
