<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Creates product relationship tables (related, cross-sells, up-sells, bundles).
     */
    public function up(): void
    {
        // Related products (you may also like)
        Schema::create('product_related', function (Blueprint $table) {
            $table->id();
            $table->uuid('product_id');
            $table->uuid('related_product_id');
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('related_product_id')->references('id')->on('products')->onDelete('cascade');
            $table->unique(['product_id', 'related_product_id'], 'pr_product_related_unique');
            $table->index(['product_id', 'sort_order'], 'pr_product_order');
        });

        // Cross-sell products (frequently bought together)
        Schema::create('product_cross_sells', function (Blueprint $table) {
            $table->id();
            $table->uuid('product_id');
            $table->uuid('cross_sell_product_id');
            $table->integer('sort_order')->default(0);
            $table->decimal('discount_percentage', 5, 2)->nullable(); // Optional bundle discount
            $table->timestamps();
            
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('cross_sell_product_id')->references('id')->on('products')->onDelete('cascade');
            $table->unique(['product_id', 'cross_sell_product_id'], 'pcs_product_cross_sell_unique');
            $table->index(['product_id', 'sort_order'], 'pcs_product_order');
        });

        // Up-sell products (premium alternatives)
        Schema::create('product_up_sells', function (Blueprint $table) {
            $table->id();
            $table->uuid('product_id');
            $table->uuid('up_sell_product_id');
            $table->integer('sort_order')->default(0);
            $table->text('reason')->nullable(); // Why this is better
            $table->timestamps();
            
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('up_sell_product_id')->references('id')->on('products')->onDelete('cascade');
            $table->unique(['product_id', 'up_sell_product_id'], 'pus_product_up_sell_unique');
            $table->index(['product_id', 'sort_order'], 'pus_product_order');
        });

        // Product bundles (package deals)
        Schema::create('product_bundles', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->unsignedBigInteger('store_id');
            
            $table->string('name');
            $table->string('slug');
            $table->text('description')->nullable();
            $table->string('image')->nullable();
            
            // Pricing
            $table->string('pricing_type', 20)->default('fixed'); // fixed, percentage_discount, dynamic
            $table->string('currency', 3);
            $table->decimal('bundle_price', 10, 2)->nullable(); // For fixed pricing
            $table->decimal('discount_percentage', 5, 2)->nullable(); // For percentage discount
            
            // Configuration
            $table->boolean('allow_customization')->default(false); // Can customer change items?
            $table->integer('min_items')->nullable();
            $table->integer('max_items')->nullable();
            
            // Status
            $table->string('status', 20)->default('active');
            $table->boolean('featured')->default(false);
            
            // Availability
            $table->timestamp('available_from')->nullable();
            $table->timestamp('available_to')->nullable();
            
            // Stock
            $table->boolean('manage_stock')->default(true);
            $table->integer('stock_quantity')->default(0);
            
            // SEO
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            
            // Metadata
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();
            
            $table->foreign('store_id')->references('id')->on('stores')->onDelete('cascade');
            $table->unique(['store_id', 'slug'], 'pb_store_slug_unique');
            $table->index(['store_id', 'status'], 'pb_store_status');
        });

        // Bundle items
        Schema::create('product_bundle_items', function (Blueprint $table) {
            $table->id();
            $table->uuid('bundle_id');
            $table->uuid('product_id');
            $table->unsignedBigInteger('product_variant_id')->nullable();
            
            $table->integer('quantity')->default(1);
            $table->boolean('is_required')->default(true); // Required in bundle
            $table->boolean('is_default')->default(false); // Default selection if customizable
            $table->integer('sort_order')->default(0);
            
            // Optional individual pricing override
            $table->decimal('price_override', 10, 2)->nullable();
            
            $table->timestamps();
            
            $table->foreign('bundle_id')->references('id')->on('product_bundles')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('product_variant_id')->references('id')->on('product_variants')->onDelete('cascade');
            $table->index(['bundle_id', 'sort_order'], 'pbi_bundle_order');
        });

        // Bundle purchase history (for analytics)
        Schema::create('bundle_purchases', function (Blueprint $table) {
            $table->id();
            $table->uuid('bundle_id');
            $table->uuid('order_id');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            $table->decimal('bundle_price', 10, 2);
            $table->decimal('original_price', 10, 2); // Sum of individual prices
            $table->decimal('savings', 10, 2);
            $table->string('currency', 3);
            
            $table->json('items_purchased'); // Snapshot of items in bundle at purchase time
            
            $table->timestamps();
            
            $table->foreign('bundle_id')->references('id')->on('product_bundles')->onDelete('cascade');
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
            $table->index(['bundle_id', 'created_at'], 'bp_bundle_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bundle_purchases');
        Schema::dropIfExists('product_bundle_items');
        Schema::dropIfExists('product_bundles');
        Schema::dropIfExists('product_up_sells');
        Schema::dropIfExists('product_cross_sells');
        Schema::dropIfExists('product_related');
    }
};
