<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Creates tax system tables for multi-jurisdiction tax management.
     */
    public function up(): void
    {
        // Tax classes (categories of taxable items)
        Schema::create('tax_classes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('store_id')->nullable(); // Null for global classes
            
            $table->string('name'); // 'Standard', 'Reduced', 'Zero', 'Exempt'
            $table->string('slug');
            $table->text('description')->nullable();
            $table->boolean('is_default')->default(false);
            $table->timestamps();
            
            $table->foreign('store_id')->references('id')->on('stores')->onDelete('cascade');
            $table->index(['store_id', 'slug'], 'tc_store_slug');
        });

        // Tax rates by jurisdiction
        Schema::create('tax_rates', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('store_id')->nullable(); // Null for global rates
            $table->unsignedBigInteger('tax_class_id')->nullable();
            
            // Rate identification
            $table->string('name'); // 'IVA Cuba', 'Sales Tax NY', 'VAT EU'
            $table->string('code')->nullable(); // Tax code/identifier
            $table->decimal('rate', 8, 4); // Percentage (e.g., 10.0000 for 10%)
            $table->string('type', 20)->default('percentage'); // percentage, fixed
            
            // Jurisdiction
            $table->string('country', 2); // ISO 3166-1 alpha-2
            $table->string('state')->nullable();
            $table->string('city')->nullable();
            $table->string('postal_code')->nullable();
            $table->json('postal_code_ranges')->nullable(); // Array of ranges
            
            // Application rules
            $table->boolean('compound')->default(false); // Compound tax (tax on tax)
            $table->integer('priority')->default(0); // Order of application
            $table->boolean('shipping_taxable')->default(true);
            
            // Validity
            $table->date('effective_from')->nullable();
            $table->date('effective_to')->nullable();
            $table->string('status', 20)->default('active');
            
            $table->timestamps();
            $table->softDeletes();
            
            $table->foreign('store_id')->references('id')->on('stores')->onDelete('cascade');
            $table->foreign('tax_class_id')->references('id')->on('tax_classes')->onDelete('set null');
            $table->index(['country', 'state', 'status'], 'tr_location_status');
            $table->index(['store_id', 'status'], 'tr_store_status');
        });

        // Order tax breakdown (detailed tax calculation per order)
        Schema::create('order_taxes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id');
            $table->unsignedBigInteger('tax_rate_id')->nullable();
            
            // Tax details
            $table->string('tax_name');
            $table->string('tax_code')->nullable();
            $table->decimal('tax_rate', 8, 4);
            $table->decimal('taxable_amount', 10, 2); // Amount subject to tax
            $table->decimal('tax_amount', 10, 2); // Calculated tax
            
            // Breakdown
            $table->decimal('product_tax', 10, 2)->default(0);
            $table->decimal('shipping_tax', 10, 2)->default(0);
            
            // Jurisdiction snapshot
            $table->string('country', 2);
            $table->string('state')->nullable();
            $table->string('city')->nullable();
            $table->string('postal_code')->nullable();
            
            $table->boolean('is_compound')->default(false);
            $table->integer('priority')->default(0);
            
            $table->timestamps();
            
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
            $table->foreign('tax_rate_id')->references('id')->on('tax_rates')->onDelete('set null');
            $table->index(['order_id', 'priority'], 'ot_order_priority');
        });

        // Tax exemptions (for specific customers or products)
        Schema::create('tax_exemptions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('store_id');
            
            // Exemption type
            $table->string('type', 20); // customer, product, category, order
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->uuid('product_id')->nullable();
            $table->uuid('category_id')->nullable();
            
            // Exemption details
            $table->string('exemption_code')->nullable(); // Tax exemption certificate number
            $table->text('reason');
            $table->json('exempt_tax_classes')->nullable(); // Which tax classes are exempt
            
            // Jurisdiction
            $table->string('country', 2)->nullable();
            $table->string('state')->nullable();
            
            // Validity
            $table->date('valid_from')->nullable();
            $table->date('valid_to')->nullable();
            $table->string('status', 20)->default('active');
            
            // Documentation
            $table->json('documents')->nullable(); // Uploaded exemption certificates
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('approved_at')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            $table->foreign('store_id')->references('id')->on('stores')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('category_id')->references('id')->on('product_categories')->onDelete('cascade');
            $table->index(['store_id', 'type', 'status'], 'te_store_type_status');
            $table->index(['user_id', 'status'], 'te_user_status');
        });

        // Tax reports/filings tracking
        Schema::create('tax_reports', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('store_id');
            
            $table->string('report_type', 20); // monthly, quarterly, annual
            $table->integer('year');
            $table->integer('period'); // Month (1-12) or Quarter (1-4)
            
            // Totals
            $table->decimal('total_sales', 12, 2);
            $table->decimal('taxable_sales', 12, 2);
            $table->decimal('tax_collected', 12, 2);
            $table->decimal('tax_refunded', 12, 2);
            $table->decimal('net_tax', 12, 2);
            
            // Status
            $table->string('status', 20)->default('draft'); // draft, filed, paid
            $table->date('filed_date')->nullable();
            $table->date('payment_date')->nullable();
            $table->string('filing_reference')->nullable();
            
            // Metadata
            $table->json('breakdown')->nullable(); // Detailed breakdown by jurisdiction
            $table->foreignId('generated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            
            $table->foreign('store_id')->references('id')->on('stores')->onDelete('cascade');
            $table->unique(['store_id', 'report_type', 'year', 'period'], 'tr_store_type_year_period_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tax_reports');
        Schema::dropIfExists('tax_exemptions');
        Schema::dropIfExists('order_taxes');
        Schema::dropIfExists('tax_rates');
        Schema::dropIfExists('tax_classes');
    }
};
