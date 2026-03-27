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
        // Check if users table has tenant_id column and migrate it
        if (Schema::hasTable('users') && Schema::hasColumn('users', 'tenant_id')) {
            // Drop foreign key if exists
            $this->dropTenantIdForeignKeyIfExists('users');
            
            // Rename tenant_id to store_id
            Schema::table('users', function (Blueprint $table) {
                $table->renameColumn('tenant_id', 'store_id');
            });
            
            // Add new foreign key if there's a stores table
            if (Schema::hasTable('stores')) {
                Schema::table('users', function (Blueprint $table) {
                    $table->foreign('store_id')->references('id')->on('stores')->onDelete('set null');
                });
            }
        }
    }

    /**
     * Drop foreign key on tenant_id if it exists for users table
     */
    private function dropTenantIdForeignKeyIfExists(string $tableName): void
    {
        try {
            // Try to get the foreign key name
            $foreignKey = DB::selectOne("
                SELECT CONSTRAINT_NAME 
                FROM information_schema.KEY_COLUMN_USAGE 
                WHERE TABLE_SCHEMA = DATABASE() 
                AND TABLE_NAME = ? 
                AND COLUMN_NAME = 'tenant_id'
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

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('users') && Schema::hasColumn('users', 'store_id')) {
            // Drop foreign key if exists
            $this->dropStoreIdForeignKeyIfExists('users');
            
            // Rename back to tenant_id
            Schema::table('users', function (Blueprint $table) {
                $table->renameColumn('store_id', 'tenant_id');
            });
            
            // Restore original foreign key if there's a tenants table
            if (Schema::hasTable('tenants')) {
                Schema::table('users', function (Blueprint $table) {
                    $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('set null');
                });
            }
        }
    }

    /**
     * Drop foreign key on store_id if it exists for users table
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
