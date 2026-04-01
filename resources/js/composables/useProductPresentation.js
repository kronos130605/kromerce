import { computed } from 'vue';
import { useTranslations } from '@/composables/useTranslations';

export function useProductPresentation(productRef, options = {}) {
    const { galleryLimit = null } = options;
    const { t } = useTranslations();

    const gallery = computed(() => {
        const product = productRef.value;
        const images = product?.images?.length
            ? product.images
            : [{ url: '/images/placeholder-product.png', thumbnail_url: '/images/placeholder-product.png' }];

        return typeof galleryLimit === 'number' ? images.slice(0, galleryLimit) : images;
    });

    const primaryImageUrl = computed(() => {
        const product = productRef.value;

        return product?.images?.[0]?.url
            || product?.images?.[0]?.thumbnail_url
            || '/images/placeholder-product.png';
    });

    const hasDiscount = computed(() => {
        const product = productRef.value;
        return Boolean(product?.sale_price && product.sale_price < product.base_price);
    });

    const discountPercentage = computed(() => {
        const product = productRef.value;
        if (!hasDiscount.value || !product?.base_price) return 0;

        return Math.round(((product.base_price - product.sale_price) / product.base_price) * 100);
    });

    const displayPrice = computed(() => {
        const product = productRef.value;
        if (!product) return 0;

        return hasDiscount.value ? product.sale_price : product.base_price;
    });

    const savingsAmount = computed(() => {
        const product = productRef.value;
        if (!hasDiscount.value || !product) return 0;

        return product.base_price - product.sale_price;
    });

    const isOutOfStock = computed(() => (productRef.value?.stock_quantity || 0) <= 0);
    const ratingValue = computed(() => parseFloat(productRef.value?.rating || 4.5));
    const reviewCount = computed(() => productRef.value?.reviews_count || productRef.value?.sales_count || 0);

    const stockLabel = computed(() => {
        const product = productRef.value;

        if (isOutOfStock.value) return t('storefront.product.out_of_stock');
        if ((product?.stock_quantity || 0) <= 5) return t('storefront.product.low_stock');
        return t('storefront.product.ready_to_ship');
    });

    const formatPrice = (price) => {
        const product = productRef.value;
        if (price === null || price === undefined) return '';

        return new Intl.NumberFormat('en-US', {
            style: 'currency',
            currency: product?.base_currency || 'USD',
        }).format(price);
    };

    return {
        gallery,
        primaryImageUrl,
        hasDiscount,
        discountPercentage,
        displayPrice,
        savingsAmount,
        isOutOfStock,
        ratingValue,
        reviewCount,
        stockLabel,
        formatPrice,
    };
}
