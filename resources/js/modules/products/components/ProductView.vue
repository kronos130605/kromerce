<script setup>
import { ref, computed, watch, onMounted, onUnmounted } from 'vue';
import { useI18n } from 'vue-i18n';

const { t } = useI18n();

const props = defineProps({
    isOpen: { type: Boolean, required: true },
    product: { type: Object, default: null },
});

const emit = defineEmits(['close', 'edit']);

// Lock body scroll
watch(() => props.isOpen, (open) => {
    document.body.style.overflow = open ? 'hidden' : '';
});

const handleClose = () => emit('close');
const handleEdit = () => emit('edit', props.product);

const formatPrice = (price) => {
    const numPrice = parseFloat(price);
    if (isNaN(numPrice) || numPrice === null || numPrice === undefined) {
        return '$0.00';
    }
    return new Intl.NumberFormat('en-US', { 
        style: 'currency', 
        currency: props.product?.base_currency || 'USD' 
    }).format(numPrice);
};

const formatDate = (date) => date ? new Date(date).toLocaleDateString() : '-';

const getStatusColor = (status) => ({
    active: 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
    inactive: 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
    draft: 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
}[status] || 'bg-yellow-100 text-yellow-800');

// Image Gallery State
const currentImageIndex = ref(0);
const loadedImages = ref(new Set());
const thumbnailsContainer = ref(null);

const images = computed(() => {
    if (!props.product?.images?.length) return [];
    // Sort by order and ensure primary is first
    return [...props.product.images].sort((a, b) => {
        if (a.is_primary && !b.is_primary) return -1;
        if (!a.is_primary && b.is_primary) return 1;
        return (a.order || 0) - (b.order || 0);
    });
});

const currentImage = computed(() => images.value[currentImageIndex.value] || null);
const hasMultipleImages = computed(() => images.value.length > 1);

// Lazy loading observer
const setupLazyLoading = () => {
    if (!thumbnailsContainer.value) return;
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const img = entry.target;
                const src = img.dataset.src;
                if (src && !loadedImages.value.has(src)) {
                    img.src = src;
                    loadedImages.value.add(src);
                    observer.unobserve(img);
                }
            }
        });
    }, {
        root: thumbnailsContainer.value,
        rootMargin: '50px',
        threshold: 0.1
    });

    // Observe all thumbnail images
    const thumbImages = thumbnailsContainer.value.querySelectorAll('img[data-src]');
    thumbImages.forEach(img => observer.observe(img));
};

watch(() => props.isOpen, (open) => {
    if (open) {
        currentImageIndex.value = 0;
        loadedImages.value.clear();
        // Preload first image immediately
        if (images.value[0]?.url) {
            loadedImages.value.add(images.value[0].url);
        }
        // Setup lazy loading after render
        setTimeout(setupLazyLoading, 100);
    }
});

watch(images, () => {
    if (props.isOpen) {
        setTimeout(setupLazyLoading, 100);
    }
}, { deep: true });

// Navigation methods
const nextImage = () => {
    if (currentImageIndex.value < images.value.length - 1) {
        currentImageIndex.value++;
        scrollThumbnailIntoView();
    }
};

const prevImage = () => {
    if (currentImageIndex.value > 0) {
        currentImageIndex.value--;
        scrollThumbnailIntoView();
    }
};

const goToImage = (index) => {
    currentImageIndex.value = index;
    scrollThumbnailIntoView();
};

const scrollThumbnailIntoView = () => {
    if (!thumbnailsContainer.value) return;
    const thumb = thumbnailsContainer.value.children[currentImageIndex.value];
    if (thumb) {
        thumb.scrollIntoView({ behavior: 'smooth', inline: 'center', block: 'nearest' });
    }
};

// Keyboard navigation
const handleKeydown = (e) => {
    if (!props.isOpen) return;
    if (e.key === 'ArrowRight') nextImage();
    if (e.key === 'ArrowLeft') prevImage();
    if (e.key === 'Escape') handleClose();
};

// Touch/Swipe support
const touchStartX = ref(0);
const touchEndX = ref(0);

const handleTouchStart = (e) => {
    touchStartX.value = e.changedTouches[0].screenX;
};

const handleTouchEnd = (e) => {
    touchEndX.value = e.changedTouches[0].screenX;
    handleSwipe();
};

const handleSwipe = () => {
    const swipeThreshold = 50;
    const diff = touchStartX.value - touchEndX.value;
    if (Math.abs(diff) > swipeThreshold) {
        if (diff > 0) nextImage();
        else prevImage();
    }
};

