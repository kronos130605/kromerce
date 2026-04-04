<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add missing fields to store_currency_configs (consolidating from business_currency_configs)
        Schema::table('store_currency_configs', function (Blueprint $table) {
            $table->uuid('preferred_cuba_source_id')->nullable()->after('historical_retention_years');
            $table->uuid('preferred_foreign_source_id')->nullable()->after('preferred_cuba_source_id');
            $table->json('dashboard_pairs')->nullable()->after('preferred_foreign_source_id');
            $table->softDeletes();

            $table->foreign('preferred_cuba_source_id')
                ->references('id')->on('currency_sources')->onDelete('set null');
            $table->foreign('preferred_foreign_source_id')
                ->references('id')->on('currency_sources')->onDelete('set null');
        });

        // Copy existing data from business_currency_configs into store_currency_configs
        if (Schema::hasTable('business_currency_configs')) {
            \DB::statement("
                UPDATE store_currency_configs scc
                INNER JOIN business_currency_configs bcc ON scc.store_id = bcc.store_id
                SET
                    scc.preferred_cuba_source_id    = bcc.preferred_cuba_source_id,
                    scc.preferred_foreign_source_id = bcc.preferred_foreign_source_id,
                    scc.dashboard_pairs             = bcc.dashboard_pairs,
                    scc.default_currency            = bcc.default_currency,
                    scc.display_currencies          = bcc.display_currencies,
                    scc.use_custom_rates            = bcc.use_custom_rates,
                    scc.auto_update_rates           = bcc.auto_update_rates,
                    scc.rate_update_frequency       = bcc.rate_update_frequency,
                    scc.last_rate_update            = bcc.last_rate_update,
                    scc.historical_retention_years  = bcc.historical_retention_years
                WHERE bcc.deleted_at IS NULL
            ");
        }
    }

    public function down(): void
    {
        Schema::table('store_currency_configs', function (Blueprint $table) {
            $table->dropForeign(['preferred_cuba_source_id']);
            $table->dropForeign(['preferred_foreign_source_id']);
            $table->dropColumn(['preferred_cuba_source_id', 'preferred_foreign_source_id', 'dashboard_pairs', 'deleted_at']);
        });
    }
};
