<script setup>
import { Link } from '@inertiajs/vue3';
import StorefrontLayout from '@/layouts/StorefrontLayout.vue';
import { useTranslations } from '@/composables/useTranslations';

const { t } = useTranslations();
useTranslations('storefront');

defineProps({
    store: { type: Object, required: true },
});
</script>

<template>
    <StorefrontLayout>
        <!-- Store Mini Header -->
        <div class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 shadow-sm">
            <div class="max-w-7xl mx-auto px-4 py-4 flex items-center gap-4">
                <Link :href="`/marketplace/stores/${store.slug}`" class="h-12 w-12 flex-shrink-0 overflow-hidden rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 shadow-sm">
                    <img v-if="store.logo" :src="store.logo" :alt="store.name" class="h-full w-full object-cover" />
                    <div v-else class="flex h-full w-full items-center justify-center bg-gradient-to-br from-blue-600 to-purple-600">
                        <span class="text-lg font-bold text-white">{{ store.name?.charAt(0) }}</span>
                    </div>
                </Link>
                <div class="flex-1 min-w-0">
                    <Link :href="`/marketplace/stores/${store.slug}`" class="font-bold text-gray-900 dark:text-white hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
                        {{ store.name }}
                    </Link>
                </div>
            </div>

            <!-- Navigation Tabs -->
            <div class="max-w-7xl mx-auto px-4">
                <nav class="flex gap-0">
                    <Link :href="`/marketplace/stores/${store.slug}`" class="border-b-2 border-transparent px-6 py-3 text-sm font-medium text-gray-600 hover:text-gray-900 hover:border-gray-300 transition-colors dark:text-gray-400 dark:hover:text-white">
                        Home
                    </Link>
                    <Link :href="`/marketplace/stores/${store.slug}/products`" class="border-b-2 border-transparent px-6 py-3 text-sm font-medium text-gray-600 hover:text-gray-900 hover:border-gray-300 transition-colors dark:text-gray-400 dark:hover:text-white">
                        Products
                    </Link>
                    <Link :href="`/marketplace/stores/${store.slug}/about`" class="border-b-2 border-blue-600 px-6 py-3 text-sm font-semibold text-blue-600 dark:text-blue-400">
                        About
                    </Link>
                </nav>
            </div>
        </div>

        <div class="max-w-4xl mx-auto px-4 py-10 space-y-8">

            <!-- Main Info Card -->
            <div class="rounded-3xl border border-gray-100 bg-white p-8 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                <div class="flex items-center gap-5 mb-6">
                    <div class="h-20 w-20 flex-shrink-0 overflow-hidden rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 shadow">
                        <img v-if="store.logo" :src="store.logo" :alt="store.name" class="h-full w-full object-cover" />
                        <div v-else class="flex h-full w-full items-center justify-center bg-gradient-to-br from-blue-600 to-purple-600">
                            <span class="text-2xl font-bold text-white">{{ store.name?.charAt(0) }}</span>
                        </div>
                    </div>
                    <div>
                        <div class="flex flex-wrap items-center gap-2 mb-1">
                            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ store.name }}</h1>
                            <span v-if="store.verified" class="inline-flex items-center gap-1 rounded-full bg-blue-100 px-2.5 py-0.5 text-xs font-semibold text-blue-700 dark:bg-blue-900/40 dark:text-blue-300">
                                <svg class="w-3 h-3 fill-current" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                {{ t('storefront.store.verified') }}
                            </span>
                        </div>
                        <p v-if="store.business_type" class="text-sm text-gray-500 dark:text-gray-400 capitalize">{{ store.business_type }} Store</p>
                    </div>
                </div>

                <div v-if="store.description" class="mb-6">
                    <h2 class="mb-3 text-sm font-semibold uppercase tracking-widest text-gray-400 dark:text-gray-500">About</h2>
                    <p class="text-gray-700 dark:text-gray-300 leading-7 whitespace-pre-line">{{ store.description }}</p>
                </div>

                <!-- Details Grid -->
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div v-if="store.country" class="flex items-center gap-3 rounded-2xl border border-gray-100 p-4 dark:border-gray-700">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-50 dark:bg-blue-900/30">
                            <svg class="h-5 w-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        </div>
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wide text-gray-400 dark:text-gray-500">Location</p>
                            <p class="text-sm font-medium text-gray-800 dark:text-gray-200">{{ store.country }}</p>
                        </div>
                    </div>

                    <div v-if="store.email" class="flex items-center gap-3 rounded-2xl border border-gray-100 p-4 dark:border-gray-700">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-indigo-50 dark:bg-indigo-900/30">
                            <svg class="h-5 w-5 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        </div>
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wide text-gray-400 dark:text-gray-500">Email</p>
                            <a :href="`mailto:${store.email}`" class="text-sm font-medium text-indigo-600 dark:text-indigo-400 hover:underline">{{ store.email }}</a>
                        </div>
                    </div>

                    <div v-if="store.phone" class="flex items-center gap-3 rounded-2xl border border-gray-100 p-4 dark:border-gray-700">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-50 dark:bg-emerald-900/30">
                            <svg class="h-5 w-5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                        </div>
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wide text-gray-400 dark:text-gray-500">Phone</p>
                            <p class="text-sm font-medium text-gray-800 dark:text-gray-200">{{ store.phone }}</p>
                        </div>
                    </div>

                    <div v-if="store.website" class="flex items-center gap-3 rounded-2xl border border-gray-100 p-4 dark:border-gray-700">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-purple-50 dark:bg-purple-900/30">
                            <svg class="h-5 w-5 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/></svg>
                        </div>
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wide text-gray-400 dark:text-gray-500">Website</p>
                            <a :href="store.website" target="_blank" rel="noopener" class="text-sm font-medium text-purple-600 dark:text-purple-400 hover:underline truncate block max-w-[160px]">{{ store.website }}</a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- CTA -->
            <div class="rounded-3xl border border-blue-100 bg-gradient-to-r from-blue-50 to-indigo-50 p-8 text-center dark:border-blue-900/30 dark:from-blue-950/30 dark:to-indigo-950/30">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Browse products from {{ store.name }}</h3>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-5">Discover our full catalog of products</p>
                <Link
                    :href="`/marketplace/stores/${store.slug}/products`"
                    class="inline-flex items-center gap-2 rounded-2xl bg-blue-600 px-6 py-3 text-sm font-semibold text-white hover:bg-blue-700 transition-colors"
                >
                    {{ t('storefront.store.visit_store') }}
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </Link>
            </div>
        </div>
    </StorefrontLayout>
</template>
