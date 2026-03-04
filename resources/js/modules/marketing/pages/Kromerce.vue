<script setup>
import { Head } from '@inertiajs/vue3';

import KromerceNavbar from '@/components/navigation/navbars/KromerceNavbar.vue';
import KromerceHero from '@/modules/marketing/components/hero/KromerceHero.vue';
import KromerceFeatures from '@/modules/marketing/components/features/KromerceFeatures.vue';
import KromerceStats from '@/modules/marketing/components/stats/KromerceStats.vue';
import KromerceTestimonials from '@/modules/marketing/components/testimonials/KromerceTestimonials.vue';
import KromercePricing from '@/modules/marketing/components/pricing/KromercePricing.vue';
import KromerceCTA from '@/modules/marketing/components/cta/KromerceCTA.vue';
import KromerceFooter from '@/modules/marketing/components/footers/KromerceFooter.vue';
import PWAInstaller from '@/components/shared/PWAInstaller.vue';
</script>

<template>
    <Head title="Kromerce" />

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
        <PWAInstaller />
    </div>
</template>
