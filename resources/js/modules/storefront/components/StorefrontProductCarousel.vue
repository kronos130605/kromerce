<script setup>
import { ref, computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import ProductCard from '@/modules/storefront/components/ProductCard.vue';

const props = defineProps({
    products: {
        type: Array,
        default: () => [],
    },
    showStore: {
        type: Boolean,
        default: true,
    },
    maxItems: {
        type: Number,
        default: 10,
    },
    viewAllLink: {
        type: String,
        default: '/marketplace/products',
    },
    viewAllText: {
        type: String,
        default: 'Ver todos los productos',
    },
    carouselClass: {
        type: String,
        default: '',
    },
});

const emit = defineEmits(['quick-view']);

const carousel = ref(null);
const scrollLeft = ref(0);

const displayProducts = computed(() => {
    return props.products.slice(0, props.maxItems);
});

const hasMoreProducts = computed(() => {
    return props.products.length > props.maxItems;
});

const canScrollLeft = computed(() => {
    return scrollLeft.value > 10;
});

const canScrollRight = computed(() => {
    if (!carousel.value) return displayProducts.value.length > 3;
    const { scrollWidth, clientWidth } = carousel.value;
    return scrollLeft.value < scrollWidth - clientWidth - 10;
});

const scrollCarousel = (direction) => {
    if (!carousel.value) return;
    const cardWidth = 336;
    carousel.value.scrollBy({
        left: direction * cardWidth,
        behavior: 'smooth',
    });
};

const handleScroll = () => {
    if (carousel.value) {
        scrollLeft.value = carousel.value.scrollLeft;
    }
};
</script>

<template>
    <div v-if="products?.length > 0" class="relative">
        <!-- Left Arrow -->
        <button
            @click="scrollCarousel(-1)"
            :disabled="!canScrollLeft"
            :class="[
                'absolute left-0 top-1/2 -translate-y-1/2 z-10 flex items-center justify-center rounded-full shadow-lg transition-all',
                canScrollLeft
                    ? 'bg-white text-gray-700 hover:bg-gray-50 hover:scale-105 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700'
                    : 'bg-gray-100 text-gray-300 cursor-not-allowed dark:bg-gray-900 dark:text-gray-600',
                'h-8 w-8',
            ]"
        >
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </button>

        <!-- Right Arrow -->
        <button
            @click="scrollCarousel(1)"
            :disabled="!canScrollRight"
            :class="[
                'absolute right-0 top-1/2 -translate-y-1/2 z-10 flex items-center justify-center rounded-full shadow-lg transition-all',
                canScrollRight
                    ? 'bg-white text-gray-700 hover:bg-gray-50 hover:scale-105 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700'
                    : 'bg-gray-100 text-gray-300 cursor-not-allowed dark:bg-gray-900 dark:text-gray-600',
                'h-8 w-8',
            ]"
        >
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
        </button>

        <!-- Carousel -->
        <div
            ref="carousel"
            @scroll="handleScroll"
            :class="[
                'flex gap-4 overflow-x-auto ml-9 pb-4 pl-12 pr-12 md:pl-16 md:pr-16 snap-x snap-mandatory scroll-smooth [scrollbar-width:none] [-ms-overflow-style:none] [&::-webkit-scrollbar]:hidden',
                carouselClass,
            ]"
        >
            <ProductCard
                v-for="(product, index) in displayProducts"
                :key="product.id"
                :product="product"
                :show-store="showStore"
                @quick-view="(p) => emit('quick-view', p)"
            />

            <!-- View All Card -->
            <Link
                v-if="hasMoreProducts"
                :href="viewAllLink"
                class="group flex h-full min-w-[280px] w-[280px] flex-shrink-0 snap-start flex-col items-center justify-center gap-4 rounded-[1.35rem] border-2 border-dashed border-gray-300 bg-gray-50/50 p-8 transition-all hover:border-blue-400 hover:bg-blue-50/50 dark:border-gray-600 dark:bg-gray-800/50 dark:hover:border-blue-500 dark:hover:bg-blue-900/20 pr-4 md:pr-8"
            >
                <div class="flex h-16 w-16 items-center justify-center rounded-full bg-blue-100 text-blue-600 transition-all group-hover:scale-110 group-hover:bg-blue-200 dark:bg-blue-900/30 dark:text-blue-400">
                    <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                    </svg>
                </div>
                <div class="text-center">
                    <p class="font-semibold text-gray-900 dark:text-white">{{ viewAllText }}</p>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        {{ products.length }} productos disponibles
                    </p>
                </div>
            </Link>
        </div>
    </div>
</template>
