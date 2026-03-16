import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';

export function useAuth() {
    const page = usePage();
    const user = computed(() => page.props.auth?.user);
    const currentStore = computed(() => page.props.current_store);

    const roleNames = computed(() => {
        const roles = user.value?.roles;
        if (!roles || !Array.isArray(roles)) {
            return [];
        }

        return roles
            .map((r) => (typeof r === 'string' ? r : r?.name))
            .filter(Boolean);
    });

    const hasRole = (role) => roleNames.value.includes(role);
    const hasAnyRole = (roles) => roles.some((r) => hasRole(r));

    const isSuperAdmin = computed(() => hasRole('super_admin'));
    const isBusinessOwner = computed(() => hasAnyRole(['super_admin', 'business_owner']));
    const isBusinessUser = computed(() => hasAnyRole(['super_admin', 'business_owner', 'admin', 'manager']));
    const isCustomer = computed(() => {
        if (!user.value) return false;
        return !isBusinessUser.value;
    });

    // User role priority (for determining primary role)
    const primaryRole = computed(() => {
        if (!user.value) return 'guest';
        if (isSuperAdmin.value) return 'super_admin';
        if (hasRole('business_owner')) return 'business_owner';
        if (hasRole('admin')) return 'admin';
        if (hasRole('manager')) return 'manager';
        if (hasRole('employee')) return 'employee';
        return 'customer';
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
        roleNames,
        hasRole,
        hasAnyRole,
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
