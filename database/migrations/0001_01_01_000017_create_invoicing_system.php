<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Creates invoicing and credit memo system tables.
     */
    public function up(): void
    {
        // Order invoices
        Schema::create('order_invoices', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('order_id');
            $table->unsignedBigInteger('store_id');
            
            // Invoice identification
            $table->string('invoice_number')->unique();
            $table->string('invoice_type', 20)->default('standard'); // standard, proforma, commercial
            
            // Financial details
            $table->string('currency', 3);
            $table->decimal('subtotal', 10, 2);
            $table->decimal('tax_amount', 10, 2);
            $table->decimal('shipping_amount', 10, 2);
            $table->decimal('discount_amount', 10, 2);
            $table->decimal('total', 10, 2);
            $table->decimal('amount_paid', 10, 2)->default(0);
            $table->decimal('amount_due', 10, 2);
            
            // Billing information (snapshot)
            $table->string('billing_name');
            $table->string('billing_company')->nullable();
            $table->string('billing_tax_id')->nullable();
            $table->string('billing_email');
            $table->string('billing_phone')->nullable();
            $table->string('billing_address');
            $table->string('billing_city');
            $table->string('billing_state');
            $table->string('billing_postal_code')->nullable();
            $table->string('billing_country', 2);
            
            // Status
            $table->string('status', 20)->default('draft'); // draft, sent, paid, partially_paid, overdue, cancelled, void
            
            // Dates
            $table->date('invoice_date');
            $table->date('due_date')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('voided_at')->nullable();
            
            // Notes
            $table->text('notes')->nullable();
            $table->text('terms')->nullable();
            $table->text('footer')->nullable();
            
            // Files
            $table->string('pdf_path')->nullable();
            
            // Metadata
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();
            
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
            $table->foreign('store_id')->references('id')->on('stores')->onDelete('cascade');
            $table->index(['store_id', 'status'], 'oi_store_status');
            $table->index(['order_id', 'invoice_type'], 'oi_order_type');
            $table->index('invoice_date');
            $table->index('due_date');
        });

        // Invoice items
        Schema::create('invoice_items', function (Blueprint $table) {
            $table->id();
            $table->uuid('invoice_id');
            $table->uuid('order_item_id')->nullable();
            
            $table->string('description');
            $table->string('sku')->nullable();
            $table->integer('quantity');
            $table->decimal('unit_price', 10, 2);
            $table->decimal('subtotal', 10, 2);
            $table->decimal('tax_amount', 10, 2)->default(0);
            $table->decimal('discount_amount', 10, 2)->default(0);
            $table->decimal('total', 10, 2);
            
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            
            $table->foreign('invoice_id')->references('id')->on('order_invoices')->onDelete('cascade');
            $table->foreign('order_item_id')->references('id')->on('order_items')->onDelete('set null');
            $table->index(['invoice_id', 'sort_order'], 'ii_invoice_order');
        });

        // Credit memos (refund documents)
        Schema::create('order_credit_memos', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('order_id');
            $table->uuid('invoice_id')->nullable();
            $table->unsignedBigInteger('store_id');
            
            // Credit memo identification
            $table->string('credit_memo_number')->unique();
            
            // Financial details
            $table->string('currency', 3);
            $table->decimal('subtotal', 10, 2);
            $table->decimal('tax_amount', 10, 2);
            $table->decimal('shipping_amount', 10, 2);
            $table->decimal('adjustment_amount', 10, 2)->default(0); // Manual adjustment
            $table->decimal('total', 10, 2);
            
            // Refund details
            $table->string('refund_method', 20); // original_payment, store_credit, bank_transfer
            $table->boolean('restock_items')->default(false);
            
            // Status
            $table->string('status', 20)->default('pending'); // pending, approved, refunded, cancelled
            
            // Dates
            $table->date('credit_memo_date');
            $table->timestamp('refunded_at')->nullable();
            
            // Notes
            $table->text('reason');
            $table->text('notes')->nullable();
            
            // Files
            $table->string('pdf_path')->nullable();
            
            // Metadata
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
            $table->foreign('invoice_id')->references('id')->on('order_invoices')->onDelete('set null');
            $table->foreign('store_id')->references('id')->on('stores')->onDelete('cascade');
            $table->index(['store_id', 'status'], 'ocm_store_status');
            $table->index('order_id');
            $table->index('credit_memo_date');
        });

        // Credit memo items
        Schema::create('credit_memo_items', function (Blueprint $table) {
            $table->id();
            $table->uuid('credit_memo_id');
            $table->uuid('order_item_id')->nullable();
            
            $table->string('description');
            $table->string('sku')->nullable();
            $table->integer('quantity');
            $table->decimal('unit_price', 10, 2);
            $table->decimal('subtotal', 10, 2);
            $table->decimal('tax_amount', 10, 2)->default(0);
            $table->decimal('total', 10, 2);
            
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            
            $table->foreign('credit_memo_id')->references('id')->on('order_credit_memos')->onDelete('cascade');
            $table->foreign('order_item_id')->references('id')->on('order_items')->onDelete('set null');
            $table->index(['credit_memo_id', 'sort_order'], 'cmi_credit_memo_order');
        });

        // Invoice payments tracking
        Schema::create('invoice_payments', function (Blueprint $table) {
            $table->id();
            $table->uuid('invoice_id');
            $table->uuid('order_payment_id')->nullable();
            
            $table->decimal('amount', 10, 2);
            $table->string('currency', 3);
            $table->string('payment_method');
            $table->string('transaction_id')->nullable();
            $table->text('notes')->nullable();
            
            $table->timestamp('payment_date');
            $table->foreignId('recorded_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            
            $table->foreign('invoice_id')->references('id')->on('order_invoices')->onDelete('cascade');
            $table->foreign('order_payment_id')->references('id')->on('order_payments')->onDelete('set null');
            $table->index(['invoice_id', 'payment_date'], 'ip_invoice_date');
        });

        // Invoice reminders/notifications
        Schema::create('invoice_reminders', function (Blueprint $table) {
            $table->id();
            $table->uuid('invoice_id');
            
            $table->string('reminder_type', 20); // due_soon, overdue, final_notice
            $table->integer('days_overdue')->nullable();
            
            $table->timestamp('sent_at');
            $table->string('sent_to_email');
            $table->boolean('was_opened')->default(false);
            $table->timestamp('opened_at')->nullable();
            
            $table->timestamps();
            
            $table->foreign('invoice_id')->references('id')->on('order_invoices')->onDelete('cascade');
            $table->index(['invoice_id', 'reminder_type'], 'ir_invoice_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice_reminders');
        Schema::dropIfExists('invoice_payments');
        Schema::dropIfExists('credit_memo_items');
        Schema::dropIfExists('order_credit_memos');
        Schema::dropIfExists('invoice_items');
        Schema::dropIfExists('order_invoices');
    }
};
