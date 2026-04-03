<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Creates currency_sources table for global provider catalog.
     */
    public function up(): void
    {
        Schema::create('currency_sources', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name'); // "Banco Central Venezuela", "OpenExchangeRates"
            $table->string('code', 50)->unique(); // 'bcv', 'openexchangerates', 'fixer'
            $table->enum('type', ['api', 'web'])->default('api'); // API REST o Web Scraping
            $table->string('provider_class'); // class name for factory
            $table->boolean('is_active')->default(true);
            $table->boolean('is_global_default')->default(false);
            $table->string('base_url'); // endpoint API o URL a scrapear
            $table->json('config')->nullable(); // selectors, headers, rate_limit, etc.
            $table->enum('auth_type', ['none', 'api_key', 'basic', 'bearer'])->default('none');
            $table->text('default_credentials')->nullable(); // encrypted default credentials
            $table->timestamp('last_tested_at')->nullable();
            $table->boolean('last_test_success')->nullable();
            $table->text('last_test_message')->nullable();
            $table->integer('success_count')->default(0);
            $table->integer('failure_count')->default(0);
            $table->timestamps();
            
            $table->index(['is_active', 'is_global_default'], 'cs_active_default');
            $table->index('code', 'cs_code_index');
        });

        // Add source_id to business_currency_configs
        Schema::table('business_currency_configs', function (Blueprint $table) {
            $table->uuid('source_id')->nullable()->after('historical_retention_years');
            $table->json('source_config_override')->nullable()->after('source_id');
            
            $table->foreign('source_id')->references('id')->on('currency_sources')->onDelete('set null');
            $table->index('source_id', 'bcc_source_id_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('business_currency_configs', function (Blueprint $table) {
            $table->dropForeign(['source_id']);
            $table->dropIndex('bcc_source_id_index');
            $table->dropColumn(['source_id', 'source_config_override']);
        });
        
        Schema::dropIfExists('currency_sources');
    }
};
