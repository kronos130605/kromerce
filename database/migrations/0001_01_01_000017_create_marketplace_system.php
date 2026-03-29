<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Creates complete marketplace system: commissions, payouts, disputes, ratings, analytics, moderation.
     */
    public function up(): void
    {
        // ============================================
        // COMMISSION & PAYOUT SYSTEM
        // ============================================
        
        // Commission tiers
        Schema::create('commission_tiers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->uuid('category_id')->nullable();
            $table->decimal('percentage', 5, 2);
            $table->decimal('fixed_amount', 10, 2)->default(0);
            $table->decimal('min_order_value', 10, 2)->nullable();
            $table->decimal('max_order_value', 10, 2)->nullable();
            $table->integer('priority')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->foreign('category_id')->references('id')->on('product_categories')->onDelete('set null');
            $table->index(['is_active', 'priority']);
        });

        // Store commission configs
        Schema::create('store_commission_configs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('store_id');
            $table->unsignedBigInteger('tier_id')->nullable();
            $table->decimal('custom_percentage', 5, 2)->nullable();
            $table->decimal('custom_fixed_amount', 10, 2)->nullable();
            $table->date('effective_from');
            $table->date('effective_until')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->foreign('store_id')->references('id')->on('stores')->onDelete('cascade');
            $table->foreign('tier_id')->references('id')->on('commission_tiers')->onDelete('set null');
            $table->index(['store_id', 'effective_from']);
        });

        // Platform fees
        Schema::create('platform_fees', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id');
            $table->unsignedBigInteger('store_id');
            $table->unsignedBigInteger('tier_id')->nullable();
            $table->decimal('order_subtotal', 10, 2);
            $table->decimal('commission_percentage', 5, 2);
            $table->decimal('commission_amount', 10, 2);
            $table->decimal('fixed_fee', 10, 2)->default(0);
            $table->decimal('total_fee', 10, 2);
            $table->string('currency', 3);
            $table->string('status', 20)->default('pending');
            $table->timestamp('collected_at')->nullable();
            $table->timestamp('refunded_at')->nullable();
            $table->json('calculation_details')->nullable();
            $table->timestamps();
            
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
            $table->foreign('store_id')->references('id')->on('stores')->onDelete('cascade');
            $table->foreign('tier_id')->references('id')->on('commission_tiers')->onDelete('set null');
            $table->index(['store_id', 'status', 'created_at']);
            $table->index(['order_id']);
        });

        // Store balances
        Schema::create('store_balances', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('store_id')->unique();
            $table->decimal('available_balance', 12, 2)->default(0);
            $table->decimal('pending_balance', 12, 2)->default(0);
            $table->decimal('total_earned', 12, 2)->default(0);
            $table->decimal('total_withdrawn', 12, 2)->default(0);
            $table->decimal('total_fees_paid', 12, 2)->default(0);
            $table->string('currency', 3)->default('USD');
            $table->timestamp('last_payout_at')->nullable();
            $table->timestamps();
            
            $table->foreign('store_id')->references('id')->on('stores')->onDelete('cascade');
        });

        // Balance transactions
        Schema::create('balance_transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('store_id');
            $table->string('type', 30);
            $table->decimal('amount', 12, 2);
            $table->string('currency', 3);
            $table->decimal('balance_after', 12, 2);
            $table->unsignedBigInteger('order_id')->nullable();
            $table->unsignedBigInteger('payout_id')->nullable();
            $table->unsignedBigInteger('fee_id')->nullable();
            $table->text('description');
            $table->json('metadata')->nullable();
            $table->timestamps();
            
            $table->foreign('store_id')->references('id')->on('stores')->onDelete('cascade');
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('set null');
            $table->index(['store_id', 'type', 'created_at']);
        });

        // Store payouts
        Schema::create('store_payouts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('store_id');
            $table->string('payout_number')->unique();
            $table->decimal('amount', 12, 2);
            $table->string('currency', 3);
            $table->string('status', 20)->default('pending');
            $table->string('method', 30);
            $table->json('payment_details');
            $table->foreignId('requested_by')->constrained('users')->onDelete('cascade');
            $table->timestamp('requested_at');
            $table->foreignId('processed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('processed_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->string('transaction_id')->nullable();
            $table->text('notes')->nullable();
            $table->text('failure_reason')->nullable();
            $table->timestamps();
            
            $table->foreign('store_id')->references('id')->on('stores')->onDelete('cascade');
            $table->index(['store_id', 'status', 'requested_at']);
        });

        // Commission reports
        Schema::create('commission_reports', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('store_id');
            $table->integer('year');
            $table->integer('month');
            $table->decimal('total_sales', 12, 2)->default(0);
            $table->decimal('total_commission', 12, 2)->default(0);
            $table->decimal('total_fees', 12, 2)->default(0);
            $table->decimal('net_earnings', 12, 2)->default(0);
            $table->integer('total_orders')->default(0);
            $table->string('currency', 3);
            $table->timestamp('generated_at');
            $table->timestamps();
            
            $table->foreign('store_id')->references('id')->on('stores')->onDelete('cascade');
            $table->unique(['store_id', 'year', 'month']);
        });

        // ============================================
        // MODERATION SYSTEM
        // ============================================

        // Product moderation history
        Schema::create('product_moderation_history', function (Blueprint $table) {
            $table->id();
            $table->uuid('product_id');
            $table->string('from_status', 20)->nullable();
            $table->string('to_status', 20);
            $table->foreignId('moderated_by')->constrained('users')->onDelete('cascade');
            $table->text('reason')->nullable();
            $table->text('notes')->nullable();
            $table->json('changes_requested')->nullable();
            $table->timestamps();
            
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->index(['product_id', 'created_at']);
        });

        // Content flags
        Schema::create('content_flags', function (Blueprint $table) {
            $table->id();
            $table->string('flaggable_type');
            $table->string('flaggable_id');
            $table->foreignId('flagged_by')->constrained('users')->onDelete('cascade');
            $table->string('reason', 50);
            $table->text('description')->nullable();
            $table->string('status', 20)->default('pending');
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('reviewed_at')->nullable();
            $table->text('resolution_notes')->nullable();
            $table->timestamps();
            
            $table->index(['flaggable_type', 'flaggable_id', 'status']);
            $table->index(['status', 'created_at']);
        });

        // Moderation rules
        Schema::create('moderation_rules', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('type', 30);
            $table->json('conditions');
            $table->string('action', 20);
            $table->integer('severity')->default(1);
            $table->boolean('is_active')->default(true);
            $table->integer('trigger_count')->default(0);
            $table->timestamps();
        });

        // Moderation queue
        Schema::create('moderation_queue', function (Blueprint $table) {
            $table->id();
            $table->string('item_type');
            $table->string('item_id');
            $table->unsignedBigInteger('store_id')->nullable();
            $table->string('priority', 20)->default('normal');
            $table->string('reason', 100);
            $table->json('metadata')->nullable();
            $table->foreignId('assigned_to')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('assigned_at')->nullable();
            $table->string('status', 20)->default('pending');
            $table->timestamps();
            
            $table->foreign('store_id')->references('id')->on('stores')->onDelete('cascade');
            $table->index(['status', 'priority', 'created_at']);
            $table->index(['assigned_to', 'status']);
        });

        // ============================================
        // STORE REPUTATION & ANALYTICS
        // ============================================

        // Store ratings
        Schema::create('store_ratings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('store_id');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->unsignedBigInteger('order_id')->nullable();
            $table->integer('overall_rating');
            $table->integer('product_quality_rating')->nullable();
            $table->integer('shipping_speed_rating')->nullable();
            $table->integer('customer_service_rating')->nullable();
            $table->integer('communication_rating')->nullable();
            $table->string('title')->nullable();
            $table->text('comment')->nullable();
            $table->boolean('verified_purchase')->default(false);
            $table->string('status', 20)->default('pending');
            $table->foreignId('moderated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('moderated_at')->nullable();
            $table->integer('helpful_count')->default(0);
            $table->integer('not_helpful_count')->default(0);
            $table->timestamps();
            $table->softDeletes();
            
            $table->foreign('store_id')->references('id')->on('stores')->onDelete('cascade');
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('set null');
            $table->index(['store_id', 'status', 'created_at']);
            $table->index(['user_id', 'store_id']);
        });

        // Store performance metrics
        Schema::create('store_performance_metrics', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('store_id')->unique();
            $table->decimal('average_rating', 3, 2)->default(0);
            $table->integer('total_ratings')->default(0);
            $table->integer('five_star_count')->default(0);
            $table->integer('four_star_count')->default(0);
            $table->integer('three_star_count')->default(0);
            $table->integer('two_star_count')->default(0);
            $table->integer('one_star_count')->default(0);
            $table->integer('total_orders')->default(0);
            $table->integer('completed_orders')->default(0);
            $table->integer('cancelled_orders')->default(0);
            $table->decimal('total_revenue', 12, 2)->default(0);
            $table->decimal('average_order_value', 10, 2)->default(0);
            $table->decimal('order_fulfillment_rate', 5, 2)->default(0);
            $table->decimal('on_time_delivery_rate', 5, 2)->default(0);
            $table->decimal('response_rate', 5, 2)->default(0);
            $table->decimal('average_response_time', 8, 2)->default(0);
            $table->integer('total_customers')->default(0);
            $table->integer('repeat_customers')->default(0);
            $table->decimal('customer_retention_rate', 5, 2)->default(0);
            $table->integer('total_products')->default(0);
            $table->integer('active_products')->default(0);
            $table->integer('out_of_stock_products')->default(0);
            $table->integer('total_disputes')->default(0);
            $table->integer('resolved_disputes')->default(0);
            $table->decimal('dispute_resolution_rate', 5, 2)->default(0);
            $table->timestamp('last_calculated_at')->nullable();
            $table->timestamps();
            
            $table->foreign('store_id')->references('id')->on('stores')->onDelete('cascade');
        });

        // Store badges
        Schema::create('store_badges', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description');
            $table->string('icon')->nullable();
            $table->string('color', 7)->nullable();
            $table->string('type', 30);
            $table->json('requirements');
            $table->integer('priority')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Store badge assignments
        Schema::create('store_badge_assignments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('store_id');
            $table->unsignedBigInteger('badge_id');
            $table->timestamp('earned_at');
            $table->timestamp('expires_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->foreign('store_id')->references('id')->on('stores')->onDelete('cascade');
            $table->foreign('badge_id')->references('id')->on('store_badges')->onDelete('cascade');
            $table->unique(['store_id', 'badge_id']);
        });

        // Product views
        Schema::create('product_views', function (Blueprint $table) {
            $table->id();
            $table->uuid('product_id');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('session_id', 100);
            $table->string('ip_address', 45);
            $table->string('user_agent')->nullable();
            $table->string('referrer')->nullable();
            $table->timestamp('viewed_at');
            
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->index(['product_id', 'viewed_at']);
            $table->index(['session_id', 'product_id']);
        });

        // Store analytics
        Schema::create('store_analytics', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('store_id');
            $table->date('date');
            $table->integer('page_views')->default(0);
            $table->integer('unique_visitors')->default(0);
            $table->integer('product_views')->default(0);
            $table->integer('orders')->default(0);
            $table->decimal('revenue', 12, 2)->default(0);
            $table->decimal('average_order_value', 10, 2)->default(0);
            $table->decimal('conversion_rate', 5, 2)->default(0);
            $table->integer('add_to_cart')->default(0);
            $table->integer('abandoned_carts')->default(0);
            $table->integer('new_customers')->default(0);
            $table->integer('returning_customers')->default(0);
            $table->timestamps();
            
            $table->foreign('store_id')->references('id')->on('stores')->onDelete('cascade');
            $table->unique(['store_id', 'date']);
            $table->index(['store_id', 'date']);
        });

        // Search analytics
        Schema::create('search_analytics', function (Blueprint $table) {
            $table->id();
            $table->string('query');
            $table->unsignedBigInteger('store_id')->nullable();
            $table->integer('results_count')->default(0);
            $table->boolean('has_results')->default(true);
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('session_id', 100);
            $table->uuid('clicked_product_id')->nullable();
            $table->timestamp('searched_at');
            
            $table->foreign('store_id')->references('id')->on('stores')->onDelete('cascade');
            $table->foreign('clicked_product_id')->references('id')->on('products')->onDelete('set null');
            $table->index(['query', 'searched_at']);
            $table->index(['store_id', 'searched_at']);
        });

        // ============================================
        // DISPUTES SYSTEM
        // ============================================

        // Order disputes
        Schema::create('order_disputes', function (Blueprint $table) {
            $table->id();
            $table->string('dispute_number')->unique();
            $table->unsignedBigInteger('order_id');
            $table->unsignedBigInteger('store_id');
            $table->foreignId('customer_id')->constrained('users')->onDelete('cascade');
            $table->string('type', 30);
            $table->string('subject');
            $table->text('description');
            $table->json('evidence')->nullable();
            $table->string('status', 20)->default('open');
            $table->string('priority', 20)->default('normal');
            $table->string('resolution_type', 30)->nullable();
            $table->decimal('refund_amount', 10, 2)->nullable();
            $table->text('resolution_notes')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->foreignId('assigned_to')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('assigned_at')->nullable();
            $table->boolean('is_escalated')->default(false);
            $table->timestamp('escalated_at')->nullable();
            $table->text('escalation_reason')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
            $table->foreign('store_id')->references('id')->on('stores')->onDelete('cascade');
            $table->index(['store_id', 'status', 'created_at']);
            $table->index(['customer_id', 'status']);
        });

        // Dispute messages
        Schema::create('dispute_messages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('dispute_id');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('sender_type', 20);
            $table->text('message');
            $table->json('attachments')->nullable();
            $table->boolean('is_internal')->default(false);
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
            
            $table->foreign('dispute_id')->references('id')->on('order_disputes')->onDelete('cascade');
            $table->index(['dispute_id', 'created_at']);
        });

        // Dispute resolutions
        Schema::create('dispute_resolutions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('dispute_id');
            $table->string('resolution_type', 30);
            $table->decimal('amount', 10, 2)->nullable();
            $table->text('description');
            $table->foreignId('resolved_by')->constrained('users')->onDelete('cascade');
            $table->timestamp('resolved_at');
            $table->boolean('customer_accepted')->nullable();
            $table->boolean('seller_accepted')->nullable();
            $table->timestamp('customer_accepted_at')->nullable();
            $table->timestamp('seller_accepted_at')->nullable();
            $table->timestamps();
            
            $table->foreign('dispute_id')->references('id')->on('order_disputes')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dispute_resolutions');
        Schema::dropIfExists('dispute_messages');
        Schema::dropIfExists('order_disputes');
        Schema::dropIfExists('search_analytics');
        Schema::dropIfExists('store_analytics');
        Schema::dropIfExists('product_views');
        Schema::dropIfExists('store_badge_assignments');
        Schema::dropIfExists('store_badges');
        Schema::dropIfExists('store_performance_metrics');
        Schema::dropIfExists('store_ratings');
        Schema::dropIfExists('moderation_queue');
        Schema::dropIfExists('moderation_rules');
        Schema::dropIfExists('content_flags');
        Schema::dropIfExists('product_moderation_history');
        Schema::dropIfExists('commission_reports');
        Schema::dropIfExists('store_payouts');
        Schema::dropIfExists('balance_transactions');
        Schema::dropIfExists('store_balances');
        Schema::dropIfExists('platform_fees');
        Schema::dropIfExists('store_commission_configs');
        Schema::dropIfExists('commission_tiers');
    }
};
