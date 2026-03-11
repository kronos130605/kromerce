import { computed } from 'vue';
import { useAuth } from './useAuth.js';
import { usePage } from '@inertiajs/vue3';
import { BusinessIcons, CustomerIcons } from '@/icons';

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
        { href: '/products', label: 'dashboard.nav_products', icon: '📦' },
        { href: '#orders', label: 'dashboard.nav_orders', icon: '🛒' },
        { href: '#analytics', label: 'dashboard.nav_analytics', icon: '📈' },
        { href: '#marketing', label: 'dashboard.nav_marketing', icon: '📢' },
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
                    icon: CustomerIcons.dashboard,
                    href: '/dashboard',
                    active: currentUrl.startsWith('/dashboard')
                },
                { 
                    name: 'stores', 
                    label: 'Stores', 
                    icon: CustomerIcons.stores,
                    href: '/stores',
                    active: currentUrl.startsWith('/stores')
                },
                { 
                    name: 'orders', 
                    label: 'My Orders', 
                    icon: CustomerIcons.orders,
                    href: '/orders',
                    active: currentUrl.startsWith('/orders')
                },
                { 
                    name: 'wishlist', 
                    label: 'Wishlist', 
                    icon: CustomerIcons.wishlist,
                    href: '/wishlist',
                    active: currentUrl.startsWith('/wishlist'),
                    badge: 3
                },
                { 
                    name: 'profile', 
                    label: 'Profile', 
                    icon: CustomerIcons.profile,
                    href: '/profile',
                    active: currentUrl.startsWith('/profile')
                },
                { 
                    name: 'settings', 
                    label: 'Settings', 
                    icon: CustomerIcons.settings,
                    href: '/settings',
                    active: currentUrl.startsWith('/settings')
                }
            ];
        }
        
        if (isBusinessUser.value) {
            return [
                { 
                    name: 'dashboard', 
                    label: 'Dashboard', 
                    icon: BusinessIcons.dashboard,
                    href: '/dashboard',
                    active: currentUrl.startsWith('/dashboard')
                },
                { 
                    name: 'products', 
                    label: 'Products', 
                    icon: BusinessIcons.products,
                    href: '/products',
                    active: currentUrl.startsWith('/products')
                },
                { 
                    name: 'orders', 
                    label: 'Orders', 
                    icon: BusinessIcons.orders,
                    href: '#orders',
                    active: currentUrl.includes('/orders')
                },
                { 
                    name: 'inventory', 
                    label: 'Inventory', 
                    icon: BusinessIcons.inventory,
                    href: '#inventory',
                    active: currentUrl.includes('/inventory')
                },
                { 
                    name: 'customers', 
                    label: 'Customers', 
                    icon: BusinessIcons.customers,
                    href: '#customers',
                    active: currentUrl.includes('/customers')
                },
                { 
                    name: 'analytics', 
                    label: 'Analytics', 
                    icon: BusinessIcons.analytics,
                    href: '#analytics',
                    active: currentUrl.includes('/analytics')
                },
                { 
                    name: 'marketing', 
                    label: 'Marketing', 
                    icon: BusinessIcons.marketing,
                    href: '#marketing',
                    active: currentUrl.includes('/marketing')
                },
                { 
                    name: 'reports', 
                    label: 'Reports', 
                    icon: BusinessIcons.reports,
                    href: '#reports',
                    active: currentUrl.includes('/reports')
                },
                { 
                    name: 'settings', 
                    label: 'Settings', 
                    icon: BusinessIcons.settings,
                    href: '#settings',
                    active: currentUrl.includes('/settings')
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
