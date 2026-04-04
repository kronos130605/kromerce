import { ref, computed, watch } from 'vue';

const CART_STORAGE_KEY = 'kromerce_cart';

const cartItems = ref([]);

const loadCart = () => {
    try {
        const stored = localStorage.getItem(CART_STORAGE_KEY);
        cartItems.value = stored ? JSON.parse(stored) : [];
    } catch {
        cartItems.value = [];
    }
};

const saveCart = () => {
    localStorage.setItem(CART_STORAGE_KEY, JSON.stringify(cartItems.value));
};

loadCart();

watch(cartItems, saveCart, { deep: true });

export function useCart() {
    const cartCount = computed(() =>
        cartItems.value.reduce((sum, item) => sum + item.quantity, 0)
    );

    const cartTotal = computed(() =>
        cartItems.value.reduce((sum, item) => sum + item.price * item.quantity, 0)
    );

    const cartKey = (productId, currency) => `${productId}::${currency}`;

    const isInCart = (productId, currency = null) =>
        cartItems.value.some((item) =>
            item.id === productId && (currency === null || item.currency === currency)
        );

    const getItemQuantity = (productId, currency = null) => {
        const item = cartItems.value.find((i) =>
            i.id === productId && (currency === null || i.currency === currency)
        );
        return item ? item.quantity : 0;
    };

    const addToCart = (product, quantity = 1) => {
        const currency = product.selected_currency || product.base_currency || 'USD';
        const existing = cartItems.value.find(
            (i) => i.id === product.id && i.currency === currency
        );
        const price = product.sale_price && product.sale_price < product.base_price
            ? product.sale_price
            : product.base_price;

        if (existing) {
            existing.quantity += quantity;
        } else {
            cartItems.value.push({
                id: product.id,
                cart_key: cartKey(product.id, currency),
                name: product.name,
                slug: product.slug,
                price,
                base_price: product.base_price,
                sale_price: product.sale_price,
                currency,
                image: product.images?.[0]?.url || product.images?.[0]?.thumbnail_url || null,
                store: product.store ? { id: product.store.id, name: product.store.name, slug: product.store.slug } : null,
                stock_quantity: product.stock_quantity,
                quantity,
            });
        }
    };

    const cartByCurrency = computed(() => {
        const groups = {};
        for (const item of cartItems.value) {
            if (!groups[item.currency]) groups[item.currency] = [];
            groups[item.currency].push(item);
        }
        return groups;
    });

    /**
     * Create a reactive handler for adding products to cart with visual feedback.
     * Returns the handler function and the 'added' reactive state for UI feedback.
     */
    const createAddToCartHandler = () => {
        const added = ref(false);

        const handleAddToCart = (product, quantity, options = {}) => {
            const { isOutOfStock = false, validateProduct = true } = options;

            if (validateProduct && !product) return;
            if (isOutOfStock) return;

            addToCart(product, quantity);
            added.value = true;
            setTimeout(() => { added.value = false; }, 2000);
        };

        return { handleAddToCart, added };
    };

    const updateQuantity = (key, quantity) => {
        const item = cartItems.value.find((i) => i.cart_key === key || i.id === key);
        if (item) {
            if (quantity <= 0) {
                removeFromCart(key);
            } else {
                item.quantity = quantity;
            }
        }
    };

    const removeFromCart = (key) => {
        cartItems.value = cartItems.value.filter((i) => i.cart_key !== key && i.id !== key);
    };

    const clearCart = () => {
        cartItems.value = [];
    };

    return {
        cartItems,
        cartCount,
        cartTotal,
        cartByCurrency,
        isInCart,
        getItemQuantity,
        addToCart,
        createAddToCartHandler,
        updateQuantity,
        removeFromCart,
        clearCart,
    };
}
