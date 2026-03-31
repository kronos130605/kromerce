<script setup>
import { Link } from '@inertiajs/vue3';
import { useTranslations } from '@/composables/useTranslations';

const { t } = useTranslations();
</script>

<template>
    <section class="relative overflow-hidden bg-gradient-to-br from-blue-700 via-blue-600 to-purple-700 text-white">
        <!-- Background blobs -->
        <div class="absolute inset-0 opacity-10 pointer-events-none">
            <div class="absolute top-0 left-1/4 w-96 h-96 bg-white rounded-full blur-3xl -translate-y-1/2"></div>
            <div class="absolute bottom-0 right-1/4 w-80 h-80 bg-purple-300 rounded-full blur-3xl translate-y-1/2"></div>
        </div>

        <div class="relative max-w-7xl mx-auto px-4 py-14 md:py-20">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-10 items-center">

                <!-- Content -->
                <div>
                    <div class="inline-flex items-center gap-2 px-3 py-1 bg-white/20 rounded-full text-sm font-medium mb-5 backdrop-blur-sm">
                        <span class="w-2 h-2 bg-emerald-400 rounded-full animate-pulse"></span>
                        {{ t('storefront.hero.badge') }}
                    </div>

                    <h1 class="text-4xl md:text-5xl font-bold mb-4 leading-tight">
                        {{ t('storefront.hero.title') }}
                    </h1>

                    <p class="text-lg md:text-xl mb-8 text-blue-100 leading-relaxed">
                        {{ t('storefront.hero.subtitle') }}
                    </p>

                    <div class="flex flex-col sm:flex-row gap-3">
                        <Link
                            href="/products"
                            class="px-7 py-3 bg-white text-blue-700 font-semibold rounded-xl hover:bg-blue-50 transition-colors text-center shadow-lg"
                        >
                            {{ t('storefront.hero.cta_browse') }}
                        </Link>
                        <Link
                            href="/stores"
                            class="px-7 py-3 border-2 border-white/60 text-white font-semibold rounded-xl hover:bg-white/10 transition-colors text-center backdrop-blur-sm"
                        >
                            {{ t('storefront.hero.cta_sellers') }}
                        </Link>
                    </div>

                    <!-- Trust Badges -->
                    <div class="flex flex-wrap items-center gap-5 mt-8 text-sm text-blue-200">
                        <span class="flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-emerald-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                            Verified Sellers
                        </span>
                        <span class="flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-emerald-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                            Secure Payments
                        </span>
                        <span class="flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-emerald-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/>
                            </svg>
                            Easy Returns
                        </span>
                    </div>
                </div>

                <!-- Decorative Visual -->
                <div class="hidden md:flex items-center justify-center">
                    <div class="relative">
                        <div class="w-64 h-64 bg-white/10 rounded-3xl backdrop-blur-sm border border-white/20 flex items-center justify-center">
                            <svg class="w-32 h-32 text-white/30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="0.8" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                            </svg>
                        </div>
                        <div class="absolute -top-4 -right-4 bg-white rounded-2xl px-4 py-2 shadow-xl text-gray-900 text-xs font-bold">
                            10K+ Products
                        </div>
                        <div class="absolute -bottom-4 -left-4 bg-emerald-500 rounded-2xl px-4 py-2 shadow-xl text-white text-xs font-bold">
                            500+ Sellers
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>
</template>
