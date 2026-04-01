<script setup>
import { Link } from '@inertiajs/vue3';
import StoreCard from '@/modules/storefront/components/StoreCard.vue';
import { useTranslations } from '@/composables/useTranslations';

const { t } = useTranslations();

defineProps({
    stores: {
        type: Array,
        default: () => [],
    },
});
</script>

<template>
    <section class="max-w-7xl mx-auto px-4 py-10">
        <div class="flex items-center justify-between mb-5">
            <h2 class="text-xl font-bold text-gray-900 dark:text-white flex items-center gap-2">
                <span class="text-2xl">🏆</span>
                {{ t('storefront.sections.top_stores') }}
            </h2>
            <Link
                href="/marketplace/stores"
                class="text-sm text-blue-600 dark:text-blue-400 hover:underline font-medium flex items-center gap-1"
            >
                {{ t('storefront.sections.view_all') }}
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </Link>
        </div>

        <div
            v-if="stores?.length > 0"
            class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5"
        >
            <StoreCard
                v-for="store in stores"
                :key="store.id"
                :store="store"
            />
        </div>

        <div v-else class="py-12 text-center text-gray-400 dark:text-gray-600 text-sm">
            {{ t('storefront.empty.no_stores') }}
        </div>
    </section>
</template>
