import { computed } from 'vue';
import { useI18n } from 'vue-i18n';
import { useAuth } from './useAuth.js';
import { usePage } from '@inertiajs/vue3';
import { BusinessIcons, CustomerIcons } from '@/icons';
import {useTranslations} from "@/composables/useTranslations.js";

export function useNavigation() {
    const page = usePage();
    const { t } = useI18n();
    useTranslations('dashboard_nav');

    // Customer navigation items (for navbars with SVG icons)
    const customerNavigation = computed(() => [
        { href: '/dashboard', label: 'dashboard_nav.dashboard', name: 'dashboard' },
        { href: '#stores', label: 'dashboard_nav.stores', name: 'stores' },
        { href: '#orders', label: 'dashboard_nav.my_orders', name: 'orders' },
        { href: '#wishlist', label: 'dashboard_nav.wishlist', name: 'wishlist' },
        { href: '#deals', label: 'dashboard_nav.deals', name: 'settings' },
    ]);

    // Business navigation items (for navbars with SVG icons)
    const businessNavigation = computed(() => [
        { href: '/dashboard', label: 'dashboard_nav.dashboard', name: 'dashboard' },
        { href: '/products', label: 'dashboard_nav.products', name: 'products' },
        { href: '#orders', label: 'dashboard_nav.orders', name: 'orders' },
        { href: '#analytics', label: 'dashboard_nav.analytics', name: 'analytics' },
        { href: '#marketing', label: 'dashboard_nav.marketing', name: 'marketing' },
        { href: '#settings', label: 'dashboard_nav.settings', name: 'settings' },
    ]);

    // Admin navigation items (for navbars with SVG icons)
    const adminNavigation = computed(() => [
        { href: '/dashboard', label: 'dashboard_nav.dashboard', name: 'settings' },
        { href: '#users', label: 'dashboard_nav.users', name: 'customers' },
        { href: '#stores', label: 'dashboard_nav.stores', name: 'customers' },
        { href: '#analytics', label: 'dashboard_nav.analytics', name: 'analytics' },
        { href: '#settings', label: 'dashboard_nav.settings', name: 'settings' },
    ]);

    // Get navigation items based on user role (for navbars)
    const navigationItems = computed(() => {
        // Get auth values inside computed to ensure reactivity
        const { isCustomer, isBusinessUser, isSuperAdmin } = useAuth();

        if (!page.props.auth?.user) {
            return [];
        }

        if (isSuperAdmin.value) {
            return adminNavigation.value;
        } else if (isBusinessUser.value) {
            return businessNavigation.value;
        } else if (isCustomer.value) {
            return customerNavigation.value;
        }

        return [];
    });

    // Get navigation items for sidebars (with SVG icon names)
    const sidebarNavigationItems = computed(() => {
        // Get auth values inside computed to ensure reactivity
        const { isCustomer, isBusinessUser, isSuperAdmin } = useAuth();
        const currentUrl = page.url;

        if (!page.props.auth?.user) {
            return [];
        }

        if (isCustomer.value) {
            return [
                {
                    name: 'dashboard',
                    label: t('dashboard_nav.dashboard'),
                    icon: CustomerIcons.dashboard,
                    href: '/dashboard',
                    active: currentUrl.startsWith('/dashboard')
                },
                {
                    name: 'stores',
                    label: t('dashboard_nav.stores'),
                    icon: CustomerIcons.stores,
                    href: '/stores',
                    active: currentUrl.startsWith('/stores')
                },
                {
                    name: 'orders',
                    label: t('dashboard_nav.my_orders'),
                    icon: CustomerIcons.orders,
                    href: '/orders',
                    active: currentUrl.startsWith('/orders')
                },
                {
                    name: 'wishlist',
                    label: t('dashboard_nav.wishlist'),
                    icon: CustomerIcons.wishlist,
                    href: '/wishlist',
                    active: currentUrl.startsWith('/wishlist'),
                    badge: 3
                },
                {
                    name: 'profile',
                    label: t('dashboard_nav.profile'),
                    icon: CustomerIcons.profile,
                    href: '/profile',
                    active: currentUrl.startsWith('/profile')
                },
                {
                    name: 'settings',
                    label: t('dashboard_nav.settings'),
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
                    label: t('dashboard_nav.dashboard'),
                    icon: BusinessIcons.dashboard,
                    href: '/dashboard',
                    active: currentUrl.startsWith('/dashboard')
                },
                {
                    name: 'products',
                    label: t('dashboard_nav.products'),
                    icon: BusinessIcons.products,
                    href: '/products',
                    active: currentUrl.startsWith('/products')
                },
                {
                    name: 'orders',
                    label: t('dashboard_nav.orders'),
                    icon: BusinessIcons.orders,
                    href: '/orders',
                    active: currentUrl.startsWith('/orders')
                },
                {
                    name: 'inventory',
                    label: t('dashboard_nav.inventory'),
                    icon: BusinessIcons.inventory,
                    href: '/inventory',
                    active: currentUrl.startsWith('/inventory')
                },
                {
                    name: 'customers',
                    label: t('dashboard_nav.customers'),
                    icon: BusinessIcons.customers,
                    href: '/customers',
                    active: currentUrl.startsWith('/customers')
                },
                {
                    name: 'analytics',
                    label: t('dashboard_nav.analytics'),
                    icon: BusinessIcons.analytics,
                    href: '/analytics',
                    active: currentUrl.startsWith('/analytics')
                },
                {
                    name: 'marketing',
                    label: t('dashboard_nav.marketing'),
                    icon: BusinessIcons.marketing,
                    href: '/marketing',
                    active: currentUrl.startsWith('/marketing')
                },
                {
                    name: 'reports',
                    label: t('dashboard_nav.reports'),
                    icon: BusinessIcons.reports,
                    href: '/reports',
                    active: currentUrl.startsWith('/reports')
                },
                {
                    name: 'settings',
                    label: t('dashboard_nav.settings'),
                    icon: BusinessIcons.settings,
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
