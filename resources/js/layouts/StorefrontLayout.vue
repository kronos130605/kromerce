<script setup>
import { ref, computed } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import { useDarkMode } from '@/composables/useDarkMode';
import { useTranslations } from '@/composables/useTranslations';
import LanguageSelector from '@/components/shared/LanguageSelector.vue';

const { t } = useTranslations();
const page = usePage();
const { isDark, toggleDarkMode } = useDarkMode();
const showMobileMenu = ref(false);

// Load storefront translations
useTranslations('storefront');

const categories = [
    { name: 'Electronics', slug: 'electronics', icon: '💻' },
    { name: 'Fashion', slug: 'fashion', icon: '👕' },
    { name: 'Home & Garden', slug: 'home-garden', icon: '🏡' },
    { name: 'Sports', slug: 'sports', icon: '⚽' },
    { name: 'Books', slug: 'books', icon: '📚' },
    { name: 'Toys', slug: 'toys', icon: '🧸' },
];

const currentYear = computed(() => new Date().getFullYear());
</script>

<template>
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900">
        <!-- Top Bar -->
        <div class="bg-blue-600 dark:bg-blue-700 text-white text-sm">
            <div class="max-w-7xl mx-auto px-4 py-2 flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <span>{{ t('storefront.navigation.home') }}</span>
                </div>
                <div class="flex items-center gap-4">
                    <LanguageSelector />
                    <button
                        @click="toggleDarkMode"
                        class="p-1 hover:bg-blue-700 dark:hover:bg-blue-600 rounded transition-colors"
                        :title="isDark ? 'Light Mode' : 'Dark Mode'"
                    >
                        <span class="text-lg">{{ isDark ? '☀️' : '🌙' }}</span>
                    </button>
                    <Link href="/welcome" class="hover:underline">{{ t('storefront.footer.quick_links.about_us') }}</Link>
                    <Link href="/stores" class="hover:underline">{{ t('storefront.footer.for_sellers.start_selling') }}</Link>
                    <template v-if="$page.props.auth.user">
                        <Link href="/business" class="hover:underline">{{ t('storefront.navigation.account') }}</Link>
                    </template>
                    <template v-else>
                        <Link href="/login" class="hover:underline">{{ t('storefront.navigation.login') }}</Link>
                        <Link href="/register" class="hover:underline">{{ t('storefront.navigation.register') }}</Link>
                    </template>
                </div>
            </div>
        </div>

        <!-- Main Header -->
        <header class="bg-white dark:bg-gray-800 shadow-sm sticky top-0 z-40">
            <div class="max-w-7xl mx-auto px-4">
                <div class="flex items-center justify-between h-16">
                    <!-- Logo -->
                    <Link href="/" class="flex items-center group">
                        <div class="transition-transform duration-300 group-hover:scale-110">
                            <img
                                src="/images/kromerce-business-text.png"
                                alt="Kromerce"
                                :class="[
                                    'h-8 w-auto object-contain transition-transform duration-300',
                                    isDark ? 'filter brightness-0 invert' : ''
                                ]"
                            />
                        </div>
                    </Link>

                    <!-- Search Bar -->
                    <div class="flex-1 max-w-2xl mx-8">
                        <form action="/search" method="GET" class="relative">
                            <input
                                type="text"
                                name="q"
                                :placeholder="t('storefront.navigation.search_placeholder')"
                                class="w-full px-4 py-2 pl-10 pr-4 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            />
                            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </form>
                    </div>

                    <!-- Right Actions -->
                    <div class="flex items-center gap-4">
                        <!-- Cart -->
                        <button class="relative p-2 text-gray-700 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                            <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center">0</span>
                        </button>

                        <!-- Wishlist -->
                        <button class="p-2 text-gray-700 dark:text-gray-300 hover:text-red-600 dark:hover:text-red-400">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                            </svg>
                        </button>

                        <!-- Mobile Menu Toggle -->
                        <button @click="showMobileMenu = !showMobileMenu" class="lg:hidden p-2">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Categories Navigation -->
                <nav class="hidden lg:flex items-center gap-6 py-3 border-t border-gray-200 dark:border-gray-700">
                    <Link
                        v-for="category in categories"
                        :key="category.slug"
                        :href="`/category/${category.slug}`"
                        class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 transition-colors"
                    >
                        <span>{{ category.icon }}</span>
                        <span>{{ category.name }}</span>
                    </Link>
                </nav>
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
            <div v-if="showMobileMenu" class="lg:hidden bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
                <div class="max-w-7xl mx-auto px-4 py-4 space-y-2">
                    <Link
                        v-for="category in categories"
                        :key="category.slug"
                        :href="`/category/${category.slug}`"
                        class="flex items-center gap-2 px-4 py-2 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg"
                    >
                        <span>{{ category.icon }}</span>
                        <span>{{ category.name }}</span>
                    </Link>
                </div>
            </div>
        </Transition>

        <!-- Main Content -->
        <main>
            <slot />
        </main>

        <!-- Footer -->
        <footer class="bg-gray-800 dark:bg-gray-950 text-gray-300 mt-16">
            <div class="max-w-7xl mx-auto px-4 py-12">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                    <!-- About -->
                    <div>
                        <h3 class="text-white font-bold mb-4">{{ t('storefront.footer.about.title') }}</h3>
                        <p class="text-sm">{{ t('storefront.footer.about.description') }}</p>
                    </div>

                    <!-- Customer Service -->
                    <div>
                        <h3 class="text-white font-bold mb-4">{{ t('storefront.footer.customer_service.title') }}</h3>
                        <ul class="space-y-2 text-sm">
                            <li><a href="#" class="hover:text-white">{{ t('storefront.footer.customer_service.help_center') }}</a></li>
                            <li><a href="#" class="hover:text-white">{{ t('storefront.footer.customer_service.track_order') }}</a></li>
                            <li><a href="#" class="hover:text-white">{{ t('storefront.footer.customer_service.returns') }}</a></li>
                            <li><a href="#" class="hover:text-white">{{ t('storefront.footer.customer_service.shipping') }}</a></li>
                        </ul>
                    </div>

                    <!-- For Sellers -->
                    <div>
                        <h3 class="text-white font-bold mb-4">{{ t('storefront.footer.for_sellers.title') }}</h3>
                        <ul class="space-y-2 text-sm">
                            <li><Link href="/stores" class="hover:text-white">{{ t('storefront.footer.for_sellers.start_selling') }}</Link></li>
                            <li><Link href="/business" class="hover:text-white">{{ t('storefront.footer.for_sellers.seller_dashboard') }}</Link></li>
                            <li><a href="#" class="hover:text-white">{{ t('storefront.footer.for_sellers.seller_support') }}</a></li>
                        </ul>
                    </div>

                    <!-- Follow Us -->
                    <div>
                        <h3 class="text-white font-bold mb-4">Follow Us</h3>
                        <div class="flex gap-4">
                            <a href="#" class="w-10 h-10 bg-gray-700 rounded-full flex items-center justify-center hover:bg-blue-600 transition-colors">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                            </a>
                            <a href="#" class="w-10 h-10 bg-gray-700 rounded-full flex items-center justify-center hover:bg-blue-400 transition-colors">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/></svg>
                            </a>
                            <a href="#" class="w-10 h-10 bg-gray-700 rounded-full flex items-center justify-center hover:bg-pink-600 transition-colors">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 0C8.74 0 8.333.015 7.053.072 5.775.132 4.905.333 4.14.63c-.789.306-1.459.717-2.126 1.384S.935 3.35.63 4.14C.333 4.905.131 5.775.072 7.053.012 8.333 0 8.74 0 12s.015 3.667.072 4.947c.06 1.277.261 2.148.558 2.913.306.788.717 1.459 1.384 2.126.667.666 1.336 1.079 2.126 1.384.766.296 1.636.499 2.913.558C8.333 23.988 8.74 24 12 24s3.667-.015 4.947-.072c1.277-.06 2.148-.262 2.913-.558.788-.306 1.459-.718 2.126-1.384.666-.667 1.079-1.335 1.384-2.126.296-.765.499-1.636.558-2.913.06-1.28.072-1.687.072-4.947s-.015-3.667-.072-4.947c-.06-1.277-.262-2.149-.558-2.913-.306-.789-.718-1.459-1.384-2.126C21.319 1.347 20.651.935 19.86.63c-.765-.297-1.636-.499-2.913-.558C15.667.012 15.26 0 12 0zm0 2.16c3.203 0 3.585.016 4.85.071 1.17.055 1.805.249 2.227.415.562.217.96.477 1.382.896.419.42.679.819.896 1.381.164.422.36 1.057.413 2.227.057 1.266.07 1.646.07 4.85s-.015 3.585-.074 4.85c-.061 1.17-.256 1.805-.421 2.227-.224.562-.479.96-.899 1.382-.419.419-.824.679-1.38.896-.42.164-1.065.36-2.235.413-1.274.057-1.649.07-4.859.07-3.211 0-3.586-.015-4.859-.074-1.171-.061-1.816-.256-2.236-.421-.569-.224-.96-.479-1.379-.899-.421-.419-.69-.824-.9-1.38-.165-.42-.359-1.065-.42-2.235-.045-1.26-.061-1.649-.061-4.844 0-3.196.016-3.586.061-4.861.061-1.17.255-1.814.42-2.234.21-.57.479-.96.9-1.381.419-.419.81-.689 1.379-.898.42-.166 1.051-.361 2.221-.421 1.275-.045 1.65-.06 4.859-.06l.045.03zm0 3.678c-3.405 0-6.162 2.76-6.162 6.162 0 3.405 2.76 6.162 6.162 6.162 3.405 0 6.162-2.76 6.162-6.162 0-3.405-2.76-6.162-6.162-6.162zM12 16c-2.21 0-4-1.79-4-4s1.79-4 4-4 4 1.79 4 4-1.79 4-4 4zm7.846-10.405c0 .795-.646 1.44-1.44 1.44-.795 0-1.44-.646-1.44-1.44 0-.794.646-1.439 1.44-1.439.793-.001 1.44.645 1.44 1.439z"/></svg>
                            </a>
                        </div>
                    </div>
                </div>

                <div class="border-t border-gray-700 mt-8 pt-8 text-sm text-center">
                    <p>{{ t('storefront.footer.copyright', { year: currentYear }) }}</p>
                </div>
            </div>
        </footer>
    </div>
</template>
