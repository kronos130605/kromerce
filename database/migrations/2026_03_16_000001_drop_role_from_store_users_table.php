<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('store_users', function (Blueprint $table) {
            if (Schema::hasColumn('store_users', 'role')) {
                $table->dropIndex(['store_id', 'role', 'is_active']);
                $table->dropColumn('role');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('store_users', function (Blueprint $table) {
            if (!Schema::hasColumn('store_users', 'role')) {
                $table->string('role')->default('customer');
                $table->index(['store_id', 'role', 'is_active']);
            }
        });
    }
};
