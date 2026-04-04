<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Drop the currency_rates_business table as all rates are now
     * consolidated into currency_rates_global with source filtering.
     */
    public function up(): void
    {
        Schema::dropIfExists('currency_rates_business');
    }

    /**
     * Reverse the migrations.
     *
     * Recreate the table if needed (for rollback purposes).
     */
    public function down(): void
    {
        if (!Schema::hasTable('currency_rates_business')) {
            Schema::create('currency_rates_business', function (Blueprint $table) {
                $table->char('id', 36)->primary();
                $table->foreignUuid('store_id')
                    ->constrained('stores')
                    ->cascadeOnDelete();
                $table->char('from_currency', 3);
                $table->char('to_currency', 3);
                $table->decimal('rate', 15, 6);
                $table->date('effective_date');
                $table->string('source', 50)->nullable();
                $table->timestamps();

                $table->unique(['store_id', 'from_currency', 'to_currency', 'effective_date'], 
                    'unique_business_rate_per_date');
                $table->index(['store_id', 'effective_date']);
                $table->index(['from_currency', 'to_currency']);
            });
        }
    }
};
