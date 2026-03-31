<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Creates the coupons and discount system tables.
     */
    public function up(): void
    {
        // Coupons table
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->unsignedBigInteger('store_id');
            
            // Coupon identification
            $table->string('code')->unique();
            $table->string('name');
            $table->text('description')->nullable();
            
            // Discount configuration
            $table->string('type', 20); // percentage, fixed_amount, free_shipping, buy_x_get_y
            $table->decimal('discount_value', 10, 2); // Percentage or fixed amount
            $table->string('currency', 3)->nullable(); // For fixed_amount type
            
            // Usage limits
            $table->integer('usage_limit')->nullable(); // Total usage limit
            $table->integer('usage_limit_per_user')->nullable();
            $table->integer('usage_count')->default(0);
            $table->decimal('minimum_purchase_amount', 10, 2)->nullable();
            $table->decimal('maximum_discount_amount', 10, 2)->nullable();
            
            // Validity period
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            
            // Applicability
            $table->json('applicable_products')->nullable(); // Array of product IDs
            $table->json('applicable_categories')->nullable(); // Array of category IDs
            $table->json('excluded_products')->nullable();
            $table->json('excluded_categories')->nullable();
            $table->boolean('apply_to_sale_items')->default(true);
            
            // Buy X Get Y configuration (for buy_x_get_y type)
            $table->integer('buy_quantity')->nullable();
            $table->integer('get_quantity')->nullable();
            $table->decimal('get_discount_percentage', 5, 2)->nullable();
            
            // Customer restrictions
            $table->json('allowed_user_ids')->nullable(); // Specific users
            $table->json('allowed_customer_groups')->nullable(); // Customer groups
            $table->boolean('first_order_only')->default(false);
            
            // Status and visibility
            $table->string('status', 20)->default('active'); // active, inactive, expired, depleted
            $table->boolean('is_public')->default(true); // Visible in store
            $table->boolean('combinable_with_other_coupons')->default(false);
            
            // Metadata
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();
            
            $table->foreign('store_id')->references('id')->on('stores')->onDelete('cascade');
            $table->index(['store_id', 'status'], 'coupons_store_status');
            $table->index(['code', 'status'], 'coupons_code_status');
            $table->index(['store_id', 'expires_at'], 'coupons_store_expires');
            $table->index('uuid');
        });

        // Coupon usage history
        Schema::create('coupon_usage', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('coupon_id');
            $table->unsignedBigInteger('order_id')->nullable();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->unsignedBigInteger('store_id');
            
            // Usage details
            $table->decimal('discount_amount', 10, 2);
            $table->string('currency', 3);
            $table->decimal('order_total', 10, 2);
            
            // Metadata
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamp('used_at');
            $table->timestamps();
            
            $table->foreign('coupon_id')->references('id')->on('coupons')->onDelete('cascade');
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('set null');
            $table->foreign('store_id')->references('id')->on('stores')->onDelete('cascade');
            $table->index(['coupon_id', 'used_at'], 'usage_coupon_date');
            $table->index(['user_id', 'coupon_id'], 'usage_user_coupon');
            $table->index(['store_id', 'used_at'], 'usage_store_date');
        });

        // Discount rules (automatic discounts without codes)
        Schema::create('discount_rules', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->unsignedBigInteger('store_id');
            
            $table->string('name');
            $table->text('description')->nullable();
            $table->integer('priority')->default(0); // Higher priority applies first
            
            // Discount configuration
            $table->string('type', 20); // percentage, fixed_amount, free_shipping, buy_x_get_y
            $table->decimal('discount_value', 10, 2);
            $table->string('currency', 3)->nullable();
            
            // Conditions
            $table->decimal('minimum_purchase_amount', 10, 2)->nullable();
            $table->integer('minimum_items_quantity')->nullable();
            $table->json('applicable_products')->nullable();
            $table->json('applicable_categories')->nullable();
            $table->json('excluded_products')->nullable();
            $table->json('excluded_categories')->nullable();
            
            // Customer conditions
            $table->json('allowed_customer_groups')->nullable();
            $table->boolean('first_order_only')->default(false);
            $table->boolean('apply_to_sale_items')->default(true);
            
            // Validity
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            
            // Status
            $table->string('status', 20)->default('active');
            $table->boolean('stop_further_rules')->default(false); // Stop applying other rules
            
            // Metadata
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();
            
            $table->foreign('store_id')->references('id')->on('stores')->onDelete('cascade');
            $table->index(['store_id', 'status', 'priority'], 'rules_store_status_priority');
            $table->index('uuid');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('discount_rules');
        Schema::dropIfExists('coupon_usage');
        Schema::dropIfExists('coupons');
    }
};
