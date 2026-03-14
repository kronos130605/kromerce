import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { router } from '@inertiajs/vue3';
import {useAuth} from "@/composables/useAuth.js";

/**
 * Role-based navigation guard composable
 * Provides methods to check user roles and redirect unauthorized access
 */
export function useRoleGuard() {
    const page = usePage();
    const { isBusinessUser, isCustomer, userRole } = useAuth();

    /**
     * Get current user's role in current store
     */
    const currentUserRole = computed(() => userRole.value || 'customer');

    /**
     * Check if user can access business routes
     */
    const canAccessBusiness = computed(() => isBusinessUser.value);

    /**
     * Check if user can access customer routes
     */
    const canAccessCustomer = computed(() => isCustomer.value);

    /**
     * Guard function to protect business routes
     */
    const requireBusinessRole = (fallbackRoute = 'dashboard') => {
        // Wait for props to be loaded during navigation
        if (page.props.user_role === undefined) {
            return true; // Allow navigation to complete
        }

        if (!canAccessBusiness.value) {
            router.visit(fallbackRoute);
            return false;
        }
        return true;
    };

    /**
     * Guard function to protect customer routes (if needed)
     */
    const requireCustomerRole = (fallbackRoute = 'dashboard') => {
        if (isBusinessUser.value) {
            router.visit(fallbackRoute);
            return false;
        }
        return true;
    };

    /**
     * Check if current route is accessible
     */
    const isRouteAccessible = (routeName) => {
        // Business-only routes
        const businessRoutes = [
            'products.index',
            'products.create',
            'products.store',
            'products.show',
            'products.edit',
            'products.update',
            'products.destroy',
            'business.dashboard'
        ];

        if (businessRoutes.includes(routeName)) {
            return canAccessBusiness.value;
        }

        return true; // Other routes are accessible to all authenticated users
    };

    return {
        currentUserRole,
        isBusinessUser,
        canAccessBusiness,
        canAccessCustomer,
        requireBusinessRole,
        requireCustomerRole,
        isRouteAccessible
    };
}
