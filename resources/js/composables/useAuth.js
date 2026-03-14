import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';

export function useAuth() {
    const page = usePage();
    const user = computed(() => page.props.auth?.user);
    const currentStore = computed(() => page.props.current_store);
    const userRole = computed(() => page.props.user_role);

    // Check user roles - usando user_role directamente
    const isCustomer = computed(() => userRole.value === 'customer');

    const isBusinessOwner = computed(() => {
        const role = userRole.value;
        return role === 'business_owner' || role === 'super_admin';
    });

    const isSuperAdmin = computed(() => userRole.value === 'super_admin');

    // Business roles (for business dashboard) - única fuente de verdad
    const isBusinessUser = computed(() => {
        const businessRoles = ['super_admin', 'business_owner'];
        return businessRoles.includes(userRole.value);
    });

    // User role priority (for determining primary role)
    const primaryRole = computed(() => {
        if (isSuperAdmin.value) return 'super_admin';
        if (isBusinessOwner.value) return 'business_owner';
        if (isCustomer.value) return 'customer';
        return 'guest';
    });

    // User permissions
    const permissions = computed(() => {
        const perms = new Set();

        // Super admin has all permissions
        if (isSuperAdmin.value) {
            perms.add('admin.access');
            perms.add('users.manage');
            perms.add('stores.manage');
            perms.add('analytics.view');
            perms.add('system.settings');
        }

        // Business owner permissions
        if (isBusinessOwner.value) {
            perms.add('business.manage');
            perms.add('products.manage');
            perms.add('orders.manage');
            perms.add('analytics.view');
            perms.add('employees.manage');
            perms.add('settings.manage');
        }

        // Customer permissions
        if (isCustomer.value) {
            perms.add('shop.access');
            perms.add('orders.create');
            perms.add('wishlist.manage');
            perms.add('profile.manage');
        }

        return perms;
    });

    // Helper functions
    const hasPermission = (permission) => {
        return permissions.value.has(permission);
    };

    const hasAnyPermission = (permissionList) => {
        return permissionList.some(permission => permissions.value.has(permission));
    };

    const hasAllPermissions = (permissionList) => {
        return permissionList.every(permission => permissions.value.has(permission));
    };

    // User display info
    const displayName = computed(() => {
        const name = user.value?.name;
        const email = user.value?.email;
        return name || email || 'Guest';
    });

    const userAvatar = computed(() => {
        if (!user.value) return '/images/default-avatar.png';
        return user.value?.avatar || '/images/default-avatar.png';
    });

    const userInitials = computed(() => {
        if (!user.value) return '?';
        const name = user.value?.name || '';
        if (!name) return '?';

        const parts = name.trim().split(' ');
        if (parts.length >= 2) {
            return (parts[0][0] + parts[parts.length - 1][0]).toUpperCase();
        }
        return name.substring(0, 2).toUpperCase();
    });

    return {
        // User data
        user,
        currentStore,
        displayName,
        userAvatar,
        userInitials,

        // Roles
        isCustomer,
        isBusinessOwner,
        isSuperAdmin,
        isBusinessUser,
        primaryRole,

        // Permissions
        permissions,
        hasPermission,
        hasAnyPermission,
        hasAllPermissions,
    };
}
