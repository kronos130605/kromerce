<script setup>
import { useI18n } from 'vue-i18n';

const { t } = useI18n();
const features = [
  {
    icon: 'M3 13h8V3H3v10zm0 8h8v-6H3v6zm10 0h8V11h-8v10zm0-18v6h8V3h-8z',
    title: t('features.list.0.title'),
    description: t('features.list.0.description')
  },
  {
    icon: 'M12 15l-2-5L5 8l5-2 2-5 2 5 5 2-5 2-2 5zm0 0l-2-5L5 8l5-2 2-5 2 5 5 2-5 2-2 5zm0 0l-2-5L5 8l5-2 2-5 2 5 5 2-5 2-2 5z M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
    title: t('features.list.1.title'),
    description: t('features.list.1.description')
  },
  {
    icon: 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z M12 8v4l3 3',
    title: t('features.list.2.title'),
    description: t('features.list.2.description')
  },
  {
    icon: 'M13 10V3L4 14h7v7l9-11h-7z M11 17a1 1 0 100-2 1 1 0 000 2z M12 2a1 1 0 100-2 1 1 0 000 2z',
    title: t('features.list.3.title'),
    description: t('features.list.3.description')
  },
  {
    icon: 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z',
    title: t('features.list.4.title'),
    description: t('features.list.4.description')
  },
  {
    icon: 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z',
    title: t('features.list.5.title'),
    description: t('features.list.5.description')
  }
];
</script>

<template>
  <section id="features" class="py-20 bg-background">
    <div class="container mx-auto px-4">
      <!-- Header -->
      <div class="text-center mb-16">
        <h2 class="text-3xl md:text-4xl font-bold mb-4">
          {{ t('features.title') }}
          <span class="text-transparent bg-gradient-to-r from-blue-600 to-cyan-500 bg-clip-text">
            {{ t('features.title_highlight') }}
          </span>
        </h2>
        <p class="text-xl text-muted-foreground max-w-3xl mx-auto">
          {{ t('features.subtitle') }}
        </p>
      </div>

      <!-- Features Grid -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        <div
          v-for="feature in features"
          :key="feature.title"
          class="group relative p-6 rounded-xl border border-border bg-card text-card-foreground hover:shadow-lg transition-all duration-300 hover:-translate-y-1"
        >
          <!-- Icon -->
          <div class="w-12 h-12 rounded-lg flex items-center justify-center mb-4 bg-primary/10 dark:bg-blue-900/30 group-hover:bg-primary/20 transition-colors">
            <svg class="w-6 h-6 text-primary dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="feature.icon" />
            </svg>
          </div>

          <!-- Content -->
          <h3 class="text-xl font-semibold mb-2 text-foreground">
            {{ feature.title }}
          </h3>
          <p class="leading-relaxed text-muted-foreground">
            {{ feature.description }}
          </p>

          <!-- Hover Effect -->
          <div class="absolute inset-0 rounded-xl bg-gradient-to-r from-blue-600/5 to-cyan-500/5 opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none" />
        </div>
      </div>

      <!-- CTA -->
      <div class="text-center mt-12">
        <p class="text-muted-foreground mb-4">
          {{ t('features.cta.question') }}
        </p>
        <button class="px-6 py-3 bg-primary text-primary-foreground rounded-lg hover:bg-primary/90 transition-colors font-medium cursor-pointer">
          {{ t('features.cta.button') }}
        </button>
      </div>
    </div>
  </section>
</template>
