<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Disable transactions for this migration.
     */
    public bool $withinTransaction = false;

    /**
     * Run the migrations.
     * This migration clears any aborted transaction state in PostgreSQL.
     */
    public function up(): void
    {
        // Attempt to rollback any aborted transaction
        try {
            DB::statement('ROLLBACK');
        } catch (\Exception $e) {
            // Transaction might not be active, ignore
        }

        // For PostgreSQL, we might need to also reset the connection
        $driver = DB::getDriverName();
        if ($driver === 'pgsql') {
            try {
                // End any existing transaction and start fresh
                DB::statement('END');
            } catch (\Exception $e) {
                // Ignore if no transaction
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Nothing to reverse
    }
};
