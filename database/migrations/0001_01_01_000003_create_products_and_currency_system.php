<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Creates products and currency system tables with store_id (not tenant_id).
     */
    public function up(): void
    {
        // Business currency configs - using store_id instead of tenant_id
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
            $table->timestamps();
            $table->softDeletes();
            
            $table->foreign('store_id')->references('id')->on('stores')->onDelete('cascade');
            $table->unique('store_id');
        });

        // Global currency rates (no store association)
        Schema::create('currency_rates_global', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('from_currency', 3);
            $table->string('to_currency', 3);
            $table->decimal('rate', 10, 6);
            $table->date('effective_date');
            $table->string('source', 50)->default('api');
            $table->timestamps();
            
            $table->unique(['from_currency', 'to_currency', 'effective_date'], 'cr_global_unique');
            $table->index(['from_currency', 'to_currency', 'effective_date'], 'cr_global_index');
            $table->index('effective_date', 'cr_global_date');
        });

        // Store-specific currency rates
        Schema::create('currency_rates_business', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->unsignedBigInteger('store_id');
            $table->string('from_currency', 3);
            $table->string('to_currency', 3);
            $table->decimal('rate', 10, 6);
            $table->date('effective_date');
            $table->string('source', 50)->default('manual');
            $table->timestamps();
            
            $table->foreign('store_id')->references('id')->on('stores')->onDelete('cascade');
            $table->unique(['store_id', 'from_currency', 'to_currency', 'effective_date'], 'cr_business_unique');
        });

        // Product categories - using store_id
        Schema::create('product_categories', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->unsignedBigInteger('store_id');
            $table->string('name');
            $table->string('slug');
            $table->text('description')->nullable();
            $table->string('image')->nullable();
            $table->uuid('parent_id')->nullable();
            $table->integer('level')->default(0);
            $table->integer('order')->default(0);
            $table->string('status', 20)->default('active');
            $table->boolean('is_featured')->default(false);
            $table->json('settings')->nullable();
            
            // SEO
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->foreign('store_id')->references('id')->on('stores')->onDelete('cascade');
            $table->unique(['store_id', 'slug'], 'pc_store_slug_unique');
            $table->index(['store_id', 'status', 'parent_id'], 'pc_store_status_parent');
            $table->index(['store_id', 'is_featured'], 'pc_store_featured');
        });

        // Products - using store_id
        Schema::create('products', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->unsignedBigInteger('store_id');
            $table->string('name');
            $table->string('slug');
            $table->text('description')->nullable();
            $table->text('short_description')->nullable();
            
            // Base prices
            $table->string('base_currency', 3);
            $table->decimal('base_price', 10, 2);
            $table->decimal('base_sale_price', 10, 2)->nullable();
            $table->decimal('cost_price', 10, 2)->nullable();
            
            // Sale configuration
            $table->boolean('is_on_sale')->default(false);
            $table->string('sale_type', 20)->nullable();
            $table->decimal('sale_discount', 5, 2)->nullable();
            $table->date('sale_start_date')->nullable();
            $table->date('sale_end_date')->nullable();
            
            // Historical cost
            $table->string('historical_cost_currency', 3)->nullable();
            $table->decimal('historical_cost_amount', 10, 2)->nullable();
            $table->decimal('historical_cost_rate', 10, 6)->nullable();
            $table->date('historical_cost_date')->nullable();
            
            // Configuration
            $table->boolean('track_cost')->default(false);
            $table->boolean('show_cost_to_customer')->default(false);
            
            // SKU and barcode
            $table->string('sku')->unique();
            $table->string('barcode')->nullable();
            
            // Status and visibility
            $table->string('status', 20)->default('active');
            $table->string('visibility', 20)->default('public');
            $table->boolean('featured')->default(false);
            $table->boolean('downloadable')->default(false);
            $table->boolean('virtual')->default(false);
            
            // Product type
            $table->string('product_type', 20)->default('simple');
            $table->boolean('manage_stock')->default(true);
            $table->integer('stock_quantity')->default(0);
            $table->string('stock_status', 20)->default('instock');
            $table->integer('low_stock_threshold')->default(0);
            
            // Shipping
            $table->decimal('weight', 8, 2)->nullable();
            $table->decimal('length', 8, 2)->nullable();
            $table->decimal('width', 8, 2)->nullable();
            $table->decimal('height', 8, 2)->nullable();
            $table->string('shipping_class')->nullable();
            $table->boolean('free_shipping')->default(false);
            
            // Taxes
            $table->string('tax_class')->nullable();
            $table->string('tax_status', 20)->default('taxable');
            
            // Stock management
            $table->integer('min_order_quantity')->default(1);
            $table->integer('max_order_quantity')->nullable();
            $table->integer('stock_alert_threshold')->nullable();
            $table->boolean('backorders_allowed')->default(false);
            
            // SEO
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->text('meta_keywords')->nullable();
            
            // Metadata
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->foreign('store_id')->references('id')->on('stores')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
            $table->unique(['store_id', 'slug'], 'p_store_slug_unique');
            $table->unique(['store_id', 'sku'], 'p_store_sku_unique');
            $table->index(['store_id', 'status'], 'p_store_status');
            $table->index(['store_id', 'stock_status'], 'p_store_stock');
            $table->index(['store_id', 'featured'], 'p_store_featured');
            $table->index(['store_id', 'product_type'], 'p_store_type');
        });

        // Product-category relationship
        Schema::create('product_category_product', function (Blueprint $table) {
            $table->uuid('product_id');
            $table->uuid('category_id');
            $table->integer('order')->default(0);
            
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('category_id')->references('id')->on('product_categories')->onDelete('cascade');
            $table->primary(['product_id', 'category_id']);
        });

        // Product tags - using store_id
        Schema::create('product_tags', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->unsignedBigInteger('store_id');
            $table->string('name');
            $table->string('slug');
            $table->text('description')->nullable();
            $table->string('color', 7)->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->foreign('store_id')->references('id')->on('stores')->onDelete('cascade');
            $table->unique(['store_id', 'slug'], 'pt_store_slug_unique');
        });

        // Product-tag relationship
        Schema::create('product_product_tag', function (Blueprint $table) {
            $table->uuid('product_id');
            $table->uuid('tag_id');
            
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('tag_id')->references('id')->on('product_tags')->onDelete('cascade');
            $table->primary(['product_id', 'tag_id']);
        });

        // Product images
        Schema::create('product_images', function (Blueprint $table) {
            $table->id();
            $table->uuid('product_id');
            $table->string('url');
            $table->string('alt')->nullable();
            $table->string('title')->nullable();
            $table->integer('order')->default(0);
            $table->boolean('is_primary')->default(false);
            $table->json('metadata')->nullable();
            $table->timestamps();
            
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->index(['product_id', 'order'], 'pi_product_order');
        });

        // Product attributes - using store_id
        Schema::create('product_attributes', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->unsignedBigInteger('store_id');
            $table->string('name');
            $table->string('slug');
            $table->string('type', 20)->default('text');
            $table->boolean('required')->default(false);
            $table->json('config')->nullable();
            $table->integer('order')->default(0);
            $table->timestamps();
            $table->softDeletes();
            
            $table->foreign('store_id')->references('id')->on('stores')->onDelete('cascade');
            $table->unique(['store_id', 'slug'], 'pa_store_slug_unique');
        });

        // Product attribute values
        Schema::create('product_attribute_values', function (Blueprint $table) {
            $table->id();
            $table->uuid('attribute_id');
            $table->string('value');
            $table->string('label');
            $table->string('color', 7)->nullable();
            $table->string('image')->nullable();
            $table->integer('order')->default(0);
            $table->timestamps();
            
            $table->foreign('attribute_id')->references('id')->on('product_attributes')->onDelete('cascade');
            $table->unique(['attribute_id', 'value']);
        });

        // Product variants
        Schema::create('product_variants', function (Blueprint $table) {
            $table->id();
            $table->uuid('product_id');
            $table->string('sku')->unique();
            $table->string('barcode')->nullable();
            
            // Prices
            $table->decimal('base_price', 10, 2)->nullable();
            $table->decimal('base_sale_price', 10, 2)->nullable();
            $table->decimal('cost_price', 10, 2)->nullable();
            
            // Inventory
            $table->boolean('manage_stock')->default(true);
            $table->integer('stock_quantity')->default(0);
            $table->string('stock_status', 20)->default('instock');
            $table->integer('low_stock_threshold')->default(0);
            
            // Shipping
            $table->decimal('weight', 8, 2)->nullable();
            $table->decimal('length', 8, 2)->nullable();
            $table->decimal('width', 8, 2)->nullable();
            $table->decimal('height', 8, 2)->nullable();
            
            // Status
            $table->string('status', 20)->default('active');
            $table->boolean('enabled')->default(true);
            
            // Metadata
            $table->json('attributes')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->unique(['product_id', 'sku'], 'pv_product_sku_unique');
            $table->index(['product_id', 'status'], 'pv_product_status');
        });

        // Variant images
        Schema::create('product_variant_images', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('variant_id');
            $table->string('url');
            $table->string('alt')->nullable();
            $table->integer('order')->default(0);
            $table->timestamps();
            
            $table->foreign('variant_id')->references('id')->on('product_variants')->onDelete('cascade');
            $table->index(['variant_id', 'order'], 'pvi_variant_order');
        });

        // Product price history
        Schema::create('product_price_history', function (Blueprint $table) {
            $table->id();
            $table->uuid('product_id');
            $table->string('currency', 3);
            $table->decimal('old_price', 10, 2);
            $table->decimal('new_price', 10, 2);
            $table->string('change_reason', 100);
            $table->unsignedBigInteger('changed_by')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('changed_by')->references('id')->on('users')->onDelete('set null');
            $table->index(['product_id', 'currency', 'created_at'], 'pph_product_currency_date');
        });

        // Currency rate updates audit
        Schema::create('currency_rate_updates', function (Blueprint $table) {
            $table->id();
            $table->date('update_date');
            $table->json('currencies_updated');
            $table->string('source', 50);
            $table->boolean('success');
            $table->text('error_message')->nullable();
            $table->integer('total_rates_updated')->default(0);
            $table->timestamps();
            
            $table->unique('update_date', 'cru_update_date_unique');
            $table->index('success', 'cru_success');
        });

        // Add self-referencing foreign key after table creation
        Schema::table('product_categories', function (Blueprint $table) {
            $table->foreign('parent_id')->references('id')->on('product_categories')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_categories', function (Blueprint $table) {
            $table->dropForeign(['parent_id']);
        });
        
        Schema::dropIfExists('currency_rate_updates');
        Schema::dropIfExists('product_price_history');
        Schema::dropIfExists('product_variant_images');
        Schema::dropIfExists('product_variants');
        Schema::dropIfExists('product_attribute_values');
        Schema::dropIfExists('product_attributes');
        Schema::dropIfExists('product_images');
        Schema::dropIfExists('product_product_tag');
        Schema::dropIfExists('product_tags');
        Schema::dropIfExists('product_category_product');
        Schema::dropIfExists('products');
        Schema::dropIfExists('product_categories');
        Schema::dropIfExists('currency_rates_business');
        Schema::dropIfExists('currency_rates_global');
        Schema::dropIfExists('business_currency_configs');
    }
};
