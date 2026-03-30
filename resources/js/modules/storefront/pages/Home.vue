<script setup>
import StorefrontLayout from '@/layouts/StorefrontLayout.vue';
import ProductCard from '@/components/storefront/ProductCard.vue';
import StoreCard from '@/components/storefront/StoreCard.vue';
import CategoryCard from '@/components/storefront/CategoryCard.vue';
import { Link } from '@inertiajs/vue3';
import {useTranslations} from "@/composables/useTranslations.js";

const { t } = useTranslations();

defineProps({
    featured_categories: Array,
    trending_products: Array,
    new_arrivals: Array,
    top_stores: Array,
    deals_of_the_day: Array,
});
</script>

<template>
    <StorefrontLayout>
        <!-- Hero Banner -->
        <section class="bg-gradient-to-r from-blue-600 to-purple-600 text-white">
            <div class="max-w-7xl mx-auto px-4 py-16 md:py-24">
                <div class="max-w-3xl">
                    <h1 class="text-4xl md:text-6xl font-bold mb-4">
                        {{ t('storefront.hero.title') }}
                    </h1>
                    <p class="text-xl md:text-2xl mb-8 text-blue-100">
                        {{ t('storefront.hero.subtitle') }}
                    </p>
                    <div class="flex flex-col sm:flex-row gap-4">
                        <Link href="/products" class="px-8 py-3 bg-white text-blue-600 font-semibold rounded-lg hover:bg-blue-50 transition-colors text-center">
                            {{ t('storefront.hero.cta_browse') }}
                        </Link>
                        <Link href="/stores" class="px-8 py-3 border-2 border-white text-white font-semibold rounded-lg hover:bg-white/10 transition-colors text-center">
                            {{ t('storefront.hero.cta_sellers') }}
                        </Link>
                    </div>
                </div>
            </div>
        </section>

        <!-- Featured Categories -->
        <section class="max-w-7xl mx-auto px-4 py-12">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">{{ t('storefront.sections.featured_categories') }}</h2>
                <Link href="/products" class="text-blue-600 dark:text-blue-400 hover:underline text-sm font-medium">
                    {{ t('storefront.sections.view_all') }} →
                </Link>
            </div>
            <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-8 gap-4">
                <CategoryCard
                    v-for="category in featured_categories"
                    :key="category.id"
                    :category="category"
                />
            </div>
        </section>

        <!-- Trending Products -->
        <section class="bg-white dark:bg-gray-800 py-12">
            <div class="max-w-7xl mx-auto px-4">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-1">🔥 {{ t('storefront.sections.trending_products') }}</h2>
                    </div>
                    <Link href="/products?sort_by=trending" class="text-blue-600 dark:text-blue-400 hover:underline text-sm font-medium">
                        {{ t('storefront.sections.view_all') }} →
                    </Link>
                </div>
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6 gap-4">
                    <ProductCard
                        v-for="product in trending_products"
                        :key="product.id"
                        :product="product"
                    />
                </div>
            </div>
        </section>

        <!-- Deals of the Day -->
        <section v-if="deals_of_the_day?.length > 0" class="max-w-7xl mx-auto px-4 py-12">
            <div class="bg-gradient-to-r from-red-500 to-pink-500 rounded-2xl p-8 mb-6">
                <div class="flex items-center justify-between text-white">
                    <div>
                        <h2 class="text-3xl font-bold mb-2">⚡ {{ t('storefront.sections.deals_of_the_day') }}</h2>
                    </div>
                    <div class="hidden md:block text-right">
                        <div class="text-sm text-red-100 mb-1">Ends in</div>
                        <div class="text-2xl font-bold">23:59:45</div>
                    </div>
                </div>
            </div>
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6 gap-4">
                <ProductCard
                    v-for="product in deals_of_the_day"
                    :key="product.id"
                    :product="product"
                />
            </div>
        </section>

        <!-- New Arrivals -->
        <section class="bg-gray-50 dark:bg-gray-900 py-12">
            <div class="max-w-7xl mx-auto px-4">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-1">✨ {{ t('storefront.sections.new_arrivals') }}</h2>
                    </div>
                    <Link href="/products?sort_by=created_at&sort_order=desc" class="text-blue-600 dark:text-blue-400 hover:underline text-sm font-medium">
                        {{ t('storefront.sections.view_all') }} →
                    </Link>
                </div>
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6 gap-4">
                    <ProductCard
                        v-for="product in new_arrivals"
                        :key="product.id"
                        :product="product"
                    />
                </div>
            </div>
        </section>

        <!-- Top Stores -->
        <section class="max-w-7xl mx-auto px-4 py-12">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-1">🏆 {{ t('storefront.sections.top_stores') }}</h2>
                </div>
                <Link href="/stores" class="text-blue-600 dark:text-blue-400 hover:underline text-sm font-medium">
                    {{ t('storefront.sections.view_all') }} →
                </Link>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <StoreCard
                    v-for="store in top_stores"
                    :key="store.id"
                    :store="store"
                />
            </div>
        </section>

        <!-- Features Banner -->
        <section class="bg-blue-50 dark:bg-gray-800 py-12">
            <div class="max-w-7xl mx-auto px-4">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <div class="text-center">
                        <div class="w-16 h-16 bg-blue-600 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">{{ t('storefront.features.quality_guarantee.title') }}</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400">{{ t('storefront.features.quality_guarantee.description') }}</p>
                    </div>
                    <div class="text-center">
                        <div class="w-16 h-16 bg-green-600 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">{{ t('storefront.features.secure_payment.title') }}</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400">{{ t('storefront.features.secure_payment.description') }}</p>
                    </div>
                    <div class="text-center">
                        <div class="w-16 h-16 bg-purple-600 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">{{ t('storefront.features.fast_shipping.title') }}</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400">{{ t('storefront.features.fast_shipping.description') }}</p>
                    </div>
                </div>
            </div>
        </section>
    </StorefrontLayout>
</template>
