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
        // First, let's create a backup of existing tenants data
        if (Schema::hasTable('tenants')) {
            // Migrate existing tenants to stores
            $tenants = DB::table('tenants')->get();
            
            foreach ($tenants as $tenant) {
                // Create store from tenant
                $storeId = DB::table('stores')->insertGetId([
                    'uuid' => $tenant->uuid,
                    'name' => $tenant->name,
                    'slug' => $tenant->slug,
                    'description' => null,
                    'logo' => null,
                    'banner' => null,
                    'business_type' => 'retail',
                    'status' => $tenant->is_active ? 'active' : 'inactive',
                    'tax_id' => null,
                    'verified_business' => false,
                    'website_url' => $tenant->custom_domain,
                    'timezone' => 'America/Havana',
                    'owner_id' => $tenant->owner_id,
                    'created_at' => $tenant->created_at,
                    'updated_at' => $tenant->updated_at,
                    'deleted_at' => null,
                ]);

                // Create currency config for the store
                DB::table('store_currency_configs')->insert([
                    'id' => $tenant->uuid, // Reuse the same UUID for consistency
                    'store_id' => $storeId,
                    'default_currency' => 'USD',
                    'display_currencies' => json_encode(['USD', 'EUR', 'CUP']),
                    'use_custom_rates' => false,
                    'auto_update_rates' => false, // Manual for CUP
                    'rate_update_frequency' => 'weekly',
                    'last_rate_update' => null,
                    'historical_retention_years' => 2,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                // Migrate tenant users to store users
                if (Schema::hasTable('tenant_users')) {
                    $tenantUsers = DB::table('tenant_users')->where('tenant_id', $tenant->id)->get();
                    
                    foreach ($tenantUsers as $tenantUser) {
                        DB::table('store_users')->insert([
                            'store_id' => $storeId,
                            'user_id' => $tenantUser->user_id,
                            'role' => $tenantUser->role,
                            'permissions' => null,
                            'is_active' => true,
                            'joined_at' => $tenantUser->created_at,
                            'created_at' => $tenantUser->created_at,
                            'updated_at' => $tenantUser->updated_at,
                        ]);
                    }
                }

                // Update products to reference the new store instead of tenant
                if (Schema::hasTable('products')) {
                    DB::table('products')
                        ->where('tenant_id', $tenant->id)
                        ->update(['tenant_id' => $storeId]); // We'll update the column name in a later migration
                }

                // Update other tables that reference tenant_id
                $tablesToUpdate = [
                    'business_currency_configs',
                    'currency_rates_business',
                    'product_categories',
                    'product_tags',
                    'product_attributes'
                ];

                foreach ($tablesToUpdate as $table) {
                    if (Schema::hasTable($table)) {
                        DB::table($table)
                            ->where('tenant_id', $tenant->id)
                            ->update(['tenant_id' => $storeId]);
                    }
                }

                // Update domains table if it exists
                if (Schema::hasTable('domains')) {
                    DB::table('domains')
                        ->where('tenant_id', $tenant->id)
                        ->update(['tenant_id' => $storeId]);
                }
            }

            // Now handle the foreign keys before dropping tenant tables
            $this->dropForeignKeysSafely();
            
            // Drop the old tenant tables
            if (Schema::hasTable('tenant_users')) {
                Schema::dropIfExists('tenant_users');
            }
            if (Schema::hasTable('tenants')) {
                Schema::dropIfExists('tenants');
            }
        }
    }

    /**
     * Safely drop foreign keys that reference tenants
     */
    private function dropForeignKeysSafely(): void
    {
        $tablesToHandle = [
            'domains',
            'business_currency_configs',
            'currency_rates_business',
            'product_categories',
            'product_tags',
            'product_attributes',
            'products'
        ];

        foreach ($tablesToHandle as $tableName) {
            if (Schema::hasTable($tableName)) {
                try {
                    Schema::table($tableName, function (Blueprint $table) {
                        // Drop foreign key if it exists
                        $table->dropForeign(['tenant_id']);
                    });
                } catch (\Exception $e) {
                    // Foreign key might not exist, continue
                    continue;
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Recreate tenants table
        Schema::create('tenants', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('custom_domain')->nullable();
            $table->boolean('is_active')->default(true);
            $table->json('branding_config')->nullable();
            $table->json('data')->nullable();
            $table->foreignId('owner_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });

        // Recreate tenant_users table
        Schema::create('tenant_users', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('role')->default('customer');
            $table->timestamps();

            $table->unique(['tenant_id', 'user_id']);
        });

        // Migrate data back from stores to tenants
        if (Schema::hasTable('stores')) {
            $stores = DB::table('stores')->get();
            
            foreach ($stores as $store) {
                // Create tenant from store
                $tenantId = DB::table('tenants')->insertGetId([
                    'uuid' => $store->uuid,
                    'name' => $store->name,
                    'slug' => $store->slug,
                    'custom_domain' => $store->website_url,
                    'is_active' => $store->status === 'active',
                    'branding_config' => null,
                    'data' => null,
                    'owner_id' => $store->owner_id,
                    'created_at' => $store->created_at,
                    'updated_at' => $store->updated_at,
                ]);

                // Migrate store users back to tenant users
                if (Schema::hasTable('store_users')) {
                    $storeUsers = DB::table('store_users')->where('store_id', $store->id)->get();
                    
                    foreach ($storeUsers as $storeUser) {
                        DB::table('tenant_users')->insert([
                            'tenant_id' => $tenantId,
                            'user_id' => $storeUser->user_id,
                            'role' => $storeUser->role,
                            'created_at' => $storeUser->created_at,
                            'updated_at' => $storeUser->updated_at,
                        ]);
                    }
                }

                // Update products back to reference tenant_id
                if (Schema::hasTable('products')) {
                    DB::table('products')
                        ->where('tenant_id', $store->id)
                        ->update(['tenant_id' => $tenantId]);
                }

                // Update other tables back
                $tablesToRevert = [
                    'business_currency_configs',
                    'currency_rates_business',
                    'product_categories',
                    'product_tags',
                    'product_attributes'
                ];

                foreach ($tablesToRevert as $tableName) {
                    if (Schema::hasTable($tableName)) {
                        DB::table($tableName)
                            ->where('tenant_id', $store->id)
                            ->update(['tenant_id' => $tenantId]);
                    }
                }

                // Update domains back
                if (Schema::hasTable('domains')) {
                    DB::table('domains')
                        ->where('tenant_id', $store->id)
                        ->update(['tenant_id' => $tenantId]);
                }
            }
        }
    }
};
