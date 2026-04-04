<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('business_currency_configs', function (Blueprint $table) {
            $table->foreignUuid('preferred_foreign_source_id')
                ->nullable()
                ->after('preferred_cuba_source_id')
                ->constrained('currency_sources')
                ->nullOnDelete()
                ->comment('Fuente preferida para divisas extranjeras (NO CUP)');
        });
    }

    public function down(): void
    {
        Schema::table('business_currency_configs', function (Blueprint $table) {
            $table->dropForeign(['preferred_foreign_source_id']);
            $table->dropColumn('preferred_foreign_source_id');
        });
    }
};
