<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Creates customer-related tables: addresses, groups, wishlists.
     */
    public function up(): void
    {
        // Customer groups (wholesale, retail, VIP, etc.)
        Schema::create('customer_groups', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('store_id');
            
            $table->string('name');
            $table->string('slug');
            $table->text('description')->nullable();
            
            // Discount configuration
            $table->decimal('discount_percentage', 5, 2)->default(0);
            $table->boolean('show_prices_with_tax')->default(true);
            
            // Permissions
            $table->boolean('can_view_wholesale_prices')->default(false);
            $table->boolean('requires_approval')->default(false);
            
            // Status
            $table->string('status', 20)->default('active');
            $table->integer('sort_order')->default(0);
            
            $table->timestamps();
            $table->softDeletes();
            
            $table->foreign('store_id')->references('id')->on('stores')->onDelete('cascade');
            $table->unique(['store_id', 'slug'], 'cg_store_slug_unique');
            $table->index(['store_id', 'status'], 'cg_store_status');
        });

        // Customer group assignments
        Schema::create('customer_group_user', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_group_id');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->unsignedBigInteger('store_id');
            
            $table->timestamp('assigned_at')->nullable();
            $table->foreignId('assigned_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            
            $table->foreign('customer_group_id')->references('id')->on('customer_groups')->onDelete('cascade');
            $table->foreign('store_id')->references('id')->on('stores')->onDelete('cascade');
            $table->unique(['customer_group_id', 'user_id', 'store_id'], 'cgu_group_user_store_unique');
        });

        // Customer addresses
        Schema::create('customer_addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // Address type
            $table->string('type', 20)->default('shipping'); // shipping, billing, both
            $table->string('label')->nullable(); // 'Home', 'Work', 'Office'
            
            // Contact information
            $table->string('first_name');
            $table->string('last_name');
            $table->string('company')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            
            // Address details
            $table->string('address_1');
            $table->string('address_2')->nullable();
            $table->string('city');
            $table->string('state');
            $table->string('postal_code')->nullable();
            $table->string('country', 2); // ISO 3166-1 alpha-2
            
            // Geolocation
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            
            // Preferences
            $table->boolean('is_default_shipping')->default(false);
            $table->boolean('is_default_billing')->default(false);
            $table->text('delivery_instructions')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['user_id', 'type'], 'ca_user_type');
            $table->index(['user_id', 'is_default_shipping'], 'ca_user_default_shipping');
            $table->index(['user_id', 'is_default_billing'], 'ca_user_default_billing');
        });

        // Wishlists
        Schema::create('wishlists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->unsignedBigInteger('store_id')->nullable(); // Null for global wishlist
            
            $table->string('name')->default('My Wishlist');
            $table->text('description')->nullable();
            $table->string('visibility', 20)->default('private'); // private, public, shared
            $table->string('share_token')->unique()->nullable(); // For sharing
            
            $table->boolean('is_default')->default(false);
            $table->timestamps();
            
            $table->foreign('store_id')->references('id')->on('stores')->onDelete('cascade');
            $table->index(['user_id', 'store_id'], 'wishlists_user_store');
            $table->index('share_token');
        });

        // Wishlist items
        Schema::create('wishlist_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('wishlist_id');
            $table->uuid('product_id');
            $table->unsignedBigInteger('product_variant_id')->nullable();
            
            $table->integer('quantity')->default(1);
            $table->integer('priority')->default(0); // 0=normal, 1=high, 2=urgent
            $table->text('notes')->nullable();
            
            $table->timestamp('added_at');
            $table->timestamps();
            
            $table->foreign('wishlist_id')->references('id')->on('wishlists')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('product_variant_id')->references('id')->on('product_variants')->onDelete('cascade');
            $table->unique(['wishlist_id', 'product_id', 'product_variant_id'], 'wi_wishlist_product_variant_unique');
            $table->index(['wishlist_id', 'added_at'], 'wi_wishlist_added');
        });

        // Product comparisons
        Schema::create('product_comparisons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('session_id')->nullable(); // For guest users
            $table->unsignedBigInteger('store_id');
            $table->uuid('product_id');
            
            $table->timestamp('added_at');
            $table->timestamps();
            
            $table->foreign('store_id')->references('id')->on('stores')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->index(['user_id', 'store_id'], 'pc_user_store');
            $table->index(['session_id', 'store_id'], 'pc_session_store');
        });

        // Customer loyalty points
        Schema::create('customer_loyalty_points', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->unsignedBigInteger('store_id');
            
            $table->integer('points_balance')->default(0);
            $table->integer('points_earned_lifetime')->default(0);
            $table->integer('points_spent_lifetime')->default(0);
            
            $table->string('tier', 20)->default('bronze'); // bronze, silver, gold, platinum
            $table->timestamp('tier_expires_at')->nullable();
            
            $table->timestamps();
            
            $table->foreign('store_id')->references('id')->on('stores')->onDelete('cascade');
            $table->unique(['user_id', 'store_id'], 'clp_user_store_unique');
            $table->index(['store_id', 'tier'], 'clp_store_tier');
        });

        // Loyalty points transactions
        Schema::create('loyalty_points_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->unsignedBigInteger('store_id');
            $table->unsignedBigInteger('order_id')->nullable();
            
            $table->string('type', 20); // earned, spent, expired, adjusted, refunded
            $table->integer('points');
            $table->integer('balance_after');
            $table->text('description')->nullable();
            
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('expires_at')->nullable();
            $table->timestamp('transacted_at');
            $table->timestamps();
            
            $table->foreign('store_id')->references('id')->on('stores')->onDelete('cascade');
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('set null');
            $table->index(['user_id', 'store_id', 'transacted_at'], 'lpt_user_store_date');
            $table->index(['type', 'expires_at'], 'lpt_type_expires');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loyalty_points_transactions');
        Schema::dropIfExists('customer_loyalty_points');
        Schema::dropIfExists('product_comparisons');
        Schema::dropIfExists('wishlist_items');
        Schema::dropIfExists('wishlists');
        Schema::dropIfExists('customer_addresses');
        Schema::dropIfExists('customer_group_user');
        Schema::dropIfExists('customer_groups');
    }
};
