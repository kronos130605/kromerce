<script setup>
import { computed } from 'vue';

const props = defineProps({
    product: {
        type: Object,
        required: true,
    },
    gallery: {
        type: Array,
        required: true,
    },
    activeImageUrl: {
        type: String,
        required: true,
    },
    activeImageIndex: {
        type: Number,
        default: 0,
    },
    // Thumbnail configuration
    thumbnailLimit: {
        type: Number,
        default: null, // null = show all
    },
    thumbnailSize: {
        type: String,
        default: 'md', // 'sm' (h-16), 'md' (h-20)
    },
    // Container styling variants
    variant: {
        type: String,
        default: 'default', // 'default', 'bordered', 'shadow'
    },
    // Padding class for container
    containerClass: {
        type: String,
        default: 'p-6',
    },
});

const emit = defineEmits(['set-active']);

const displayedGallery = computed(() => {
    if (props.thumbnailLimit) {
        return props.gallery.slice(0, props.thumbnailLimit);
    }
    return props.gallery;
});

const thumbnailSizeClass = computed(() => {
    return props.thumbnailSize === 'sm' ? 'h-16 w-16' : 'h-20 w-20';
});

const mainImageContainerClass = computed(() => {
    const baseClasses = 'overflow-hidden';
    switch (props.variant) {
        case 'bordered':
            return `${baseClasses} rounded-[1.5rem] border border-white/70 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800`;
        case 'shadow':
            return `${baseClasses} rounded-3xl border border-gray-100 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800`;
        default:
            return `${baseClasses} rounded-[1.5rem] bg-white shadow-sm dark:bg-gray-800`;
    }
});

const isActiveImage = (index) => index === props.activeImageIndex;

const setActiveImage = (index) => {
    emit('set-active', index);
};
</script>

<template>
    <div :class="['from-slate-50 via-white to-blue-50/70 dark:from-gray-800 dark:via-gray-900 dark:to-blue-950/30', containerClass]">
        <!-- Main Image -->
        <div :class="mainImageContainerClass">
            <div class="aspect-[4/3] overflow-hidden bg-gray-100 dark:bg-gray-700">
                <img
                    :src="activeImageUrl"
                    :alt="product.name"
                    class="h-full w-full object-cover"
                />
            </div>
        </div>

        <!-- Thumbnails -->
        <div v-if="gallery.length > 1" class="mt-4 flex gap-3 overflow-x-auto pb-1">
            <button
                v-for="(img, idx) in displayedGallery"
                :key="idx"
                @click="setActiveImage(idx)"
                :class="[
                    'flex-shrink-0 overflow-hidden rounded-2xl border-2 bg-white transition-all dark:bg-gray-800',
                    thumbnailSizeClass,
                    isActiveImage(idx)
                        ? 'border-blue-500 shadow-md shadow-blue-500/20'
                        : 'border-transparent hover:border-gray-300 dark:hover:border-gray-600'
                ]"
            >
                <img
                    :src="img.url || img.thumbnail_url"
                    :alt="`${product.name} ${idx + 1}`"
                    class="h-full w-full object-cover"
                />
            </button>
        </div>
    </div>
</template>
