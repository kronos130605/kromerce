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
        // First, drop any foreign key constraints on tenant_id
        $this->dropTenantIdForeignKeyIfExists();
        
        // Then drop the tenant_id column
        Schema::table('domains', function (Blueprint $table) {
            if (Schema::hasColumn('domains', 'tenant_id')) {
                $table->dropColumn('tenant_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('domains', function (Blueprint $table) {
            $table->foreignId('tenant_id')->nullable()->after('id');
            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
        });
    }

    /**
     * Drop foreign key on tenant_id if it exists
     */
    private function dropTenantIdForeignKeyIfExists(): void
    {
        try {
            // Try to get the foreign key name
            $foreignKey = DB::selectOne("
                SELECT CONSTRAINT_NAME 
                FROM information_schema.KEY_COLUMN_USAGE 
                WHERE TABLE_SCHEMA = DATABASE() 
                AND TABLE_NAME = 'domains' 
                AND COLUMN_NAME = 'tenant_id'
                AND REFERENCED_TABLE_NAME IS NOT NULL
            ");

            if ($foreignKey && isset($foreignKey->CONSTRAINT_NAME)) {
                Schema::table('domains', function (Blueprint $table) use ($foreignKey) {
                    $table->dropForeign([$foreignKey->CONSTRAINT_NAME]);
                });
            }
        } catch (\Exception $e) {
            // If there's any error, continue
        }
    }
};
