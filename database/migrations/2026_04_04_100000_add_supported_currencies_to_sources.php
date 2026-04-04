<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('currency_sources', function (Blueprint $table) {
            $table->json('supported_currencies')->nullable()->after('config')
                ->comment('Array de monedas soportadas por esta fuente (ej: ["CUP"], ["USD","EUR"], etc). Null = todas');
        });
    }

    public function down(): void
    {
        Schema::table('currency_sources', function (Blueprint $table) {
            $table->dropColumn('supported_currencies');
        });
    }
};
