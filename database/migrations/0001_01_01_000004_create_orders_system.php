<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Creates the orders system tables with store_id (not tenant_id).
     */
    public function up(): void
    {
        // Orders table
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->unsignedBigInteger('store_id');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // Order identification
            $table->string('order_number')->unique();
            $table->string('status', 20)->default('pending'); // pending, processing, shipped, delivered, cancelled, refunded
            $table->string('payment_status', 20)->default('pending'); // pending, paid, failed, refunded, partial
            $table->string('fulfillment_status', 20)->default('unfulfilled'); // unfulfilled, partial, fulfilled
            
            // Financial summary
            $table->string('currency', 3);
            $table->decimal('subtotal', 10, 2);
            $table->decimal('tax_amount', 10, 2)->default(0);
            $table->decimal('shipping_amount', 10, 2)->default(0);
            $table->decimal('discount_amount', 10, 2)->default(0);
            $table->decimal('total', 10, 2);
            $table->decimal('total_paid', 10, 2)->default(0);
            $table->decimal('total_refunded', 10, 2)->default(0);
            
            // Weight and items
            $table->decimal('total_weight', 8, 2)->nullable();
            $table->integer('total_items');
            $table->integer('total_unique_items');
            
            // Customer information (snapshot at order time)
            $table->string('customer_email');
            $table->string('customer_phone')->nullable();
            $table->string('customer_first_name');
            $table->string('customer_last_name');
            $table->string('customer_company')->nullable();
            
            // Billing address
            $table->string('billing_address_1');
            $table->string('billing_address_2')->nullable();
            $table->string('billing_city');
            $table->string('billing_state');
            $table->string('billing_postal_code');
            $table->string('billing_country', 2);
            $table->string('billing_phone')->nullable();
            
            // Shipping address
            $table->string('shipping_address_1');
            $table->string('shipping_address_2')->nullable();
            $table->string('shipping_city');
            $table->string('shipping_state');
            $table->string('shipping_postal_code');
            $table->string('shipping_country', 2);
            $table->string('shipping_phone')->nullable();
            $table->text('shipping_instructions')->nullable();
            
            // Shipping details
            $table->string('shipping_method')->nullable();
            $table->string('shipping_carrier')->nullable();
            $table->string('tracking_number')->nullable();
            $table->timestamp('shipped_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamp('estimated_delivery_at')->nullable();
            
            // Payment details
            $table->string('payment_method'); // stripe, paypal, cash_on_delivery, bank_transfer
            $table->string('payment_provider')->nullable();
            $table->string('payment_transaction_id')->nullable();
            $table->timestamp('paid_at')->nullable();
            
            // Discounts and coupons
            $table->string('coupon_code')->nullable();
            $table->decimal('coupon_discount', 10, 2)->default(0);
            $table->json('applied_discounts')->nullable(); // Array of applied discounts
            
            // Gift options
            $table->boolean('is_gift')->default(false);
            $table->text('gift_message')->nullable();
            $table->decimal('gift_wrap_fee', 8, 2)->default(0);
            
            // Loyalty points
            $table->integer('loyalty_points_earned')->default(0);
            $table->integer('loyalty_points_used')->default(0);
            $table->decimal('loyalty_points_discount', 10, 2)->default(0);
            
            // Metadata
            $table->text('customer_notes')->nullable();
            $table->text('internal_notes')->nullable();
            $table->string('source', 20)->default('web'); // web, mobile, api, pos
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent')->nullable();
            $table->string('referrer')->nullable();
            $table->json('metadata')->nullable(); // Additional order metadata
            
            // Timestamps
            $table->timestamp('processed_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->string('cancellation_reason')->nullable();
            $table->timestamp('refunded_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->foreign('store_id')->references('id')->on('stores')->onDelete('cascade');
            $table->index(['store_id', 'status'], 'orders_store_status');
            $table->index(['store_id', 'payment_status'], 'orders_store_payment');
            $table->index(['store_id', 'created_at'], 'orders_store_created');
            $table->index(['user_id', 'created_at'], 'orders_user_created');
            $table->index('order_number');
            $table->index('uuid');
        });

        // Order items
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->unsignedBigInteger('order_id');
            $table->uuid('product_id')->nullable();
            $table->unsignedBigInteger('product_variant_id')->nullable();
            
            // Product snapshot at order time
            $table->string('product_name');
            $table->string('product_sku');
            $table->string('product_type'); // simple, variant
            $table->text('product_description')->nullable();
            $table->string('product_image')->nullable();
            
            // Variant attributes (if applicable)
            $table->json('variant_attributes')->nullable(); // {color: 'red', size: 'M'}
            
            // Pricing
            $table->string('currency', 3);
            $table->decimal('unit_price', 10, 2);
            $table->decimal('unit_cost', 10, 2)->nullable();
            $table->integer('quantity');
            $table->decimal('subtotal', 10, 2); // unit_price * quantity
            $table->decimal('tax_amount', 10, 2)->default(0);
            $table->decimal('discount_amount', 10, 2)->default(0);
            $table->decimal('total', 10, 2);
            
            // Weight
            $table->decimal('unit_weight', 8, 2)->nullable();
            $table->decimal('total_weight', 8, 2)->nullable();
            
            // Fulfillment
            $table->string('fulfillment_status', 20)->default('pending'); // pending, picked, packed, shipped, delivered, returned
            $table->integer('quantity_fulfilled')->default(0);
            $table->integer('quantity_returned')->default(0);
            $table->timestamp('fulfilled_at')->nullable();
            
            // Metadata
            $table->json('metadata')->nullable();
            $table->timestamps();
            
            // Indexes
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('set null');
            $table->foreign('product_variant_id')->references('id')->on('product_variants')->onDelete('set null');
            $table->index(['order_id', 'fulfillment_status'], 'items_order_fulfillment');
            $table->index('uuid');
        });

        // Order status history
        Schema::create('order_status_history', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id');
            $table->string('from_status', 20)->nullable();
            $table->string('to_status', 20);
            $table->string('reason')->nullable();
            $table->foreignId('changed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->string('changed_by_type', 20)->default('user'); // user, system, api, webhook
            $table->json('metadata')->nullable();
            $table->timestamp('changed_at');
            $table->timestamps();
            
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
            $table->index(['order_id', 'changed_at'], 'status_history_order_date');
        });

        // Order payments
        Schema::create('order_payments', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->unsignedBigInteger('order_id');
            
            // Payment details
            $table->string('transaction_id')->nullable();
            $table->string('payment_method');
            $table->string('payment_provider');
            $table->decimal('amount', 10, 2);
            $table->string('currency', 3);
            $table->string('status', 20)->default('pending'); // pending, completed, failed, refunded
            
            // Provider-specific data
            $table->json('provider_data')->nullable();
            $table->text('authorization_code')->nullable();
            $table->text('receipt_url')->nullable();
            
            // Metadata
            $table->text('failure_reason')->nullable();
            $table->foreignId('processed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
            $table->index(['order_id', 'status'], 'payments_order_status');
            $table->index('uuid');
        });

        // Order refunds
        Schema::create('order_refunds', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->unsignedBigInteger('order_id');
            $table->unsignedBigInteger('order_item_id')->nullable(); // Null if full order refund
            
            // Refund details
            $table->string('refund_number')->unique();
            $table->decimal('amount', 10, 2);
            $table->string('currency', 3);
            $table->string('reason');
            $table->text('notes')->nullable();
            $table->string('status', 20)->default('pending'); // pending, processing, completed, failed
            
            // Refund method
            $table->string('refund_method'); // original_payment, store_credit, bank_transfer, other
            $table->json('refund_method_details')->nullable();
            
            // Provider data
            $table->string('transaction_id')->nullable();
            $table->json('provider_data')->nullable();
            
            // Inventory restock
            $table->boolean('restock_items')->default(false);
            $table->integer('quantity_restocked')->default(0);
            
            // Metadata
            $table->foreignId('requested_by')->constrained('users')->onDelete('cascade');
            $table->foreignId('processed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('processed_at')->nullable();
            $table->text('failure_reason')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
            $table->foreign('order_item_id')->references('id')->on('order_items')->onDelete('set null');
            $table->index(['order_id', 'status'], 'refunds_order_status');
            $table->index('uuid');
        });

        // Order shipments
        Schema::create('order_shipments', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->unsignedBigInteger('order_id');
            
            // Shipment details
            $table->string('shipment_number')->unique();
            $table->string('carrier'); // fedex, ups, usps, dhl, local
            $table->string('service')->nullable(); // ground, express, overnight
            $table->string('tracking_number');
            $table->string('tracking_url')->nullable();
            
            // Addresses
            $table->string('ship_from_name');
            $table->string('ship_from_address');
            $table->string('ship_from_city');
            $table->string('ship_from_state');
            $table->string('ship_from_postal_code');
            $table->string('ship_from_country', 2);
            
            // Weight and dimensions
            $table->decimal('weight', 8, 2);
            $table->string('weight_unit', 5)->default('kg');
            $table->decimal('length', 8, 2)->nullable();
            $table->decimal('width', 8, 2)->nullable();
            $table->decimal('height', 8, 2)->nullable();
            $table->string('dimension_unit', 5)->default('cm');
            
            // Shipping costs
            $table->decimal('shipping_cost', 10, 2)->nullable();
            $table->decimal('insurance_amount', 10, 2)->nullable();
            $table->decimal('declared_value', 10, 2)->nullable();
            
            // Status and dates
            $table->string('status', 20)->default('pending'); // pending, label_created, picked_up, in_transit, out_for_delivery, delivered, exception
            $table->timestamp('shipped_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamp('estimated_delivery_at')->nullable();
            
            // Metadata
            $table->text('notes')->nullable();
            $table->json('carrier_data')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
            $table->index(['order_id', 'status'], 'shipments_order_status');
            $table->index('tracking_number');
            $table->index('uuid');
        });

        // Shipment items (linking shipments to order items)
        Schema::create('shipment_items', function (Blueprint $table) {
            $table->unsignedBigInteger('shipment_id');
            $table->unsignedBigInteger('order_item_id');
            $table->integer('quantity');
            $table->timestamps();
            
            $table->foreign('shipment_id')->references('id')->on('order_shipments')->onDelete('cascade');
            $table->foreign('order_item_id')->references('id')->on('order_items')->onDelete('cascade');
            $table->primary(['shipment_id', 'order_item_id']);
        });

        // Order notes (internal and customer-facing)
        Schema::create('order_notes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            
            $table->text('content');
            $table->string('type', 20)->default('internal'); // internal, customer
            $table->boolean('is_important')->default(false);
            $table->boolean('is_customer_visible')->default(false);
            
            $table->timestamps();
            $table->softDeletes();
            
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
            $table->index(['order_id', 'type'], 'notes_order_type');
        });

        // Abandoned carts (for recovery)
        Schema::create('abandoned_carts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('store_id');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            
            $table->string('session_id');
            $table->string('email')->nullable();
            $table->json('items'); // Cart items snapshot
            $table->decimal('total', 10, 2);
            $table->string('currency', 3);
            
            // Recovery
            $table->boolean('recovery_email_sent')->default(false);
            $table->timestamp('recovery_email_sent_at')->nullable();
            $table->string('recovery_token')->nullable();
            $table->timestamp('recovered_at')->nullable();
            $table->uuid('converted_to_order_id')->nullable();
            
            // Metadata
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamp('abandoned_at');
            $table->timestamps();
            
            $table->foreign('store_id')->references('id')->on('stores')->onDelete('cascade');
            $table->index(['store_id', 'abandoned_at'], 'carts_store_abandoned');
            $table->index(['email', 'recovery_email_sent'], 'carts_email_recovery');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('abandoned_carts');
        Schema::dropIfExists('order_notes');
        Schema::dropIfExists('shipment_items');
        Schema::dropIfExists('order_shipments');
        Schema::dropIfExists('order_refunds');
        Schema::dropIfExists('order_payments');
        Schema::dropIfExists('order_status_history');
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
    }
};
