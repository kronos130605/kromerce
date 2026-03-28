<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Creates email and SMS communication system tables.
     */
    public function up(): void
    {
        // Email templates
        Schema::create('email_templates', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('store_id')->nullable(); // Null for global templates
            
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('category', 50); // order, customer, marketing, system
            $table->text('description')->nullable();
            
            // Email content
            $table->string('subject');
            $table->text('preheader')->nullable();
            $table->longText('html_content');
            $table->longText('text_content')->nullable();
            
            // Configuration
            $table->string('from_name')->nullable();
            $table->string('from_email')->nullable();
            $table->string('reply_to')->nullable();
            $table->json('cc')->nullable();
            $table->json('bcc')->nullable();
            
            // Variables/placeholders
            $table->json('available_variables')->nullable(); // {{order_number}}, {{customer_name}}, etc.
            
            // Attachments
            $table->json('attachments')->nullable();
            
            // Status
            $table->boolean('is_active')->default(true);
            $table->boolean('is_system')->default(false); // System templates can't be deleted
            
            // Localization
            $table->string('language', 5)->default('es');
            
            // Metadata
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();
            
            $table->foreign('store_id')->references('id')->on('stores')->onDelete('cascade');
            $table->index(['store_id', 'category', 'is_active'], 'et_store_category_active');
            $table->index(['slug', 'language'], 'et_slug_language');
        });

        // Email logs
        Schema::create('email_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('store_id')->nullable();
            $table->unsignedBigInteger('template_id')->nullable();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            
            // Recipient
            $table->string('to_email');
            $table->string('to_name')->nullable();
            $table->json('cc')->nullable();
            $table->json('bcc')->nullable();
            
            // Email details
            $table->string('from_email');
            $table->string('from_name')->nullable();
            $table->string('reply_to')->nullable();
            $table->string('subject');
            $table->longText('html_content')->nullable();
            $table->longText('text_content')->nullable();
            
            // Attachments
            $table->json('attachments')->nullable();
            
            // Status
            $table->string('status', 20)->default('pending'); // pending, sent, failed, bounced, opened, clicked
            $table->text('error_message')->nullable();
            $table->integer('attempts')->default(0);
            
            // Tracking
            $table->string('message_id')->nullable(); // Provider message ID
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamp('opened_at')->nullable();
            $table->timestamp('clicked_at')->nullable();
            $table->timestamp('bounced_at')->nullable();
            $table->timestamp('complained_at')->nullable();
            
            // Analytics
            $table->integer('open_count')->default(0);
            $table->integer('click_count')->default(0);
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent')->nullable();
            
            // Reference
            $table->string('reference_type')->nullable(); // Order, User, etc.
            $table->string('reference_id')->nullable();
            
            $table->timestamps();
            
            $table->foreign('store_id')->references('id')->on('stores')->onDelete('cascade');
            $table->foreign('template_id')->references('id')->on('email_templates')->onDelete('set null');
            $table->index(['store_id', 'status', 'sent_at'], 'el_store_status_sent');
            $table->index(['to_email', 'sent_at'], 'el_email_sent');
            $table->index(['user_id', 'sent_at'], 'el_user_sent');
            $table->index('message_id');
        });

        // SMS logs
        Schema::create('sms_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('store_id')->nullable();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            
            // Recipient
            $table->string('to_phone');
            $table->string('from_phone')->nullable();
            
            // Message
            $table->text('message');
            $table->integer('message_length');
            $table->integer('segments')->default(1); // SMS segments
            
            // Status
            $table->string('status', 20)->default('pending'); // pending, sent, delivered, failed, undelivered
            $table->text('error_message')->nullable();
            $table->integer('attempts')->default(0);
            
            // Provider
            $table->string('provider', 20)->nullable(); // twilio, nexmo, etc.
            $table->string('message_id')->nullable(); // Provider message ID
            $table->decimal('cost', 8, 4)->nullable();
            $table->string('currency', 3)->nullable();
            
            // Tracking
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamp('failed_at')->nullable();
            
            // Reference
            $table->string('reference_type')->nullable(); // Order, User, etc.
            $table->string('reference_id')->nullable();
            
            $table->timestamps();
            
            $table->foreign('store_id')->references('id')->on('stores')->onDelete('cascade');
            $table->index(['store_id', 'status', 'sent_at'], 'sl_store_status_sent');
            $table->index(['to_phone', 'sent_at'], 'sl_phone_sent');
            $table->index(['user_id', 'sent_at'], 'sl_user_sent');
        });

        // Email campaigns (for marketing)
        Schema::create('email_campaigns', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('store_id');
            $table->unsignedBigInteger('template_id')->nullable();
            
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('subject');
            
            // Recipients
            $table->string('recipient_type', 20); // all, segment, custom
            $table->json('recipient_filters')->nullable(); // Customer group, purchase history, etc.
            $table->json('recipient_list')->nullable(); // Custom email list
            
            // Content
            $table->longText('html_content');
            $table->longText('text_content')->nullable();
            
            // Scheduling
            $table->string('status', 20)->default('draft'); // draft, scheduled, sending, sent, paused, cancelled
            $table->timestamp('scheduled_at')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            
            // Statistics
            $table->integer('total_recipients')->default(0);
            $table->integer('sent_count')->default(0);
            $table->integer('delivered_count')->default(0);
            $table->integer('opened_count')->default(0);
            $table->integer('clicked_count')->default(0);
            $table->integer('bounced_count')->default(0);
            $table->integer('unsubscribed_count')->default(0);
            
            // Metadata
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
            
            $table->foreign('store_id')->references('id')->on('stores')->onDelete('cascade');
            $table->foreign('template_id')->references('id')->on('email_templates')->onDelete('set null');
            $table->index(['store_id', 'status'], 'ec_store_status');
        });

        // Newsletter subscriptions
        Schema::create('newsletter_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('store_id');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            
            $table->string('email')->index();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            
            $table->string('status', 20)->default('subscribed'); // subscribed, unsubscribed, bounced, complained
            $table->string('source', 20)->default('website'); // website, checkout, import, api
            
            // Preferences
            $table->json('interests')->nullable(); // Topics of interest
            $table->string('frequency', 20)->default('weekly'); // daily, weekly, monthly
            
            // Tracking
            $table->timestamp('subscribed_at');
            $table->timestamp('unsubscribed_at')->nullable();
            $table->string('unsubscribe_reason')->nullable();
            $table->string('ip_address', 45)->nullable();
            
            $table->timestamps();
            
            $table->foreign('store_id')->references('id')->on('stores')->onDelete('cascade');
            $table->unique(['store_id', 'email'], 'ns_store_email_unique');
            $table->index(['store_id', 'status'], 'ns_store_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('newsletter_subscriptions');
        Schema::dropIfExists('email_campaigns');
        Schema::dropIfExists('sms_logs');
        Schema::dropIfExists('email_logs');
        Schema::dropIfExists('email_templates');
    }
};