onMounted(() => {
    window.addEventListener('keydown', handleKeydown);
});

onUnmounted(() => {
    window.removeEventListener('keydown', handleKeydown);
});

// Computed price display
const displayPrice = computed(() => {
    if (!props.product) return '$0.00';
    const salePrice = parseFloat(props.product.base_sale_price);
    const basePrice = parseFloat(props.product.base_price);
    
    if (!isNaN(salePrice) && salePrice > 0 && salePrice < basePrice) {
        return formatPrice(salePrice);
    }
    return formatPrice(basePrice);
});

const originalPrice = computed(() => {
    if (!props.product) return null;
    const salePrice = parseFloat(props.product.base_sale_price);
    const basePrice = parseFloat(props.product.base_price);
    
    if (!isNaN(salePrice) && salePrice > 0 && salePrice < basePrice) {
        return formatPrice(basePrice);
    }
    return null;
});
</script>

<template>
    <Teleport to="body">
        <Transition name="slider">
            <div v-if="isOpen" class="fixed inset-0 z-50 overflow-hidden">
                <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" @click="handleClose" />
                <div class="absolute inset-y-0 right-0 w-full max-w-2xl bg-white dark:bg-gray-900 shadow-2xl flex flex-col" @click.stop>
                    <!-- Header -->
                    <header class="flex items-center justify-between px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-white/80 dark:bg-gray-900/80 backdrop-blur-sm sticky top-0 z-10">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">{{ t('products.view.title') }}</h2>
                        <div class="flex items-center space-x-2">
                            <button @click="handleEdit" class="p-2 text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/30 rounded-lg transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                            </button>
                            <button @click="handleClose" class="p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 rounded-lg transition-colors">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </header>

                    <!-- Content -->
                    <div v-if="product" class="flex-1 overflow-y-auto">
                        <!-- Image Gallery -->
                        <div class="relative bg-gray-100 dark:bg-gray-800">
                            <!-- Main Image -->
                            <div 
                                class="aspect-[4/3] relative overflow-hidden"
                                @touchstart="handleTouchStart"
                                @touchend="handleTouchEnd"
                            >
                                <span v-if="!images.length" class="absolute inset-0 flex items-center justify-center text-6xl">📦</span>
                                
                                <template v-else>
                                    <TransitionGroup name="fade" mode="out-in">
                                        <img 
                                            :key="currentImage?.id"
                                            :src="currentImage?.medium_url || currentImage?.url" 
                                            :alt="currentImage?.alt || product.name"
                                            class="w-full h-full object-cover"
                                            loading="eager"
                                        />
                                    </TransitionGroup>
                                    
                                    <!-- Navigation Arrows -->
                                    <button 
                                        v-if="hasMultipleImages && currentImageIndex > 0"
                                        @click="prevImage"
                                        class="absolute left-4 top-1/2 -translate-y-1/2 p-2 bg-white/90 dark:bg-gray-800/90 rounded-full shadow-lg hover:bg-white dark:hover:bg-gray-800 transition-all"
                                    >
                                        <svg class="w-5 h-5 text-gray-700 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                                        </svg>
                                    </button>
                                    <button 
                                        v-if="hasMultipleImages && currentImageIndex < images.length - 1"
                                        @click="nextImage"
                                        class="absolute right-4 top-1/2 -translate-y-1/2 p-2 bg-white/90 dark:bg-gray-800/90 rounded-full shadow-lg hover:bg-white dark:hover:bg-gray-800 transition-all"
                                    >
                                        <svg class="w-5 h-5 text-gray-700 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                        </svg>
                                    </button>
                                    
                                    <!-- Image Counter -->
                                    <div v-if="hasMultipleImages" class="absolute top-4 right-4 px-3 py-1 bg-black/50 text-white text-sm rounded-full">
                                        {{ currentImageIndex + 1 }} / {{ images.length }}
                                    </div>
                                </template>
                            </div>
                            
                            <!-- Thumbnails -->
                            <div 
                                v-if="hasMultipleImages"
                                ref="thumbnailsContainer"
                                class="flex gap-2 p-4 overflow-x-auto scrollbar-thin scrollbar-thumb-gray-300 scrollbar-track-transparent"
                            >
                                <button
                                    v-for="(img, idx) in images"
                                    :key="img.id"
                                    @click="goToImage(idx)"
                                    class="flex-shrink-0 w-16 h-16 rounded-lg overflow-hidden border-2 transition-all"
                                    :class="idx === currentImageIndex ? 'border-blue-500 ring-2 ring-blue-200' : 'border-transparent hover:border-gray-300'"
                                >
                                    <img
                                        :data-src="img.thumbnail_url || img.url"
                                        :src="loadedImages.has(img.thumbnail_url || img.url) ? (img.thumbnail_url || img.url) : ''"
                                        :alt="img.alt || ''"
                                        class="w-full h-full object-cover"
                                        :class="loadedImages.has(img.thumbnail_url || img.url) ? '' : 'bg-gray-200 dark:bg-gray-700 animate-pulse'"
                                    />
                                </button>
                            </div>
                        </div>

                        <!-- Product Info -->
                        <div class="p-6 space-y-6">
                            <div>
                                <h3 class="text-2xl font-bold text-gray-900 dark:text-white">{{ product.name }}</h3>
                                <p class="text-gray-600 dark:text-gray-400 mt-2">{{ product.description || t('products.no_description') }}</p>
                            </div>

                            <!-- Status & Price -->
                            <div class="flex items-center justify-between flex-wrap gap-4">
                                <span :class="['px-3 py-1 rounded-full text-sm font-medium capitalize', getStatusColor(product.status)]">
                                    {{ product.status }}
                                </span>
                                <div class="text-right">
                                    <span v-if="originalPrice" class="text-lg text-gray-400 line-through mr-2">{{ originalPrice }}</span>
                                    <span class="text-2xl font-bold text-gray-900 dark:text-white">{{ displayPrice }}</span>
                                </div>
                            </div>

                            <!-- Details Grid -->
                            <div class="grid grid-cols-2 gap-4 text-sm">
                                <div class="p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">
                                    <p class="text-gray-500 dark:text-gray-400">{{ t('products.fields.sku') }}</p>
                                    <p class="font-medium text-gray-900 dark:text-white">{{ product.sku || '-' }}</p>
                                </div>
                                <div class="p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">
                                    <p class="text-gray-500 dark:text-gray-400">{{ t('products.fields.barcode') }}</p>
                                    <p class="font-medium text-gray-900 dark:text-white">{{ product.barcode || '-' }}</p>
                                </div>
                                <div class="p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">
                                    <p class="text-gray-500 dark:text-gray-400">{{ t('products.fields.stock') }}</p>
                                    <p class="font-medium text-gray-900 dark:text-white" :class="product.stock_quantity <= product.low_stock_threshold ? 'text-red-600' : ''">
                                        {{ product.stock_quantity || 0 }}
                                    </p>
                                </div>
                                <div class="p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">
                                    <p class="text-gray-500 dark:text-gray-400">{{ t('products.fields.created') }}</p>
                                    <p class="font-medium text-gray-900 dark:text-white">{{ formatDate(product.created_at) }}</p>
                                </div>
                            </div>
                            
                            <!-- Stock Status -->
                            <div v-if="product.manage_stock" class="flex items-center gap-2 p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                                <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                </svg>
                                <span class="text-sm text-blue-800 dark:text-blue-200">
                                    {{ t('products.stock.managing_stock') }} - 
                                    <span :class="product.stock_quantity === 0 ? 'text-red-600 font-semibold' : (product.stock_quantity <= product.low_stock_threshold ? 'text-yellow-600 font-semibold' : 'text-green-600')">
                                        {{ product.stock_quantity === 0 ? t('products.stock.out_of_stock') : (product.stock_quantity <= product.low_stock_threshold ? t('products.stock.low_stock') : t('products.stock.in_stock')) }}
                                    </span>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </Transition>
    </Teleport>
</template>

<style scoped>
.slider-enter-active, .slider-leave-active { 
    transition: all 0.3s ease; 
}
.slider-enter-from, .slider-leave-to { 
    opacity: 0; 
}
.slider-enter-from > div:last-child, .slider-leave-to > div:last-child { 
    transform: translateX(100%); 
}

.fade-enter-active, .fade-leave-active {
    transition: opacity 0.3s ease;
}
.fade-enter-from, .fade-leave-to {
    opacity: 0;
}

/* Custom scrollbar for thumbnails */
.scrollbar-thin::-webkit-scrollbar {
    height: 6px;
}
.scrollbar-thin::-webkit-scrollbar-track {
    background: transparent;
}
.scrollbar-thin::-webkit-scrollbar-thumb {
    background-color: rgb(209 213 219);
    border-radius: 20px;
}
.dark .scrollbar-thin::-webkit-scrollbar-thumb {
    background-color: rgb(75 85 99);
}
</style>
