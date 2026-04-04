<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('currency_rates_global', function (Blueprint $table) {
            // Eliminar índice único existente
            $table->dropUnique('cr_global_unique');
            
            // Crear nuevo índice único incluyendo source
            $table->unique(['from_currency', 'to_currency', 'effective_date', 'source'], 'cr_global_unique');
        });
    }

    public function down(): void
    {
        Schema::table('currency_rates_global', function (Blueprint $table) {
            // Eliminar nuevo índice
            $table->dropUnique('cr_global_unique');
            
            // Restaurar índice original
            $table->unique(['from_currency', 'to_currency', 'effective_date'], 'cr_global_unique');
        });
    }
};
