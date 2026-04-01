<script setup>
import { ref, computed } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import { useDarkMode } from '@/composables/useDarkMode';
import { useTranslations } from '@/composables/useTranslations';
import { useCart } from '@/composables/useCart';
import LanguageSelector from '@/components/shared/LanguageSelector.vue';

const { t } = useTranslations();
useTranslations('storefront');
const { isDark, toggleDarkMode } = useDarkMode();
const { cartCount } = useCart();
const showMobileMenu = ref(false);
const showCartPreview = ref(false);
const { cartItems, cartTotal, removeFromCart, updateQuantity } = useCart();

const categories = [
    { name: 'Electronics', slug: 'electronics', icon: '💻' },
    { name: 'Fashion', slug: 'fashion', icon: '👕' },
    { name: 'Home & Garden', slug: 'home-garden', icon: '🏡' },
    { name: 'Sports', slug: 'sports', icon: '⚽' },
    { name: 'Books', slug: 'books', icon: '📚' },
    { name: 'Toys', slug: 'toys', icon: '🧸' },
    { name: 'Beauty', slug: 'beauty', icon: '💄' },
    { name: 'Automotive', slug: 'automotive', icon: '🚗' },
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
        <div class="bg-gray-900 dark:bg-gray-950 text-gray-300 text-xs border-b border-gray-700">
            <div class="max-w-7xl mx-auto px-4 py-2 flex items-center justify-between gap-4">
                <!-- Left: Promo Info -->
                <div class="hidden md:flex items-center gap-5">
                    <span class="flex items-center gap-1.5 text-emerald-400 font-medium">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/>
                        </svg>
                        {{ t('storefront.topbar.free_shipping') }}
                    </span>
                    <span class="text-gray-600">|</span>
                    <span class="flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                        {{ t('storefront.topbar.secure_payment') }}
                    </span>
                    <span class="text-gray-600">|</span>
                    <span class="flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                        {{ t('storefront.topbar.support') }}
                    </span>
                </div>

                <!-- Right: Actions -->
                <div class="flex items-center gap-4 ml-auto">
                    <LanguageSelector />
                    <button
                        @click="toggleDarkMode"
                        class="hover:text-white transition-colors"
                        :title="isDark ? 'Light Mode' : 'Dark Mode'"
                    >
                        <span>{{ isDark ? '☀️' : '🌙' }}</span>
                    </button>
                    <span class="text-gray-600 hidden sm:block">|</span>
                    <Link href="/stores" class="hidden sm:block hover:text-white transition-colors">
                        {{ t('storefront.footer.for_sellers.start_selling') }}
                    </Link>
                    <span class="text-gray-600 hidden sm:block">|</span>
                    <template v-if="$page.props.auth?.user">
                        <Link href="/business" class="hover:text-white transition-colors flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            {{ t('storefront.navigation.account') }}
                        </Link>
                    </template>
                    <template v-else>
                        <Link href="/login" class="hover:text-white transition-colors">{{ t('storefront.navigation.login') }}</Link>
                        <Link href="/register" class="px-2.5 py-0.5 bg-blue-600 hover:bg-blue-500 text-white rounded-full transition-colors font-medium">
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

                    <!-- Search Bar -->
                    <form action="/search" method="GET" class="flex-1 flex items-center">
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

                    <!-- Right Actions -->
                    <div class="flex items-center gap-1 flex-shrink-0">
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
                                <span class="hidden sm:block text-sm font-medium text-gray-700 dark:text-gray-300">{{ t('storefront.navigation.cart') }}</span>
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

                    <!-- Categories Navigation -->
                    <nav class="hidden lg:flex items-center gap-1 py-2 border-t border-gray-100 dark:border-gray-700 overflow-x-auto scrollbar-none">
                        <Link
                            href="/products"
                            class="flex-shrink-0 px-3 py-1.5 text-xs font-semibold text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-900/30 rounded-full transition-colors hover:bg-blue-100 dark:hover:bg-blue-900/50"
                        >
                            {{ t('storefront.navigation.all_products') }}
                        </Link>
                        <Link
                            v-for="category in categories"
                            :key="category.slug"
                            :href="`/category/${category.slug}`"
                            class="flex-shrink-0 flex items-center gap-1.5 px-3 py-1.5 text-xs text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-full transition-colors"
                        >
                            <span>{{ category.icon }}</span>
                            <span>{{ category.name }}</span>
                        </Link>
                    </nav>
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
            <div v-if="showMobileMenu" class="lg:hidden bg-white dark:bg-gray-800 border-b border-gray-100 dark:border-gray-700 shadow-sm">
                <div class="max-w-7xl mx-auto px-4 py-3">
                    <div class="grid grid-cols-3 gap-2">
                        <Link
                            v-for="category in categories"
                            :key="category.slug"
                            :href="`/category/${category.slug}`"
                            class="flex items-center gap-2 px-3 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg"
                        >
                            <span>{{ category.icon }}</span>
                            <span class="text-xs">{{ category.name }}</span>
                        </Link>
                    </div>
                </div>
            </div>
        </Transition>

        <!-- Click outside overlay for cart -->
        <div v-if="showCartPreview" class="fixed inset-0 z-30" @click="showCartPreview = false" />

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
