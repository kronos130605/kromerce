<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create permissions
        $permissions = [
            // Super admin permissions
            'manage_users',
            'manage_tenants',
            'view_analytics',
            'manage_platform_settings',
            
            // Business owner permissions
            'manage_tenant',
            'manage_products',
            'view_customers',
            'manage_orders',
            'manage_branding',
            'view_tenant_analytics',
            
            // Customer permissions
            'view_products',
            'place_orders',
            'manage_profile',
            'view_order_history',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create roles and assign permissions
        $superAdmin = Role::firstOrCreate(['name' => 'super_admin']);
        $businessOwner = Role::firstOrCreate(['name' => 'business_owner']);
        $customer = Role::firstOrCreate(['name' => 'customer']);

        // Super admin gets all permissions
        $superAdmin->givePermissionTo(Permission::all());

        // Business owner permissions
        $businessOwner->givePermissionTo([
            'manage_tenant',
            'manage_products',
            'view_customers',
            'manage_orders',
            'manage_branding',
            'view_tenant_analytics',
        ]);

        // Customer permissions
        $customer->givePermissionTo([
            'view_products',
            'place_orders',
            'manage_profile',
            'view_order_history',
        ]);
    }
}
