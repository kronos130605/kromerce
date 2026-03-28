<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Tablas que necesitan actualizar tenant_id a store_id
        $tables = [
            'business_currency_configs',
            'currency_rates_business', 
            'product_categories',
            'products',
            'product_tags',
            'product_attributes',
            'product_attribute_values',
            'product_variants',
            'product_images',
            'product_reviews',
            'shopping_carts',
            'wishlist_items',
            'orders',
            'order_items',
            'customer_addresses',
            'store_contacts',
            'store_payment_methods',
            'store_statistics',
            'inventory_logs',
            'price_history',
            'coupons',
            'coupon_usage',
            'store_settings',
            'store_analytics',
            'customer_segments',
            'email_campaigns',
            'notifications',
            'audit_logs'
        ];

        foreach ($tables as $tableName) {
            if (Schema::hasTable($tableName) && Schema::hasColumn($tableName, 'tenant_id')) {
                // Drop foreign key if exists
                $this->dropTenantIdForeignKeyIfExists($tableName);
                
                Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                    // Rename column
                    $table->renameColumn('tenant_id', 'store_id');
                });
                
                // Add new foreign key
                Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                    $table->foreign('store_id')->references('id')->on('stores')->onDelete('cascade');
                });
            }
        }
    }

    /**
     * Drop foreign key on tenant_id if it exists for specific table
     */
    private function dropTenantIdForeignKeyIfExists(string $tableName): void
    {
        $driver = DB::getDriverName();
        
        // Get current database name based on driver
        $databaseName = match($driver) {
            'pgsql' => DB::selectOne('SELECT current_database() as name')->name,
            'mysql', 'mariadb' => DB::selectOne('SELECT DATABASE() as name')->name,
            'sqlite' => 'main',
            default => null,
        };

        if (!$databaseName) {
            return;
        }

        try {
            $foreignKey = DB::selectOne("
                SELECT CONSTRAINT_NAME 
                FROM information_schema.KEY_COLUMN_USAGE 
                WHERE TABLE_SCHEMA = ?
                AND TABLE_NAME = ? 
                AND COLUMN_NAME = 'tenant_id'
                AND REFERENCED_TABLE_NAME IS NOT NULL
            ", [$databaseName, $tableName]);

            if ($foreignKey && isset($foreignKey->CONSTRAINT_NAME)) {
                Schema::table($tableName, function (Blueprint $table) use ($foreignKey) {
                    $table->dropForeign([$foreignKey->CONSTRAINT_NAME]);
                });
            }
        } catch (\Exception $e) {
            // If there's any error, continue
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tables = [
            'business_currency_configs',
            'currency_rates_business', 
            'product_categories',
            'products',
            'product_tags',
            'product_attributes',
            'product_attribute_values',
            'product_variants',
            'product_images',
            'product_reviews',
            'shopping_carts',
            'wishlist_items',
            'orders',
            'order_items',
            'customer_addresses',
            'store_contacts',
            'store_payment_methods',
            'store_statistics',
            'inventory_logs',
            'price_history',
            'coupons',
            'coupon_usage',
            'store_settings',
            'store_analytics',
            'customer_segments',
            'email_campaigns',
            'notifications',
            'audit_logs'
        ];

        foreach ($tables as $tableName) {
            if (Schema::hasTable($tableName) && Schema::hasColumn($tableName, 'store_id')) {
                // Drop foreign key if exists
                $this->dropStoreIdForeignKeyIfExists($tableName);
                
                Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                    // Rename back to tenant_id
                    $table->renameColumn('store_id', 'tenant_id');
                });
                
                // Restore original foreign key
                Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                    $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
                });
            }
        }
    }

    /**
     * Drop foreign key on store_id if it exists for specific table
     */
    private function dropStoreIdForeignKeyIfExists(string $tableName): void
    {
        try {
            // Try to get the foreign key name
            $foreignKey = DB::selectOne("
                SELECT CONSTRAINT_NAME 
                FROM information_schema.KEY_COLUMN_USAGE 
                WHERE TABLE_SCHEMA = DATABASE() 
                AND TABLE_NAME = ? 
                AND COLUMN_NAME = 'store_id'
                AND REFERENCED_TABLE_NAME IS NOT NULL
            ", [$tableName]);

            if ($foreignKey && isset($foreignKey->CONSTRAINT_NAME)) {
                Schema::table($tableName, function (Blueprint $table) use ($foreignKey) {
                    $table->dropForeign([$foreignKey->CONSTRAINT_NAME]);
                });
            }
        } catch (\Exception $e) {
            // If there's any error, continue
        }
    }
};
