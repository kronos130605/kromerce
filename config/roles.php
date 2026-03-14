<?php

return [
    /*
    |--------------------------------------------------------------------------
    | System Roles Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains all role definitions and their configurations.
    | Centralizing roles makes it easier to maintain and modify role logic
    | across the application without duplicating role arrays in multiple places.
    |
    */

    /**
     * All available roles in the system
     * These should match the roles defined in RolePermissionSeeder
     */
    'available_roles' => [
        'super_admin' => 'Super Administrator',
        'business_owner' => 'Business Owner',
        'admin' => 'Administrator',
        'manager' => 'Manager',
        'employee' => 'Employee',
        'customer' => 'Customer',
    ],

    /**
     * Business roles that can access business dashboard and create stores
     * These roles are considered "business users" with higher privileges
     */
    'business_roles' => [
        'super_admin',
        'business_owner',
        'admin',
        'manager',
    ],

    /**
     * Business roles for store management (more granular)
     * Used for store-specific operations and permissions
     */
    'store_management_roles' => [
        'super_admin',
        'business_owner',
        'admin',
        'manager',
    ],

    /**
     * Role priority for determining primary role
     * Lower numbers = higher priority
     * Used when user has multiple roles
     */
    'role_priority' => [
        'super_admin' => 1,
        'business_owner' => 2,
        'admin' => 3,
        'manager' => 4,
        'employee' => 5,
        'customer' => 6,
    ],

    /**
     * Default store slug mapping based on user roles
     * Determines which default store to create for each role type
     */
    'default_store_slugs' => [
        'business' => 'business-default',    // For all business_roles
        'customer' => 'customers-default',  // For customers only
    ],

    /**
     * Dashboard view mapping based on user roles
     * Determines which dashboard view to show for each role type
     */
    'dashboard_views' => [
        'business' => 'Business/Index',    // For all business_roles
        'customer' => 'Customer/Index',    // For customers only
    ],

    /**
     * Registration form user types
     * Maps to the actual roles in the system
     */
    'registration_types' => [
        'customer' => 'customer',
        'business_owner' => 'business_owner',
    ],

    /**
     * Role display names for UI
     * Human-readable names for displaying in the interface
     */
    'display_names' => [
        'super_admin' => 'Super Administrator',
        'business_owner' => 'Business Owner',
        'admin' => 'Administrator',
        'manager' => 'Manager',
        'employee' => 'Employee',
        'customer' => 'Customer',
    ],

    /**
     * Role descriptions for help text or tooltips
     */
    'descriptions' => [
        'super_admin' => 'Has full system access and can manage all users and stores',
        'business_owner' => 'Can create and manage their own stores and products',
        'admin' => 'Can manage store operations and employees',
        'manager' => 'Can manage daily store operations and inventory',
        'employee' => 'Can handle sales and customer service',
        'customer' => 'Can browse products and place orders',
    ],
];
