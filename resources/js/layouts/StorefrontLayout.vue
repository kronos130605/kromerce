<script setup>
import { ref, computed } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import { useDarkMode } from '@/composables/useDarkMode';
import { useTranslations } from '@/composables/useTranslations';
import { useCart } from '@/composables/useCart';
import LanguageSelector from '@/components/shared/LanguageSelector.vue';
import DropdownMenu from '@/components/shared/DropdownMenu.vue';

const { t } = useTranslations();
useTranslations('storefront');
const { isDark, toggleDarkMode } = useDarkMode();
const { cartCount } = useCart();
const showMobileMenu = ref(false);
const showCartPreview = ref(false);
const categoriesDropdownRef = ref(null);
const { cartItems, cartTotal, removeFromCart, updateQuantity } = useCart();

const categories = [
    { name: 'Electronics', slug: 'electronics', icon: '💻', count: 1240 },
    { name: 'Fashion', slug: 'fashion', icon: '👕', count: 856 },
    { name: 'Home & Garden', slug: 'home-garden', icon: '🏡', count: 643 },
    { name: 'Sports', slug: 'sports', icon: '⚽', count: 432 },
    { name: 'Books', slug: 'books', icon: '📚', count: 389 },
    { name: 'Toys', slug: 'toys', icon: '🧸', count: 267 },
    { name: 'Beauty', slug: 'beauty', icon: '💄', count: 198 },
    { name: 'Automotive', slug: 'automotive', icon: '🚗', count: 156 },
];

const currentYear = computed(() => new Date().getFullYear());

const formatPrice = (price, currency = 'USD') =>
    new Intl.NumberFormat('en-US', { style: 'currency', currency }).format(price);

const cartSubtotal = computed(() => cartTotal.value);

const cartSavings = computed(() =>
    cartItems.value.reduce((sum, item) => {
        const base = Number(item.base_price || item.price || 0);
        const finalPrice = Number(item.price || 0);
        return sum + Math.max(base - finalPrice, 0) * item.quantity;
    }, 0)
);

const cartStoresCount = computed(() => {
    const stores = new Set(cartItems.value.map((item) => item.store?.id).filter(Boolean));
    return stores.size;
});
</script>

