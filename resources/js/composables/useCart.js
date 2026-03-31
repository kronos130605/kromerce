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

    const isInCart = (productId) =>
        cartItems.value.some((item) => item.id === productId);

    const getItemQuantity = (productId) => {
        const item = cartItems.value.find((i) => i.id === productId);
        return item ? item.quantity : 0;
    };

    const addToCart = (product, quantity = 1) => {
        const existing = cartItems.value.find((i) => i.id === product.id);
        const price = product.sale_price && product.sale_price < product.base_price
            ? product.sale_price
            : product.base_price;

        if (existing) {
            existing.quantity += quantity;
        } else {
            cartItems.value.push({
                id: product.id,
                name: product.name,
                slug: product.slug,
                price,
                base_price: product.base_price,
                sale_price: product.sale_price,
                currency: product.base_currency || 'USD',
                image: product.images?.[0]?.url || product.images?.[0]?.thumbnail_url || null,
                store: product.store ? { id: product.store.id, name: product.store.name, slug: product.store.slug } : null,
                stock_quantity: product.stock_quantity,
                quantity,
            });
        }
    };

    const updateQuantity = (productId, quantity) => {
        const item = cartItems.value.find((i) => i.id === productId);
        if (item) {
            if (quantity <= 0) {
                removeFromCart(productId);
            } else {
                item.quantity = quantity;
            }
        }
    };

    const removeFromCart = (productId) => {
        cartItems.value = cartItems.value.filter((i) => i.id !== productId);
    };

    const clearCart = () => {
        cartItems.value = [];
    };

    return {
        cartItems,
        cartCount,
        cartTotal,
        isInCart,
        getItemQuantity,
        addToCart,
        updateQuantity,
        removeFromCart,
        clearCart,
    };
}
