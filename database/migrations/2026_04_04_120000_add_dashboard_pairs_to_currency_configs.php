<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('business_currency_configs', function (Blueprint $table) {
            $table->json('dashboard_pairs')->nullable()->after('display_currencies');
        });
    }

    public function down(): void
    {
        Schema::table('business_currency_configs', function (Blueprint $table) {
            $table->dropColumn('dashboard_pairs');
        });
    }
};
