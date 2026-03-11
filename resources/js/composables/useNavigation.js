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

    // Customer navigation items (for navbars with SVG icons)
    const customerNavigation = computed(() => [
        { href: '/dashboard', label: 'dashboard.nav_dashboard', name: 'dashboard' },
        { href: '#stores', label: 'dashboard.nav_stores', name: 'stores' },
        { href: '#orders', label: 'dashboard.nav_my_orders', name: 'orders' },
        { href: '#wishlist', label: 'dashboard.nav_wishlist', name: 'wishlist' },
        { href: '#deals', label: 'dashboard.nav_deals', name: 'settings' },
    ]);

    // Business navigation items (for navbars with SVG icons)
    const businessNavigation = computed(() => [
        { href: '/dashboard', label: 'dashboard.nav_dashboard', name: 'dashboard' },
        { href: '/products', label: 'dashboard.nav_products', name: 'products' },
        { href: '#orders', label: 'dashboard.nav_orders', name: 'orders' },
        { href: '#analytics', label: 'dashboard.nav_analytics', name: 'analytics' },
        { href: '#marketing', label: 'dashboard.nav_marketing', name: 'marketing' },
        { href: '#settings', label: 'dashboard.nav_settings', name: 'settings' },
    ]);

    // Admin navigation items (for navbars with SVG icons)
    const adminNavigation = computed(() => [
        { href: '/dashboard', label: 'dashboard.nav_dashboard', name: 'settings' },
        { href: '#users', label: 'dashboard.nav_users', name: 'customers' },
        { href: '#tenants', label: 'dashboard.nav_tenants', name: 'customers' },
        { href: '#analytics', label: 'dashboard.nav_analytics', name: 'analytics' },
        { href: '#settings', label: 'dashboard.nav_settings', name: 'settings' },
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
