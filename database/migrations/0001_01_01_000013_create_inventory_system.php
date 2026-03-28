<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Creates inventory management and stock movement tracking tables.
     */
    public function up(): void
    {
        // Product stock movements (inventory history)
        Schema::create('product_stock_movements', function (Blueprint $table) {
            $table->id();
            $table->uuid('product_id');
            $table->unsignedBigInteger('product_variant_id')->nullable();
            $table->unsignedBigInteger('store_id');
            
            // Movement details
            $table->string('type', 20); // purchase, sale, return, adjustment, transfer, damage, theft, restock
            $table->integer('quantity'); // Positive for increase, negative for decrease
            $table->integer('quantity_before');
            $table->integer('quantity_after');
            
            // Reference
            $table->uuid('order_id')->nullable();
            $table->string('reference_type')->nullable(); // Order, Purchase, Transfer, Adjustment
            $table->string('reference_id')->nullable();
            $table->string('reference_number')->nullable();
            
            // Cost tracking
            $table->decimal('unit_cost', 10, 2)->nullable();
            $table->decimal('total_cost', 10, 2)->nullable();
            $table->string('currency', 3)->nullable();
            
            // Location
            $table->unsignedBigInteger('warehouse_id')->nullable();
            $table->string('location')->nullable(); // Shelf/bin location
            
            // Metadata
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('movement_date');
            $table->timestamps();
            
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('product_variant_id')->references('id')->on('product_variants')->onDelete('cascade');
            $table->foreign('store_id')->references('id')->on('stores')->onDelete('cascade');
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('set null');
            $table->index(['product_id', 'movement_date'], 'psm_product_date');
            $table->index(['store_id', 'type', 'movement_date'], 'psm_store_type_date');
            $table->index(['type', 'movement_date'], 'psm_type_date');
        });

        // Warehouses/Storage locations
        Schema::create('warehouses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('store_id');
            
            $table->string('name');
            $table->string('code')->unique();
            $table->text('description')->nullable();
            
            // Address
            $table->string('address')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('country', 2)->nullable();
            
            // Contact
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('manager_name')->nullable();
            
            // Configuration
            $table->boolean('is_primary')->default(false);
            $table->boolean('is_active')->default(true);
            $table->integer('priority')->default(0); // For fulfillment priority
            
            $table->timestamps();
            $table->softDeletes();
            
            $table->foreign('store_id')->references('id')->on('stores')->onDelete('cascade');
            $table->index(['store_id', 'is_active'], 'w_store_active');
        });

        // Warehouse stock levels
        Schema::create('warehouse_stock', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('warehouse_id');
            $table->uuid('product_id');
            $table->unsignedBigInteger('product_variant_id')->nullable();
            
            $table->integer('quantity')->default(0);
            $table->integer('reserved_quantity')->default(0); // Reserved for orders
            $table->integer('available_quantity')->default(0); // quantity - reserved_quantity
            
            $table->string('location')->nullable(); // Shelf/bin location
            $table->integer('reorder_point')->nullable();
            $table->integer('reorder_quantity')->nullable();
            
            $table->timestamp('last_counted_at')->nullable();
            $table->timestamps();
            
            $table->foreign('warehouse_id')->references('id')->on('warehouses')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('product_variant_id')->references('id')->on('product_variants')->onDelete('cascade');
            $table->unique(['warehouse_id', 'product_id', 'product_variant_id'], 'ws_warehouse_product_variant_unique');
            $table->index(['warehouse_id', 'available_quantity'], 'ws_warehouse_available');
        });

        // Stock adjustments (manual corrections)
        Schema::create('stock_adjustments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('store_id');
            $table->unsignedBigInteger('warehouse_id')->nullable();
            
            $table->string('adjustment_number')->unique();
            $table->string('reason', 50); // inventory_count, damage, theft, correction, return, other
            $table->text('notes')->nullable();
            
            $table->string('status', 20)->default('pending'); // pending, approved, rejected
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('approved_at')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            $table->foreign('store_id')->references('id')->on('stores')->onDelete('cascade');
            $table->foreign('warehouse_id')->references('id')->on('warehouses')->onDelete('cascade');
            $table->index(['store_id', 'status'], 'sa_store_status');
        });

        // Stock adjustment items
        Schema::create('stock_adjustment_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('adjustment_id');
            $table->uuid('product_id');
            $table->unsignedBigInteger('product_variant_id')->nullable();
            
            $table->integer('quantity_before');
            $table->integer('quantity_after');
            $table->integer('quantity_adjusted'); // Can be positive or negative
            
            $table->decimal('unit_cost', 10, 2)->nullable();
            $table->decimal('total_cost', 10, 2)->nullable();
            
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->foreign('adjustment_id')->references('id')->on('stock_adjustments')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('product_variant_id')->references('id')->on('product_variants')->onDelete('cascade');
            $table->index(['adjustment_id', 'product_id'], 'sai_adjustment_product');
        });

        // Stock transfers between warehouses
        Schema::create('stock_transfers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('store_id');
            
            $table->string('transfer_number')->unique();
            $table->unsignedBigInteger('from_warehouse_id');
            $table->unsignedBigInteger('to_warehouse_id');
            
            $table->string('status', 20)->default('pending'); // pending, in_transit, completed, cancelled
            $table->text('notes')->nullable();
            
            $table->foreignId('requested_by')->constrained('users')->onDelete('cascade');
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('received_by')->nullable()->constrained('users')->onDelete('set null');
            
            $table->timestamp('requested_at');
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('shipped_at')->nullable();
            $table->timestamp('received_at')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            $table->foreign('store_id')->references('id')->on('stores')->onDelete('cascade');
            $table->foreign('from_warehouse_id')->references('id')->on('warehouses')->onDelete('cascade');
            $table->foreign('to_warehouse_id')->references('id')->on('warehouses')->onDelete('cascade');
            $table->index(['store_id', 'status'], 'st_store_status');
        });

        // Stock transfer items
        Schema::create('stock_transfer_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('transfer_id');
            $table->uuid('product_id');
            $table->unsignedBigInteger('product_variant_id')->nullable();
            
            $table->integer('quantity_requested');
            $table->integer('quantity_shipped')->default(0);
            $table->integer('quantity_received')->default(0);
            
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->foreign('transfer_id')->references('id')->on('stock_transfers')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('product_variant_id')->references('id')->on('product_variants')->onDelete('cascade');
            $table->index(['transfer_id', 'product_id'], 'sti_transfer_product');
        });

        // Stock alerts/notifications
        Schema::create('stock_alerts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('store_id');
            $table->uuid('product_id');
            $table->unsignedBigInteger('product_variant_id')->nullable();
            $table->unsignedBigInteger('warehouse_id')->nullable();
            
            $table->string('alert_type', 20); // low_stock, out_of_stock, overstock, reorder_point
            $table->integer('current_quantity');
            $table->integer('threshold_quantity')->nullable();
            
            $table->boolean('is_resolved')->default(false);
            $table->timestamp('resolved_at')->nullable();
            $table->foreignId('resolved_by')->nullable()->constrained('users')->onDelete('set null');
            
            $table->timestamps();
            
            $table->foreign('store_id')->references('id')->on('stores')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('product_variant_id')->references('id')->on('product_variants')->onDelete('cascade');
            $table->foreign('warehouse_id')->references('id')->on('warehouses')->onDelete('cascade');
            $table->index(['store_id', 'alert_type', 'is_resolved'], 'sa_store_type_resolved');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_alerts');
        Schema::dropIfExists('stock_transfer_items');
        Schema::dropIfExists('stock_transfers');
        Schema::dropIfExists('stock_adjustment_items');
        Schema::dropIfExists('stock_adjustments');
        Schema::dropIfExists('warehouse_stock');
        Schema::dropIfExists('warehouses');
        Schema::dropIfExists('product_stock_movements');
    }
};
