<script setup>
import { ref, computed } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import StorefrontLayout from '@/layouts/StorefrontLayout.vue';
import StoreCard from '@/modules/storefront/components/StoreCard.vue';
import { useTranslations } from '@/composables/useTranslations';

const { t } = useTranslations();
useTranslations('storefront');

const props = defineProps({
    stores: { type: Object, required: true },
});

const searchQuery = ref('');

const storeList = computed(() => props.stores?.data || []);
const pagination = computed(() => props.stores);

const handleSearch = () => {
    router.get('/stores', { search: searchQuery.value || undefined }, { preserveState: true, replace: true });
};
</script>

<template>
    <StorefrontLayout>
        <!-- Hero -->
        <div class="bg-gradient-to-br from-indigo-600 via-purple-600 to-pink-500 dark:from-indigo-900 dark:via-purple-900 dark:to-pink-900">
            <div class="max-w-7xl mx-auto px-4 py-14 text-center">
                <h1 class="text-4xl font-bold text-white mb-3">{{ t('storefront.sections.top_stores') }}</h1>
                <p class="text-indigo-100 mb-8 max-w-xl mx-auto">Discover verified sellers and unique products from stores worldwide</p>
                <div class="max-w-md mx-auto flex overflow-hidden rounded-2xl shadow-xl">
                    <input
                        v-model="searchQuery"
                        type="text"
                        placeholder="Search stores..."
                        class="flex-1 px-5 py-3.5 text-sm bg-white text-gray-900 outline-none"
                        @keyup.enter="handleSearch"
                    />
                    <button @click="handleSearch" class="px-6 bg-indigo-700 hover:bg-indigo-800 text-white text-sm font-semibold transition-colors">
                        {{ t('storefront.navigation.search') }}
                    </button>
                </div>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 py-10">
            <!-- Stats bar -->
            <div class="mb-8 flex items-center justify-between">
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    <span class="font-semibold text-gray-900 dark:text-white">{{ pagination?.total || storeList.length }}</span> stores available
                </p>
            </div>

            <!-- Stores Grid -->
            <div v-if="storeList.length > 0" class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                <StoreCard
                    v-for="store in storeList"
                    :key="store.id"
                    :store="store"
                />
            </div>

            <div v-else class="py-24 text-center">
                <svg class="mx-auto h-16 w-16 text-gray-300 dark:text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                <p class="text-lg font-semibold text-gray-500 dark:text-gray-400">{{ t('storefront.empty.no_stores') }}</p>
            </div>

            <!-- Pagination -->
            <div v-if="pagination?.last_page > 1" class="mt-10 flex items-center justify-center gap-2">
                <Link
                    v-for="page in pagination.last_page"
                    :key="page"
                    :href="`/stores?page=${page}`"
                    :class="[
                        'flex h-10 w-10 items-center justify-center rounded-xl text-sm font-medium transition-colors',
                        page === pagination.current_page
                            ? 'bg-blue-600 text-white'
                            : 'border border-gray-200 text-gray-600 hover:border-blue-300 hover:text-blue-600 dark:border-gray-600 dark:text-gray-400'
                    ]"
                >
                    {{ page }}
                </Link>
            </div>
        </div>
    </StorefrontLayout>
</template>
