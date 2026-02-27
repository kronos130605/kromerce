<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { useI18n } from 'vue-i18n';

const { t } = useI18n();

const currentTime = ref(new Date());
const flashSaleEnds = ref(new Date(Date.now() + 2 * 60 * 60 * 1000 + 34 * 60 * 1000 + 12 * 1000)); // 2h 34m 12s

const flashSaleData = ref({
    sold: 847,
    available: 1000,
    products: [
        { id: 1, name: 'Wireless Earbuds', icon: '🎧', originalPrice: 89.99, salePrice: 35.99, discount: 60 },
        { id: 2, name: 'Smart Watch', icon: '⌚', originalPrice: 299.99, salePrice: 149.99, discount: 50 },
        { id: 3, name: 'Laptop Stand', icon: '💻', originalPrice: 49.99, salePrice: 19.99, discount: 60 },
        { id: 4, name: 'Phone Case', icon: '📱', originalPrice: 24.99, salePrice: 7.49, discount: 70 }
    ]
});

const countdown = computed(() => {
    const now = currentTime.value.getTime();
    const end = flashSaleEnds.value.getTime();
    const diff = Math.max(0, end - now);
    
    const hours = Math.floor(diff / (1000 * 60 * 60));
    const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
    const seconds = Math.floor((diff % (1000 * 60)) / 1000);
    
    return `${hours}h ${minutes}m ${seconds}s`;
});

const progressPercentage = computed(() => {
    return (flashSaleData.value.sold / flashSaleData.value.available) * 100;
});

let timer = null;

onMounted(() => {
    timer = setInterval(() => {
        currentTime.value = new Date();
    }, 1000);
});

onUnmounted(() => {
    if (timer) clearInterval(timer);
});
</script>

<template>
    <div class="bg-gradient-to-r from-emerald-600 to-teal-600 dark:from-emerald-700 dark:to-teal-700 rounded-2xl p-6 text-white shadow-xl relative overflow-hidden mt-8">
        <!-- Animated background pattern -->
        <div class="absolute inset-0 opacity-10">
            <div class="absolute inset-0 bg-gradient-45 from-transparent via-white to-transparent animate-pulse"></div>
        </div>
        
        <div class="relative z-10">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <div class="flex items-center space-x-2 mb-2">
                        <span class="bg-white/20 backdrop-blur-sm px-3 py-1 rounded-full text-sm font-semibold">
                            🔥 {{ t('dashboard.flash_sale') }}
                        </span>
                        <span class="bg-yellow-400 text-emerald-900 px-2 py-1 rounded-full text-xs font-bold animate-pulse">
                            {{ t('dashboard.limited_time') }}
                        </span>
                    </div>
                    <h3 class="text-2xl font-bold">{{ t('dashboard.mega_deals') }}</h3>
                    <p class="text-emerald-100">{{ t('dashboard.up_to_70_off') }}</p>
                </div>
                <div class="text-right">
                    <div class="text-3xl font-bold tabular-nums">{{ countdown }}</div>
                    <div class="text-sm text-emerald-100">{{ t('dashboard.time_left') }}</div>
                </div>
            </div>
            
            <!-- Progress bar -->
            <div class="mb-4">
                <div class="flex justify-between text-sm text-emerald-100 mb-1">
                    <span>{{ t('dashboard.progress') }}</span>
                    <span>{{ Math.round(progressPercentage) }}%</span>
                </div>
                <div class="w-full bg-white/20 rounded-full h-3 overflow-hidden">
                    <div 
                        class="bg-gradient-to-r from-yellow-400 to-amber-300 h-full rounded-full transition-all duration-1000 ease-out"
                        :style="{ width: `${progressPercentage}%` }"
                    ></div>
                </div>
                <div class="text-xs text-emerald-100 mt-1">
                    {{ flashSaleData.sold }} {{ t('dashboard.sold') }} • {{ flashSaleData.available - flashSaleData.sold }} {{ t('dashboard.available') }}
                </div>
            </div>
            
            <!-- Featured products -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                <div v-for="product in flashSaleData.products" :key="product.id" 
                     class="bg-white/10 backdrop-blur-sm rounded-lg p-3 hover:bg-white/20 transition-all cursor-pointer">
                    <div class="text-center">
                        <div class="text-2xl mb-1">{{ product.icon }}</div>
                        <div class="text-xs font-semibold truncate">{{ product.name }}</div>
                        <div class="flex items-center justify-center space-x-1 mt-1">
                            <span class="text-xs line-through opacity-70">${{ product.originalPrice }}</span>
                            <span class="text-sm font-bold">${{ product.salePrice }}</span>
                        </div>
                        <div class="text-xs bg-emerald-700 text-white rounded-full px-2 py-0.5 mt-1 inline-block">
                            -{{ product.discount }}%
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
