<script setup>
import { ref } from 'vue';
import ProductCard from '@/modules/storefront/components/ProductCard.vue';

const props = defineProps({
    products: {
        type: Array,
        default: () => [],
    },
    showArrows: {
        type: Boolean,
        default: true,
    },
    arrowVariant: {
        type: String,
        default: 'default',
    },
    showStore: {
        type: Boolean,
        default: true,
    },
    scrollStep: {
        type: Number,
        default: 336,
    },
    arrowsClass: {
        type: String,
        default: '',
    },
    carouselClass: {
        type: String,
        default: '',
    },
});

const emit = defineEmits(['quick-view', 'details-view']);

const carousel = ref(null);

const arrowButtonClass = {
    default: 'flex h-10 w-10 items-center justify-center rounded-full border border-gray-200 bg-white text-gray-600 transition-colors hover:border-blue-300 hover:text-blue-600 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:hover:border-blue-500 dark:hover:text-blue-400',
    light: 'flex h-10 w-10 items-center justify-center rounded-full border border-white/20 bg-white/10 text-white transition-colors hover:bg-white/20',
};

const scrollCarousel = (direction) => {
    if (!carousel.value) return;
    carousel.value.scrollBy({
        left: direction * props.scrollStep,
        behavior: 'smooth',
    });
};
</script>

<template>
    <div v-if="products?.length > 0" class="relative">
        <div v-if="showArrows" :class="['hidden items-center gap-2 md:flex', arrowsClass]">
            <button
                @click="scrollCarousel(-1)"
                :class="arrowButtonClass[arrowVariant] || arrowButtonClass.default"
            >
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </button>
            <button
                @click="scrollCarousel(1)"
                :class="arrowButtonClass[arrowVariant] || arrowButtonClass.default"
            >
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </button>
        </div>

        <div
            ref="carousel"
            :class="[
                'flex gap-4 overflow-x-auto pb-4 snap-x snap-mandatory scroll-smooth [scrollbar-width:none] [-ms-overflow-style:none] [&::-webkit-scrollbar]:hidden',
                carouselClass,
            ]"
        >
            <ProductCard
                v-for="product in products"
                :key="product.id"
                :product="product"
                :show-store="showStore"
                @quick-view="(p) => emit('quick-view', p)"
                @details-view="(p) => emit('details-view', p)"
            />
        </div>
    </div>
</template>
