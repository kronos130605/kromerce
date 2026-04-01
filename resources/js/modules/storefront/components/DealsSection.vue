<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import { Link } from '@inertiajs/vue3';
import StorefrontProductCarousel from '@/modules/storefront/components/StorefrontProductCarousel.vue';
import { useTranslations } from '@/composables/useTranslations';

const { t } = useTranslations();

defineProps({
    products: {
        type: Array,
        default: () => [],
    },
    viewAllLink: {
        type: String,
        default: '/products?sort_by=discount',
    },
});

const emit = defineEmits(['quick-view', 'details-view']);

const hours = ref(23);
const minutes = ref(59);
const seconds = ref(45);
let timer = null;

const tick = () => {
    if (seconds.value > 0) {
        seconds.value--;
    } else if (minutes.value > 0) {
        minutes.value--;
        seconds.value = 59;
    } else if (hours.value > 0) {
        hours.value--;
        minutes.value = 59;
        seconds.value = 59;
    }
};

const pad = (n) => String(n).padStart(2, '0');

onMounted(() => { timer = setInterval(tick, 1000); });
onUnmounted(() => { clearInterval(timer); });
</script>

<template>
    <section v-if="products?.length > 0" class="py-10">
        <div class="max-w-7xl mx-auto px-4">

            <!-- Section Header -->
            <div class="bg-gradient-to-r from-red-500 to-rose-600 rounded-2xl px-6 py-4 mb-5 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <span class="text-2xl">⚡</span>
                    <h2 class="text-xl font-bold text-white">
                        {{ t('storefront.sections.deals_of_the_day') }}
                    </h2>
                </div>

                <div class="flex items-center gap-3">
                    <!-- Countdown -->
                    <div class="hidden sm:flex items-center gap-2 text-white">
                        <span class="text-sm text-red-100">{{ t('storefront.sections.ends_in') }}</span>
                        <div class="flex items-center gap-1 font-mono">
                            <span class="bg-white/20 backdrop-blur-sm px-2 py-1 rounded text-sm font-bold min-w-[2rem] text-center">{{ pad(hours) }}</span>
                            <span class="font-bold">:</span>
                            <span class="bg-white/20 backdrop-blur-sm px-2 py-1 rounded text-sm font-bold min-w-[2rem] text-center">{{ pad(minutes) }}</span>
                            <span class="font-bold">:</span>
                            <span class="bg-white/20 backdrop-blur-sm px-2 py-1 rounded text-sm font-bold min-w-[2rem] text-center">{{ pad(seconds) }}</span>
                        </div>
                    </div>

                    <!-- View All -->
                    <Link
                        :href="viewAllLink"
                        class="text-sm text-red-100 hover:text-white font-medium flex items-center gap-1 transition-colors"
                    >
                        {{ t('storefront.sections.view_all') }}
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </Link>
                </div>
            </div>

            <StorefrontProductCarousel
                :products="products"
                :show-arrows="true"
                arrow-variant="light"
                arrows-class="absolute right-0 -top-16 z-10 hidden lg:flex"
                @quick-view="(p) => emit('quick-view', p)"
                @details-view="(p) => emit('details-view', p)"
            />

        </div>
    </section>
</template>
