<script setup>
import KromerceNavbar from './components/navigation/navbars/KromerceNavbar.vue';
import KromerceHeroTest from './modules/marketing/components/hero/KromerceHeroTest.vue';
import KromerceFeatures from './modules/marketing/components/features/KromerceFeatures.vue';
import KromerceStats from './modules/marketing/components/stats/KromerceStats.vue';
import KromerceTestimonials from './modules/marketing/components/testimonials/KromerceTestimonials.vue';
import KromercePricing from './modules/marketing/components/pricing/KromercePricing.vue';
import KromerceCTA from './modules/marketing/components/cta/KromerceCTA.vue';
import KromerceFooter from './modules/marketing/components/footers/KromerceFooter.vue';
</script>

<template>
  <div class="min-h-screen bg-background">
    <KromerceNavbar />
    <main class="pt-16">
      <KromerceHeroTest />
      <KromerceFeatures />
      <KromerceStats />
      <KromerceTestimonials />
      <KromercePricing />
      <KromerceCTA />
    </main>
    <KromerceFooter />
  </div>
</template>
