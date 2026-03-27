<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Orders table - Main order information
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->unsignedBigInteger('store_id');
            $table->unsignedBigInteger('customer_id');
            $table->string('order_number')->unique();
            
            // Order status and dates
            $table->string('status', 20)->default('pending'); // pending, processing, shipped, delivered, cancelled, refunded
            $table->string('payment_status', 20)->default('pending'); // pending, paid, failed, refunded
            $table->string('fulfillment_status', 20)->default('pending'); // pending, processing, shipped, delivered
            
            // Financial information
            $table->string('currency', 3)->default('USD');
            $table->decimal('subtotal', 10, 2);
            $table->decimal('tax_amount', 10, 2)->default(0);
            $table->decimal('shipping_amount', 10, 2)->default(0);
            $table->decimal('discount_amount', 10, 2)->default(0);
            $table->decimal('total_amount', 10, 2);
            
            // Payment and shipping
            $table->string('payment_method')->nullable(); // stripe, paypal, cash_on_delivery, etc.
            $table->string('payment_method_title')->nullable();
            $table->string('transaction_id')->nullable();
            $table->string('shipping_method')->nullable(); // pickup, standard, express
            $table->string('shipping_method_title')->nullable();
            
            // Addresses (JSON for flexibility)
            $table->json('shipping_address')->nullable();
            $table->json('billing_address')->nullable();
            
            // Additional information
            $table->text('notes')->nullable();
            $table->text('customer_notes')->nullable();
            $table->json('metadata')->nullable(); // Additional order metadata
            
            // Timestamps
            $table->timestamp('shipped_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            // Foreign keys
            $table->foreign('store_id')->references('id')->on('stores')->onDelete('cascade');
            $table->foreign('customer_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            
            // Indexes
            $table->index(['store_id', 'status']);
            $table->index(['store_id', 'created_at']);
            $table->index(['customer_id', 'status']);
            $table->index(['payment_status']);
            $table->index(['order_number']);
        });

        // Order Items table - Individual products in orders
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id');
            $table->uuid('product_id')->nullable();
            $table->unsignedBigInteger('product_variant_id')->nullable();
            
            // Product information at time of purchase
            $table->string('product_name');
            $table->string('product_sku')->nullable();
            $table->string('product_image')->nullable();
            
            // Pricing and quantity
            $table->integer('quantity');
            $table->decimal('unit_price', 10, 2);
            $table->decimal('total_price', 10, 2);
            $table->decimal('tax_amount', 10, 2)->default(0);
            $table->decimal('discount_amount', 10, 2)->default(0);
            
            // Product snapshot (JSON) - Store product data at time of purchase
            $table->json('product_snapshot')->nullable();
            
            $table->timestamps();
            
            // Foreign keys
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('set null');
            $table->foreign('product_variant_id')->references('id')->on('product_variants')->onDelete('set null');
            
            // Indexes
            $table->index(['order_id']);
            $table->index(['product_id']);
        });

        // Order Status History table - Track status changes
        Schema::create('order_status_history', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id');
            $table->string('status', 20); // pending, processing, shipped, delivered, cancelled, refunded
            $table->string('payment_status', 20)->nullable();
            $table->string('fulfillment_status', 20)->nullable();
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('notified_by')->nullable(); // User who made the change
            $table->boolean('customer_notified')->default(false);
            $table->timestamps();
            
            // Foreign keys
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
            $table->foreign('notified_by')->references('id')->on('users')->onDelete('set null');
            
            // Indexes
            $table->index(['order_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_status_history');
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
    }
};
