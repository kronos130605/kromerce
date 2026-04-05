<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Fix unique indexes to respect soft deletes.
     */
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Drop existing unique indexes
            $table->dropUnique('p_store_slug_unique');
            $table->dropUnique('p_store_sku_unique');
            
            // Create new unique indexes including deleted_at
            // This allows same slug/sku for soft-deleted products
            $table->unique(['store_id', 'slug', 'deleted_at'], 'p_store_slug_deleted_unique');
            $table->unique(['store_id', 'sku', 'deleted_at'], 'p_store_sku_deleted_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropUnique('p_store_slug_deleted_unique');
            $table->dropUnique('p_store_sku_deleted_unique');
            
            $table->unique(['store_id', 'slug'], 'p_store_slug_unique');
            $table->unique(['store_id', 'sku'], 'p_store_sku_unique');
        });
    }
};
