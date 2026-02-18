<script setup>
import { ref } from 'vue';
import KromerceNavbar from './components/KromerceNavbar.vue';
import KromerceHero from './components/KromerceHero.vue';
import KromerceFeatures from './components/KromerceFeatures.vue';
import KromerceStats from './components/KromerceStats.vue';
import KromerceTestimonials from './components/KromerceTestimonials.vue';
import KromercePricing from './components/KromercePricing.vue';
import KromerceCTA from './components/KromerceCTA.vue';
import KromerceFooter from './components/KromerceFooter.vue';
</script>

<template>
  <div class="min-h-screen bg-background">
    <KromerceNavbar />
    <main class="pt-16">
      <KromerceHero />
      <KromerceFeatures />
      <KromerceStats />
      <KromerceTestimonials />
      <KromercePricing />
      <KromerceCTA />
    </main>
    <KromerceFooter />
  </div>
</template>
