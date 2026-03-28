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
        // Check if column exists before trying to drop
        $columnExists = DB::selectOne("
            SELECT column_name 
            FROM information_schema.columns 
            WHERE table_name = 'domains' 
            AND column_name = 'tenant_id'
            AND table_schema = current_schema()
        ");
        
        if ($columnExists) {
            // Drop any foreign key first
            $this->dropTenantIdForeignKeyIfExists();
            
            // Then drop the column
            DB::statement('ALTER TABLE domains DROP COLUMN IF EXISTS tenant_id');
        }
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
                AND TABLE_NAME = 'domains' 
                AND COLUMN_NAME = 'tenant_id'
                AND REFERENCED_TABLE_NAME IS NOT NULL
            ", [$databaseName]);

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