<template>
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900">

        <!-- Top Bar -->
        <div class="bg-gray-900 dark:bg-gray-950 text-gray-300 text-xs border-b border-gray-800">
            <div class="max-w-7xl mx-auto px-4 py-2 flex items-center justify-between">
                <!-- Left: Promo -->
                <span class="hidden sm:flex items-center gap-2 text-emerald-400 font-medium">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    {{ t('storefront.topbar.free_shipping') }}
                </span>

                <!-- Right: Actions -->
                <div class="flex items-center gap-3 ml-auto">
                    <LanguageSelector />
                    <button
                        @click="toggleDarkMode"
                        class="p-1 hover:text-white transition-colors"
                        :title="isDark ? 'Light Mode' : 'Dark Mode'"
                    >
                        <svg v-if="isDark" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                        <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                        </svg>
                    </button>
                    <span class="text-gray-700">|</span>
                    <template v-if="$page.props.auth?.user">
                        <Link href="/business" class="hover:text-white transition-colors flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            <span class="hidden sm:inline">{{ t('storefront.navigation.account') }}</span>
                        </Link>
                    </template>
                    <template v-else>
                        <Link href="/login" class="hover:text-white transition-colors">{{ t('storefront.navigation.login') }}</Link>
                        <Link href="/register" class="px-2 py-0.5 bg-blue-600 hover:bg-blue-500 text-white rounded text-[10px] font-medium transition-colors">
                            {{ t('storefront.navigation.register') }}
                        </Link>
                    </template>
                </div>
            </div>
        </div>

        <!-- Main Header -->
        <header class="bg-white dark:bg-gray-800 shadow-sm sticky top-0 z-40 backdrop-blur-sm">
            <div class="max-w-7xl mx-auto px-4">
                <div class="flex items-center gap-4 h-16">

                    <!-- Logo -->
                    <Link href="/" class="flex-shrink-0 flex items-center group">
                        <img
                            src="/images/kromerce-business-text.png"
                            alt="Kromerce"
                            :class="[
                                'h-8 w-auto object-contain transition-all duration-300 group-hover:opacity-80',
                                isDark ? 'filter brightness-0 invert' : ''
                            ]"
                        />
                    </Link>

                    <!-- Search Bar with Categories Dropdown -->
                    <div class="flex-1 flex items-center gap-2">
                        <!-- Categories Dropdown -->
                        <DropdownMenu
                            ref="categoriesDropdownRef"
                            triggerType="both"
                            position="left"
                            width="280px"
                            triggerClass="hidden md:flex items-center gap-2 px-3 py-2.5 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-xl font-medium text-sm whitespace-nowrap"
                        >
                            <template #trigger="{ isOpen }">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                                </svg>
                                <span class="hidden lg:block">{{ t('storefront.navigation.categories') }}</span>
                                <svg class="w-3.5 h-3.5 transition-transform duration-200" :class="{ 'rotate-180': isOpen }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </template>

                            <template #content="{ close }">
                                <div class="p-2">
                                    <Link
                                        href="/products"
                                        @click="close"
                                        class="flex items-center gap-3 px-3 py-2.5 text-sm font-semibold text-blue-600 dark:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/30 rounded-lg transition-colors"
                                    >
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                                        </svg>
                                        {{ t('storefront.navigation.all_products') }}
                                    </Link>
                                    <div class="my-2 border-t border-gray-100 dark:border-gray-700"></div>
                                    <Link
                                        v-for="category in categories"
                                        :key="category.slug"
                                        :href="`/category/${category.slug}`"
                                        @click="close"
                                        class="flex items-center justify-between px-3 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors group"
                                    >
                                        <div class="flex items-center gap-3">
                                            <span class="w-6 h-6 flex items-center justify-center bg-gray-100 dark:bg-gray-700 rounded-full text-sm group-hover:bg-white dark:group-hover:bg-gray-600 transition-colors">{{ category.icon }}</span>
                                            <span>{{ category.name }}</span>
                                        </div>
                                        <span class="text-xs text-gray-400 dark:text-gray-500">{{ category.count }}</span>
                                    </Link>
                                </div>
                                <div class="bg-gray-50 dark:bg-gray-900/50 p-3 border-t border-gray-100 dark:border-gray-700">
                                    <Link href="/stores" @click="close" class="flex items-center gap-2 text-xs text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                        </svg>
                                        {{ t('storefront.footer.for_sellers.start_selling') }}
                                    </Link>
                                </div>
                            </template>
                        </DropdownMenu>

                        <!-- Search Input -->
                        <form action="/search" method="GET" class="flex-1">
                            <div class="flex w-full rounded-xl border border-gray-200 dark:border-gray-600 overflow-hidden focus-within:ring-2 focus-within:ring-blue-500 focus-within:border-blue-500 transition-all">
                                <input
                                    type="text"
                                    name="q"
                                    :placeholder="t('storefront.navigation.search_placeholder')"
                                    class="flex-1 px-4 py-2.5 text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-400 outline-none"
                                />
                                <button
                                    type="submit"
                                    class="px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white transition-colors flex-shrink-0 flex items-center gap-1.5"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    </svg>
                                    <span class="hidden sm:block text-sm font-medium">{{ t('storefront.navigation.search') }}</span>
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Right Actions -->
                    <div class="flex items-center gap-1 flex-shrink-0">
                        <!-- Mobile Menu Button -->
                        <button
                            @click="showMobileMenu = !showMobileMenu"
                            class="md:hidden p-2.5 text-gray-500 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                            </svg>
                        </button>

                        <!-- Wishlist -->
                        <button class="p-2.5 text-gray-500 dark:text-gray-400 hover:text-red-500 dark:hover:text-red-400 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors" :title="t('storefront.navigation.wishlist')">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                            </svg>
                        </button>

                        <!-- Cart -->
                        <div class="relative">
                            <button
                                @click="showCartPreview = !showCartPreview"
                                class="relative p-2.5 text-gray-500 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors flex items-center gap-1.5"
                            >
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                                <span class="hidden lg:block text-sm font-medium text-gray-700 dark:text-gray-300">{{ t('storefront.navigation.cart') }}</span>
                                <Transition
                                    enter-active-class="transition-all duration-200"
                                    enter-from-class="scale-0 opacity-0"
                                    enter-to-class="scale-100 opacity-100"
                                >
                                    <span
                                        v-if="cartCount > 0"
                                        class="absolute -top-1 -right-1 min-w-[18px] h-[18px] bg-red-500 text-white text-[10px] font-bold rounded-full flex items-center justify-center px-1"
                                    >
                                        {{ cartCount > 99 ? '99+' : cartCount }}
                                    </span>
                                </Transition>
                            </button>

                            <!-- Cart Dropdown Preview -->
                            <Transition
                                enter-active-class="transition ease-out duration-200"
                                enter-from-class="opacity-0 translate-y-1"
                                enter-to-class="opacity-100 translate-y-0"
                                leave-active-class="transition ease-in duration-150"
                                leave-from-class="opacity-100 translate-y-0"
                                leave-to-class="opacity-0 translate-y-1"
                            >
                                <div
                                    v-if="showCartPreview"
                                    class="absolute right-0 top-full z-50 mt-2 w-[24rem] overflow-hidden rounded-[1.5rem] border border-gray-100 bg-white shadow-2xl dark:border-gray-700 dark:bg-gray-800"
                                >
                                    <div class="border-b border-gray-100 bg-gradient-to-r from-blue-600 to-indigo-600 p-4 text-white dark:border-gray-700">
                                        <div class="flex items-start justify-between gap-3">
                                            <div>
                                                <h3 class="text-sm font-semibold">{{ t('storefront.navigation.cart') }}</h3>
                                                <p class="mt-1 text-xs text-blue-100">
                                                    {{ cartCount }} {{ t('storefront.cart.items') }}
                                                    <span v-if="cartStoresCount">• {{ cartStoresCount }} {{ t('storefront.cart.stores') }}</span>
                                                </p>
                                            </div>
                                            <span class="rounded-full bg-white/15 px-2.5 py-1 text-[11px] font-semibold text-white/95 backdrop-blur">
                                                {{ formatPrice(cartSubtotal) }}
                                            </span>
                                        </div>
                                    </div>

                                    <div v-if="cartItems.length === 0" class="p-8 text-center">
                                        <svg class="w-12 h-12 text-gray-300 dark:text-gray-600 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                                        </svg>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ t('storefront.cart.empty') }}</p>
                                    </div>

                                    <div v-else>
                                        <ul class="max-h-80 space-y-3 overflow-y-auto p-3">
                                            <li v-for="item in cartItems" :key="item.id" class="rounded-2xl border border-gray-100 bg-gray-50/70 p-3 dark:border-gray-700 dark:bg-gray-900/40">
                                                <div class="flex items-start gap-3">
                                                    <div class="h-14 w-14 flex-shrink-0 overflow-hidden rounded-xl bg-gray-100 dark:bg-gray-700">
                                                        <img v-if="item.image" :src="item.image" :alt="item.name" class="w-full h-full object-cover" />
                                                        <div v-else class="w-full h-full flex items-center justify-center text-gray-400">
                                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                                            </svg>
                                                        </div>
                                                    </div>
                                                    <div class="min-w-0 flex-1">
                                                        <div class="flex items-start justify-between gap-2">
                                                            <div class="min-w-0">
                                                                <p class="truncate text-xs font-semibold text-gray-900 dark:text-white">{{ item.name }}</p>
                                                                <p v-if="item.store?.name" class="mt-1 text-[11px] text-blue-600 dark:text-blue-400">{{ item.store.name }}</p>
                                                                <p class="mt-1 text-[11px] text-gray-500 dark:text-gray-400">
                                                                    {{ formatPrice(item.price, item.currency) }}
                                                                    <span v-if="item.base_price && item.base_price > item.price" class="ml-1 text-gray-400 line-through dark:text-gray-500">
                                                                        {{ formatPrice(item.base_price, item.currency) }}
                                                                    </span>
                                                                </p>
                                                            </div>
                                                            <button
                                                                @click="removeFromCart(item.id)"
                                                                class="flex-shrink-0 text-gray-300 transition-colors hover:text-red-500 dark:text-gray-600"
                                                            >
                                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                                                </svg>
                                                            </button>
                                                        </div>

                                                        <div class="mt-3 flex items-center justify-between gap-3">
                                                            <div class="flex items-center overflow-hidden rounded-xl border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-800">
                                                                <button
                                                                    @click="updateQuantity(item.id, item.quantity - 1)"
                                                                    class="px-2.5 py-1.5 text-gray-600 transition-colors hover:bg-gray-50 dark:text-gray-300 dark:hover:bg-gray-700"
                                                                >
                                                                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />
                                                                    </svg>
                                                                </button>
                                                                <span class="min-w-[2rem] px-2 text-center text-xs font-semibold text-gray-900 dark:text-white">{{ item.quantity }}</span>
                                                                <button
                                                                    @click="updateQuantity(item.id, item.quantity + 1)"
                                                                    :disabled="item.stock_quantity > 0 && item.quantity >= item.stock_quantity"
                                                                    class="px-2.5 py-1.5 text-gray-600 transition-colors hover:bg-gray-50 disabled:opacity-40 dark:text-gray-300 dark:hover:bg-gray-700"
                                                                >
                                                                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                                                    </svg>
                                                                </button>
                                                            </div>

                                                            <div class="text-right">
                                                                <p class="text-xs font-semibold text-gray-900 dark:text-white">
                                                                    {{ formatPrice(item.price * item.quantity, item.currency) }}
                                                                </p>
                                                                <p v-if="item.stock_quantity && item.stock_quantity <= 5" class="mt-1 text-[10px] font-medium text-amber-600 dark:text-amber-400">
                                                                    {{ t('storefront.product.low_stock') }}
                                                                </p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </li>
                                        </ul>
                                        <div class="border-t border-gray-100 p-4 dark:border-gray-700">
                                            <div class="rounded-2xl bg-gray-50 p-3 dark:bg-gray-900/50">
                                                <div class="flex justify-between text-xs text-gray-500 dark:text-gray-400">
                                                    <span>{{ t('storefront.cart.subtotal') }}</span>
                                                    <span>{{ formatPrice(cartSubtotal) }}</span>
                                                </div>
                                                <div class="mt-2 flex justify-between text-xs text-emerald-600 dark:text-emerald-400">
                                                    <span>{{ t('storefront.cart.savings') }}</span>
                                                    <span>{{ formatPrice(cartSavings) }}</span>
                                                </div>
                                                <div class="mt-3 flex justify-between text-sm font-semibold text-gray-900 dark:text-white">
                                                    <span>{{ t('storefront.cart.total') }}</span>
                                                    <span>{{ formatPrice(cartSubtotal) }}</span>
                                                </div>
                                            </div>
                                            <div class="mt-3 grid grid-cols-2 gap-2">
                                                <Link href="/products" class="inline-flex items-center justify-center rounded-xl border border-gray-200 px-3 py-2 text-xs font-semibold text-gray-700 transition-colors hover:border-blue-300 hover:text-blue-600 dark:border-gray-600 dark:text-gray-200 dark:hover:border-blue-500 dark:hover:text-blue-400">
                                                    {{ t('storefront.cart.continue_shopping') }}
                                                </Link>
                                                <button class="rounded-xl bg-blue-600 py-2 text-sm font-medium text-white transition-colors hover:bg-blue-700">
                                                    {{ t('storefront.cart.checkout') }}
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </Transition>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- Mobile Menu -->
        <Transition
            enter-active-class="transition ease-out duration-200"
            enter-from-class="opacity-0 -translate-y-2"
            enter-to-class="opacity-100 translate-y-0"
            leave-active-class="transition ease-in duration-150"
            leave-from-class="opacity-100 translate-y-0"
            leave-to-class="opacity-0 -translate-y-2"
        >
            <div v-if="showMobileMenu" class="md:hidden bg-white dark:bg-gray-800 border-b border-gray-100 dark:border-gray-700 shadow-lg">
                <div class="max-w-7xl mx-auto px-4 py-4">
                    <!-- Search Mobile -->
                    <form action="/search" method="GET" class="mb-4">
                        <div class="flex w-full rounded-xl border border-gray-200 dark:border-gray-600 overflow-hidden">
                            <input
                                type="text"
                                name="q"
                                :placeholder="t('storefront.navigation.search_placeholder')"
                                class="flex-1 px-4 py-2.5 text-sm bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-400 outline-none"
                            />
                            <button type="submit" class="px-4 bg-blue-600 text-white">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </button>
                        </div>
                    </form>

                    <!-- Categories Grid -->
                    <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-3">{{ t('storefront.navigation.categories') }}</p>
                    <div class="grid grid-cols-2 gap-2 mb-4">
                        <Link
                            v-for="category in categories"
                            :key="category.slug"
                            :href="`/category/${category.slug}`"
                            class="flex items-center gap-3 px-3 py-2.5 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors"
                        >
                            <span class="w-8 h-8 flex items-center justify-center bg-gray-100 dark:bg-gray-700 rounded-full text-base">{{ category.icon }}</span>
                            <div>
                                <span class="block font-medium">{{ category.name }}</span>
                                <span class="text-xs text-gray-400">{{ category.count }} items</span>
                            </div>
                        </Link>
                    </div>

                    <!-- All Products Link -->
                    <Link
                        href="/products"
                        class="flex items-center justify-center gap-2 px-4 py-3 text-sm font-medium text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-900/30 rounded-xl hover:bg-blue-100 dark:hover:bg-blue-900/50 transition-colors mb-4"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                        </svg>
                        {{ t('storefront.navigation.all_products') }}
                    </Link>

                    <!-- Quick Links -->
                    <div class="border-t border-gray-100 dark:border-gray-700 pt-4">
                        <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-3">{{ t('storefront.footer.quick_links.title') }}</p>
                        <div class="space-y-1">
                            <Link href="/stores" class="flex items-center gap-2 px-3 py-2 text-sm text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 rounded-lg transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                </svg>
                                {{ t('storefront.footer.for_sellers.start_selling') }}
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </Transition>

        <!-- Click outside overlay -->
        <div
            v-if="showCartPreview || categoriesDropdownRef?.isOpen"
            class="fixed inset-0 z-30"
            @click="showCartPreview = false; categoriesDropdownRef?.close()"
        />

        <!-- Main Content -->
        <main>
            <slot />
        </main>

        <!-- Footer -->
        <footer class="bg-gray-900 dark:bg-gray-950 text-gray-400 mt-16">
            <div class="max-w-7xl mx-auto px-4 py-12">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-8">
                    <!-- Brand -->
                    <div class="md:col-span-1">
                        <img
                            src="/images/kromerce-business-text.png"
                            alt="Kromerce"
                            class="h-8 w-auto object-contain filter brightness-0 invert mb-4"
                        />
                        <p class="text-sm leading-relaxed mb-4">{{ t('storefront.footer.about.description') }}</p>
                        <div class="flex gap-3">
                            <a href="#" class="w-8 h-8 bg-gray-800 dark:bg-gray-700 rounded-full flex items-center justify-center hover:bg-blue-600 transition-colors">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                            </a>
                            <a href="#" class="w-8 h-8 bg-gray-800 dark:bg-gray-700 rounded-full flex items-center justify-center hover:bg-sky-500 transition-colors">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/></svg>
                            </a>
                            <a href="#" class="w-8 h-8 bg-gray-800 dark:bg-gray-700 rounded-full flex items-center justify-center hover:bg-pink-600 transition-colors">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 0C8.74 0 8.333.015 7.053.072 5.775.132 4.905.333 4.14.63c-.789.306-1.459.717-2.126 1.384S.935 3.35.63 4.14C.333 4.905.131 5.775.072 7.053.012 8.333 0 8.74 0 12s.015 3.667.072 4.947c.06 1.277.261 2.148.558 2.913.306.788.717 1.459 1.384 2.126.667.666 1.336 1.079 2.126 1.384.766.296 1.636.499 2.913.558C8.333 23.988 8.74 24 12 24s3.667-.015 4.947-.072c1.277-.06 2.148-.262 2.913-.558.788-.306 1.459-.718 2.126-1.384.666-.667 1.079-1.335 1.384-2.126.296-.765.499-1.636.558-2.913.06-1.28.072-1.687.072-4.947s-.015-3.667-.072-4.947c-.06-1.277-.262-2.149-.558-2.913-.306-.789-.718-1.459-1.384-2.126C21.319 1.347 20.651.935 19.86.63c-.765-.297-1.636-.499-2.913-.558C15.667.012 15.26 0 12 0zm0 2.16c3.203 0 3.585.016 4.85.071 1.17.055 1.805.249 2.227.415.562.217.96.477 1.382.896.419.42.679.819.896 1.381.164.422.36 1.057.413 2.227.057 1.266.07 1.646.07 4.85s-.015 3.585-.074 4.85c-.061 1.17-.256 1.805-.421 2.227-.224.562-.479.96-.899 1.382-.419.419-.824.679-1.38.896-.42.164-1.065.36-2.235.413-1.274.057-1.649.07-4.859.07-3.211 0-3.586-.015-4.859-.074-1.171-.061-1.816-.256-2.236-.421-.569-.224-.96-.479-1.379-.899-.421-.419-.69-.824-.9-1.38-.165-.42-.359-1.065-.42-2.235-.045-1.26-.061-1.649-.061-4.844 0-3.196.016-3.586.061-4.861.061-1.17.255-1.814.42-2.234.21-.57.479-.96.9-1.381.419-.419.81-.689 1.379-.898.42-.166 1.051-.361 2.221-.421 1.275-.045 1.65-.06 4.859-.06l.045.03zm0 3.678c-3.405 0-6.162 2.76-6.162 6.162 0 3.405 2.76 6.162 6.162 6.162 3.405 0 6.162-2.76 6.162-6.162 0-3.405-2.76-6.162-6.162-6.162zM12 16c-2.21 0-4-1.79-4-4s1.79-4 4-4 4 1.79 4 4-1.79 4-4 4zm7.846-10.405c0 .795-.646 1.44-1.44 1.44-.795 0-1.44-.646-1.44-1.44 0-.794.646-1.439 1.44-1.439.793-.001 1.44.645 1.44 1.439z"/></svg>
                            </a>
                        </div>
                    </div>

                    <!-- Customer Service -->
                    <div>
                        <h3 class="text-white font-semibold mb-4 text-sm">{{ t('storefront.footer.customer_service.title') }}</h3>
                        <ul class="space-y-2 text-sm">
                            <li><a href="#" class="hover:text-white transition-colors">{{ t('storefront.footer.customer_service.help_center') }}</a></li>
                            <li><a href="#" class="hover:text-white transition-colors">{{ t('storefront.footer.customer_service.track_order') }}</a></li>
                            <li><a href="#" class="hover:text-white transition-colors">{{ t('storefront.footer.customer_service.returns') }}</a></li>
                            <li><a href="#" class="hover:text-white transition-colors">{{ t('storefront.footer.customer_service.shipping') }}</a></li>
                        </ul>
                    </div>

                    <!-- For Sellers -->
                    <div>
                        <h3 class="text-white font-semibold mb-4 text-sm">{{ t('storefront.footer.for_sellers.title') }}</h3>
                        <ul class="space-y-2 text-sm">
                            <li><Link href="/stores" class="hover:text-white transition-colors">{{ t('storefront.footer.for_sellers.start_selling') }}</Link></li>
                            <li><Link href="/business" class="hover:text-white transition-colors">{{ t('storefront.footer.for_sellers.seller_dashboard') }}</Link></li>
                            <li><a href="#" class="hover:text-white transition-colors">{{ t('storefront.footer.for_sellers.seller_support') }}</a></li>
                        </ul>
                    </div>

                    <!-- Quick Links -->
                    <div>
                        <h3 class="text-white font-semibold mb-4 text-sm">{{ t('storefront.footer.quick_links.title') }}</h3>
                        <ul class="space-y-2 text-sm">
                            <li><Link href="/welcome" class="hover:text-white transition-colors">{{ t('storefront.footer.quick_links.about_us') }}</Link></li>
                            <li><a href="#" class="hover:text-white transition-colors">{{ t('storefront.footer.quick_links.faq') }}</a></li>
                            <li><a href="#" class="hover:text-white transition-colors">{{ t('storefront.footer.quick_links.terms') }}</a></li>
                            <li><a href="#" class="hover:text-white transition-colors">{{ t('storefront.footer.quick_links.privacy') }}</a></li>
                        </ul>
                    </div>
                </div>

                <div class="border-t border-gray-800 pt-6 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs">
                    <p>{{ t('storefront.footer.copyright', { year: currentYear }) }}</p>
                    <div class="flex items-center gap-4">
                        <span class="flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                            SSL Secured
                        </span>
                    </div>
                </div>
            </div>
        </footer>
    </div>
</template>
