import { computed } from 'vue';
import { useAuth } from './useAuth.js';
import { usePage } from '@inertiajs/vue3';

export function useNavigation() {
    const { isCustomer, isBusinessUser, isSuperAdmin } = useAuth();
    const page = usePage();

    // Debug: Verificar roles y navegación
    console.log('useNavigation - isCustomer:', isCustomer.value);
    console.log('useNavigation - isBusinessUser:', isBusinessUser.value);
    console.log('useNavigation - isSuperAdmin:', isSuperAdmin.value);

    // Customer navigation items (for navbars with emojis)
    const customerNavigation = computed(() => [
        { href: '/dashboard', label: 'dashboard.nav_dashboard', icon: '🏠' },
        { href: '#stores', label: 'dashboard.nav_stores', icon: '🏪' },
        { href: '#orders', label: 'dashboard.nav_my_orders', icon: '📦' },
        { href: '#wishlist', label: 'dashboard.nav_wishlist', icon: '❤️' },
        { href: '#deals', label: 'dashboard.nav_deals', icon: '🎯' },
    ]);

    // Business navigation items (for navbars with emojis)
    const businessNavigation = computed(() => [
        { href: '/dashboard', label: 'dashboard.nav_dashboard', icon: '📊' },
        { href: '/test-products', label: 'dashboard.nav_products', icon: '📦' },
        { href: '#orders', label: 'dashboard.nav_orders', icon: '🛒' },
        { href: '#analytics', label: 'dashboard.nav_analytics', icon: '📈' },
        { href: '#settings', label: 'dashboard.nav_settings', icon: '⚙️' },
    ]);

    // Admin navigation items (for navbars with emojis)
    const adminNavigation = computed(() => [
        { href: '/dashboard', label: 'dashboard.nav_dashboard', icon: '🔧' },
        { href: '#users', label: 'dashboard.nav_users', icon: '👥' },
        { href: '#tenants', label: 'dashboard.nav_tenants', icon: '🏢' },
        { href: '#analytics', label: 'dashboard.nav_analytics', icon: '📊' },
        { href: '#settings', label: 'dashboard.nav_settings', icon: '⚙️' },
    ]);

    // Get navigation items based on user role (for navbars)
    const navigationItems = computed(() => {
        if (isCustomer.value) {
            return customerNavigation.value;
        } else if (isBusinessUser.value) {
            return businessNavigation.value;
        } else if (isSuperAdmin.value) {
            return adminNavigation.value;
        }
        return [];
    });

    // Get navigation items for sidebars (with SVG icon names)
    const sidebarNavigationItems = computed(() => {
        const currentUrl = page.url;
        
        if (isCustomer.value) {
            return [
                { 
                    name: 'dashboard', 
                    label: 'Dashboard', 
                    icon: 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6', 
                    href: '/dashboard',
                    active: currentUrl.startsWith('/dashboard')
                },
                { 
                    name: 'stores', 
                    label: 'Stores', 
                    icon: 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4', 
                    href: '/stores',
                    active: currentUrl.startsWith('/stores')
                },
                { 
                    name: 'orders', 
                    label: 'My Orders', 
                    icon: 'M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z', 
                    href: '/orders',
                    active: currentUrl.startsWith('/orders')
                },
                { 
                    name: 'wishlist', 
                    label: 'Wishlist', 
                    icon: 'M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z', 
                    href: '/wishlist',
                    active: currentUrl.startsWith('/wishlist'),
                    badge: 3
                },
                { 
                    name: 'profile', 
                    label: 'Profile', 
                    icon: 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z', 
                    href: '/profile',
                    active: currentUrl.startsWith('/profile')
                },
                { 
                    name: 'settings', 
                    label: 'Settings', 
                    icon: 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z', 
                    href: '/settings',
                    active: currentUrl.startsWith('/settings')
                }
            ];
        }
        return [];
    });

    return {
        // Navigation items for navbars (with emojis and translation keys)
        navigationItems,
        
        // Navigation items for sidebars (with SVG icons and labels)
        sidebarNavigationItems,
        
        // Individual navigation sets
        customerNavigation,
        businessNavigation,
        adminNavigation,
    };
}
