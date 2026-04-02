import { ref, computed, watch } from 'vue';

/**
 * Composable for managing product image gallery state
 * 
 * @param {import('vue').Ref<Object>} productRef - Ref to the product object
 * @param {Object} options - Configuration options
 * @param {import('vue').Ref<Array>} options.galleryRef - Optional external gallery ref (from useProductPresentation)
 * @param {string} options.placeholderUrl - URL for placeholder image
 * @returns {Object} Gallery state and methods
 */
export function useProductGallery(productRef, options = {}) {
    const {
        galleryRef = null,
        placeholderUrl = '/images/placeholder-product.png'
    } = options;

    const activeImageIndex = ref(0);

    // Reset to first image when product changes
    watch(() => productRef?.value, () => {
        activeImageIndex.value = 0;
    }, { immediate: true });

    // Get the active image URL
    const activeImageUrl = computed(() => {
        const gallery = galleryRef?.value;
        if (!gallery || gallery.length === 0) {
            return placeholderUrl;
        }
        const image = gallery[activeImageIndex.value];
        return image?.url || image?.thumbnail_url || placeholderUrl;
    });

    // Check if a specific index is the active one
    const isActiveImage = (index) => activeImageIndex.value === index;

    // Set active image by index
    const setActiveImage = (index) => {
        activeImageIndex.value = index;
    };

    // Navigate to next image
    const nextImage = () => {
        const gallery = galleryRef?.value;
        if (!gallery || gallery.length === 0) return;
        activeImageIndex.value = (activeImageIndex.value + 1) % gallery.length;
    };

    // Navigate to previous image
    const previousImage = () => {
        const gallery = galleryRef?.value;
        if (!gallery || gallery.length === 0) return;
        activeImageIndex.value = (activeImageIndex.value - 1 + gallery.length) % gallery.length;
    };

    // Get image URL at specific index (for thumbnails)
    const getImageUrlAt = (index) => {
        const gallery = galleryRef?.value;
        if (!gallery || !gallery[index]) return placeholderUrl;
        return gallery[index]?.url || gallery[index]?.thumbnail_url || placeholderUrl;
    };

    // Check if gallery has images
    const hasImages = computed(() => {
        const gallery = galleryRef?.value;
        return gallery && gallery.length > 0;
    });

    // Get gallery length
    const imageCount = computed(() => {
        const gallery = galleryRef?.value;
        return gallery?.length || 0;
    });

    return {
        activeImageIndex,
        activeImageUrl,
        isActiveImage,
        setActiveImage,
        nextImage,
        previousImage,
        getImageUrlAt,
        hasImages,
        imageCount,
    };
}
