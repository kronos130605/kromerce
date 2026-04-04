<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('business_currency_configs');
    }

    public function down(): void
    {
        Schema::create('business_currency_configs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->unsignedBigInteger('store_id');
            $table->string('default_currency', 3);
            $table->json('display_currencies');
            $table->boolean('use_custom_rates')->default(false);
            $table->boolean('auto_update_rates')->default(true);
            $table->string('rate_update_frequency', 20)->default('daily');
            $table->date('last_rate_update')->nullable();
            $table->integer('historical_retention_years')->default(2);
            $table->uuid('preferred_cuba_source_id')->nullable();
            $table->uuid('preferred_foreign_source_id')->nullable();
            $table->json('dashboard_pairs')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('store_id')->references('id')->on('stores')->onDelete('cascade');
            $table->unique('store_id');
        });
    }
};
