import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { router } from '@inertiajs/vue3';

/**
 * Role-based navigation guard composable
 * Provides methods to check user roles and redirect unauthorized access
 */
export function useRoleGuard() {
    const page = usePage();
    
    /**
     * Get current user's role in current tenant
     */
    const currentUserRole = computed(() => {
        return page.props.user_role || 'customer';
    });
    
    /**
     * Check if user has business role
     */
    const isBusinessUser = computed(() => {
        const businessRoles = ['business_owner', 'owner', 'admin', 'manager', 'employee'];
        return businessRoles.includes(currentUserRole.value);
    });
    
    /**
     * Check if user can access business routes
     */
    const canAccessBusiness = computed(() => {
        return isBusinessUser.value;
    });
    
    /**
     * Guard function to protect business routes
     */
    const requireBusinessRole = (fallbackRoute = 'dashboard') => {
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
        requireBusinessRole,
        requireCustomerRole,
        isRouteAccessible
    };
}
