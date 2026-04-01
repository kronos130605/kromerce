<script setup>
import { ref } from 'vue';
import StorefrontLayout from '@/layouts/StorefrontLayout.vue';
import ProductDetailsModal from '@/components/storefront/ProductDetailsModal.vue';
import ProductQuickViewModal from '@/components/storefront/ProductQuickViewModal.vue';
import HeroBanner from '@/modules/storefront/components/HeroBanner.vue';
import TrustBar from '@/modules/storefront/components/TrustBar.vue';
import FeaturedCategoriesSection from '@/modules/storefront/components/FeaturedCategoriesSection.vue';
import ProductsGridSection from '@/modules/storefront/components/ProductsGridSection.vue';
import DealsSection from '@/modules/storefront/components/DealsSection.vue';
import TopStoresSection from '@/modules/storefront/components/TopStoresSection.vue';
import { useTranslations } from '@/composables/useTranslations.js';

const { t } = useTranslations();

defineProps({
    featured_categories: Array,
    trending_products: Array,
    new_arrivals: Array,
    top_stores: Array,
    deals_of_the_day: Array,
});

const quickViewProduct = ref(null);
const showQuickView = ref(false);
const detailsProduct = ref(null);
const showDetailsView = ref(false);

const openQuickView = (product) => {
    quickViewProduct.value = product;
    showQuickView.value = true;
};

const closeQuickView = () => {
    showQuickView.value = false;
    setTimeout(() => { quickViewProduct.value = null; }, 200);
};

const openDetailsView = (product) => {
    detailsProduct.value = product;
    showDetailsView.value = true;
};

const closeDetailsView = () => {
    showDetailsView.value = false;
    setTimeout(() => { detailsProduct.value = null; }, 200);
};
</script>

<template>
    <StorefrontLayout>

        <ProductDetailsModal
            :product="detailsProduct"
            :show="showDetailsView"
            @close="closeDetailsView"
        />

        <ProductQuickViewModal
            :product="quickViewProduct"
            :show="showQuickView"
            @close="closeQuickView"
            @open-details="openDetailsView"
        />

        <HeroBanner />

        <TrustBar />

        <FeaturedCategoriesSection :categories="featured_categories" />

        <ProductsGridSection
            :title="t('storefront.sections.trending_products')"
            icon="🔥"
            :products="trending_products"
            view-all-link="/products?sort_by=trending"
            bg-class="bg-white dark:bg-gray-800"
            @quick-view="openQuickView"
            @details-view="openDetailsView"
        />

        <DealsSection
            :products="deals_of_the_day"
            @quick-view="openQuickView"
            @details-view="openDetailsView"
        />

        <ProductsGridSection
            :title="t('storefront.sections.new_arrivals')"
            icon="✨"
            :products="new_arrivals"
            view-all-link="/products?sort_by=created_at&sort_order=desc"
            bg-class="bg-gray-50 dark:bg-gray-900"
            @quick-view="openQuickView"
            @details-view="openDetailsView"
        />

        <TopStoresSection :stores="top_stores" />

    </StorefrontLayout>
</template>
