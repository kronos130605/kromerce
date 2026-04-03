<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('business_currency_configs', function (Blueprint $table) {
            $table->foreignUuid('preferred_cuba_source_id')
                ->nullable()
                ->references('id')
                ->on('currency_sources')
                ->nullOnDelete()
                ->after('source_id');
        });
    }

    public function down(): void
    {
        Schema::table('business_currency_configs', function (Blueprint $table) {
            $table->dropColumn('preferred_cuba_source_id');
        });
    }
};
